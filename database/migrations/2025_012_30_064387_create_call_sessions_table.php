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
        Schema::create('call_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('astrologer_id');
            $table->unsignedBigInteger('user_id');

            $table->string('user_number')->nullable();
            $table->string('astro_number')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('call_sid')->nullable();
            $table->integer('billable_seconds')->nullable();
            $table->boolean('is_deducted')->default(false);
            $table->integer('duration')->nullable();
            $table->decimal('amount', 10, 2)->default(0);

            $table->enum('status', ['pending', 'active', 'completed', 'missed'])->default('pending');
            $table->timestamps();

            $table->foreign('astrologer_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_sessions');
    }
};