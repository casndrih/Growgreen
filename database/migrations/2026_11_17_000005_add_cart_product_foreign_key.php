<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('carts') || ! Schema::hasTable('food_products')) {
            return;
        }

        $foreignKeys = collect(Schema::getForeignKeys('carts'));
        $alreadyExists = $foreignKeys->contains(
            fn (array $foreignKey) => ($foreignKey['name'] ?? null) === 'carts_product_id_foreign'
        );

        if (! $alreadyExists) {
            Schema::table('carts', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('food_products')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('carts')) {
            return;
        }

        $foreignKeys = collect(Schema::getForeignKeys('carts'));
        $alreadyExists = $foreignKeys->contains(
            fn (array $foreignKey) => ($foreignKey['name'] ?? null) === 'carts_product_id_foreign'
        );

        if ($alreadyExists) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropForeign('carts_product_id_foreign');
            });
        }
    }
};
