<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('requires_queue')->default(false)->after('wallet_discount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('queue_number')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('queue_number');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('requires_queue');
        });
    }
};
