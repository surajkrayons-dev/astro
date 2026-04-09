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
        Schema::create('horoscopes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('zodiac_id');

            $table->enum('type', ['today', 'yesterday', 'tomorrow', 'daily', 'weekly', 'monthly', 'yearly']);

            $table->string('title')->nullable();
            $table->longText('overview')->nullable();

            $table->longText('career')->nullable();
            $table->json('career_date')->nullable();
            $table->longText('finance')->nullable();
            $table->json('finance_date')->nullable();
            $table->longText('love')->nullable();
            $table->json('love_date')->nullable();
            $table->longText('health')->nullable();
            $table->json('health_date')->nullable();
            $table->longText('family')->nullable();
            $table->json('family_date')->nullable();
            $table->longText('students')->nullable();
            $table->json('students_date')->nullable();
            $table->longText('warning')->nullable();

            $table->json('lucky_numbers')->nullable();
            $table->json('lucky_colors')->nullable(); 

            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('zodiac_id')
                ->references('id')
                ->on('zodiac_signs')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('modified_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horoscopes');
    }
};