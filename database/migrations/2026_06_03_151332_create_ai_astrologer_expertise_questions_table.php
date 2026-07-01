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
        Schema::create('ai_astrologer_expertise_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expertise_id')
                ->constrained('ai_astrologer_expertises')
                ->cascadeOnDelete();
            $table->text('question');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_astrologer_expertise_questions');
    }
};
