<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('store_wallets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('total_added', 12, 2)->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->decimal('total_refunded', 12, 2)->default(0);
            $table->decimal('last_recharge_amount', 12, 2)->default(0);
            $table->timestamp('last_recharge_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_wallets');
    }
};
