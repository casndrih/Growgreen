<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'vendors', 'admins'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'remember_token')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('remember_token', 100)->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['users', 'vendors', 'admins'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'remember_token')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('remember_token');
                });
            }
        }
    }
};
