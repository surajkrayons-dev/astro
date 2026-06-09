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
        Schema::create('ai_chat_transactions', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('message_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->enum('type', ['debit', 'credit'])->default('debit');
            $table->string('remark')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('session_id');
            $table->index('message_id');
            $table->index('type');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('session_id')
                ->references('id')
                ->on('ai_chat_sessions')
                ->cascadeOnDelete();

            $table->foreign('message_id')
                ->references('id')
                ->on('ai_chat_messages')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_transactions');
    }
};
