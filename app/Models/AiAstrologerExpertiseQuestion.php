<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiAstrologerExpertiseQuestion extends Model
{
    use HasFactory;
    
    protected $table = 'ai_astrologer_expertise_questions';

    protected $fillable = [
        'expertise_id',
        'question'
    ];

    // protected $casts = [
    //     'is_free' => 'boolean',
    //     'charged_amount' => 'decimal:2',
    //     'tokens_used' => 'integer',
    // ];

    public function expertise()
    {
        return $this->belongsTo(AiAstrologerExpertise::class, 'expertise_id');
    }
}