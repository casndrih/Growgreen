<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\FoodProduct;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal;

class PayPalController extends Controller
{
    protected function paypal(): PayPal
    {
        $provider = new PayPal;
        $provider->setApiCredentials(config('paypal'));

        return $provider;
    }

    public function createPayment(Request $request)
    {
        $userId = $request->user()->id;
        $cartItems = Cart::with('product')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        foreach ($cartItems as $item) {
            if (! $item->product || (int) $item->quantity < 1 || (int) $item->quantity > (int) $item->product->stock) {
                return redirect()->route('cart.index')->with('error', 'One or more cart items are no longer available in the requested quantity.');
            }
        }

        $currency = strtoupper((string) config('paypal.currency', 'USD'));
        try {
            $payment = DB::transaction(function () use ($cartItems, $userId, $currency) {
            $totalCents = 0;
            $orders = [];

            foreach ($cartItems as $item) {
                $product = FoodProduct::query()->lockForUpdate()->findOrFail($item->product_id);

                if ((int) $item->quantity > (int) $product->stock) {
                    throw new \DomainException('A product no longer has enough stock.');
                }

                $unitCents = (int) round(((float) $product->price) * 100);
                $lineCents = $unitCents * (int) $item->quantity;
                $totalCents += $lineCents;

                $orders[] = Order::create([
                    'user_id' => $userId,
                    'vendor_id' => $product->vendor_id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item->quantity,
                    'price' => number_format($lineCents / 100, 2, '.', ''),
                    'status' => 'payment_pending',
                ]);
            }

            if ($totalCents < 1) {
                throw new \DomainException('The cart total must be greater than zero.');
            }

                return Payment::create([
                    'user_id' => $userId,
                    'reference' => (string) Str::uuid(),
                    'amount' => number_format($totalCents / 100, 2, '.', ''),
                    'currency' => $currency,
                    'status' => 'pending',
                    'order_ids' => collect($orders)->pluck('id')->all(),
                ]);
            });
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->route('cart.index')->with('error', 'Your cart changed while checkout was starting. Please review it and try again.');
        }

        try {
            $provider = $this->paypal();
            $provider->getAccessToken();

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $payment->reference,
                    'custom_id' => $payment->reference,
                    'invoice_id' => $payment->reference,
                    'amount' => [
                        'currency_code' => $payment->currency,
                        'value' => (string) $payment->amount,
                    ],
                ]],
                'application_context' => [
                    'return_url' => route('paypal.execute'),
                    'cancel_url' => route('paypal.cancel', ['reference' => $payment->reference]),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

            $approveLink = collect($response['links'] ?? [])->firstWhere('rel', 'approve');

            if (empty($response['id']) || empty($approveLink['href'])) {
                throw new \RuntimeException('PayPal did not return an approval URL.');
            }

            $payment->update([
                'paypal_order_id' => $response['id'],
                'response' => $response,
            ]);

            return redirect()->away($approveLink['href']);
        } catch (\Throwable $exception) {
            report($exception);
            $payment->update(['status' => 'failed', 'response' => ['error' => 'PayPal order creation failed.']]);
            Order::whereIn('id', $payment->order_ids)->where('user_id', $userId)->update(['status' => 'payment_failed']);

            return redirect()->route('cart.index')->with('error', 'Unable to start PayPal checkout. Please try again.');
        }
    }

    public function executePayment(Request $request)
    {
        $paypalOrderId = (string) $request->query('token', '');

        if ($paypalOrderId === '') {
            return redirect()->route('cart.index')->with('error', 'Missing PayPal order token.');
        }

        $payment = Payment::where('user_id', $request->user()->id)
            ->where('paypal_order_id', $paypalOrderId)
            ->first();

        if (! $payment) {
            return redirect()->route('cart.index')->with('error', 'This PayPal payment could not be found.');
        }

        if ($payment->status === 'paid') {
            return redirect()->route('orders.index')->with('success', 'This payment has already been confirmed.');
        }

        if ($payment->status !== 'pending') {
            return redirect()->route('cart.index')->with('error', 'This payment is no longer pending.');
        }

        try {
            $provider = $this->paypal();
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($paypalOrderId);

            $capture = data_get($response, 'purchase_units.0.payments.captures.0');
            $capturedValue = (string) data_get($capture, 'amount.value', '');
            $capturedCurrency = strtoupper((string) data_get($capture, 'amount.currency_code', ''));
            $isCompleted = data_get($response, 'status') === 'COMPLETED'
                && data_get($capture, 'status') === 'COMPLETED';
            $amountMatches = number_format((float) $capturedValue, 2, '.', '') === number_format((float) $payment->amount, 2, '.', '');
            $currencyMatches = $capturedCurrency === strtoupper($payment->currency);

            if (! $isCompleted || ! $amountMatches || ! $currencyMatches) {
                $payment->update([
                    'status' => 'review',
                    'response' => $response,
                ]);

                return redirect()->route('orders.index')->with('error', 'PayPal returned a payment that needs verification. Please contact support before retrying.');
            }

            DB::transaction(function () use ($payment, $response, $capture) {
                $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

                if ($lockedPayment->status === 'paid') {
                    return;
                }

                if ($lockedPayment->status !== 'pending') {
                    throw new \RuntimeException('Payment is no longer pending.');
                }

                $orders = Order::whereIn('id', $lockedPayment->order_ids)
                    ->where('user_id', $lockedPayment->user_id)
                    ->lockForUpdate()
                    ->get();

                if ($orders->count() !== count($lockedPayment->order_ids)) {
                    throw new \RuntimeException('The local order records could not be verified.');
                }

                $stockUpdates = [];
                foreach ($orders as $order) {
                    $product = FoodProduct::whereKey($order->product_id)->lockForUpdate()->first();

                    if (! $product || (int) $product->stock < (int) $order->quantity) {
                        $lockedPayment->update([
                            'status' => 'requires_refund',
                            'response' => $response,
                        ]);
                        Order::whereIn('id', $lockedPayment->order_ids)->update(['status' => 'payment_review']);
                        return;
                    }

                    $stockUpdates[] = ['product' => $product, 'quantity' => (int) $order->quantity];
                }

                foreach ($stockUpdates as $stockUpdate) {
                    $stockUpdate['product']->decrement('stock', $stockUpdate['quantity']);
                }

                Order::whereIn('id', $lockedPayment->order_ids)->update(['status' => 'paid']);

                $lockedPayment->update([
                    'status' => 'paid',
                    'paypal_capture_id' => data_get($capture, 'id'),
                    'response' => $response,
                    'paid_at' => now(),
                ]);

                Cart::where('user_id', $lockedPayment->user_id)
                    ->whereIn('product_id', $orders->pluck('product_id'))
                    ->delete();
            });

            $payment->refresh();

            if ($payment->status === 'requires_refund') {
                return redirect()->route('orders.index')->with('error', 'Payment was captured but stock could not be confirmed. Support must arrange a refund.');
            }

            return redirect()->route('orders.index')->with('success', 'Your payment was confirmed and your orders have been placed.');
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->route('orders.index')->with('error', 'We could not verify the PayPal capture. Your order has not been marked as paid; please contact support.');
        }
    }

    public function cancelPayment(Request $request, string $reference)
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->where('reference', $reference)
            ->firstOrFail();

        if ($payment->status === 'pending') {
            DB::transaction(function () use ($payment) {
                Payment::whereKey($payment->id)->where('status', 'pending')->update(['status' => 'cancelled']);
                Order::whereIn('id', $payment->order_ids)
                    ->where('user_id', $payment->user_id)
                    ->where('status', 'payment_pending')
                    ->update(['status' => 'payment_cancelled']);
            });
        }

        return redirect()->route('cart.index')->with('error', 'PayPal checkout was cancelled. Your cart has not been cleared.');
    }
}
