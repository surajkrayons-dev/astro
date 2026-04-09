<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horoscope extends Model
{
    use HasFactory;

    protected $table = 'horoscopes';

    protected $fillable = [
        'zodiac_id',
        'type',
        'title',
        'overview',
        'career',
        'career_date',
        'finance',
        'finance_date',
        'love',
        'love_date',
        'health',
        'health_date',
        'family',
        'family_date',
        'students',
        'students_date',
        'warning',
        'lucky_numbers',
        'lucky_colors',
        'status',
        'created_by',
        'modified_by'
    ];

    protected $casts = [
        'career_date' => 'array',
        'finance_date' => 'array',
        'love_date' => 'array',
        'health_date' => 'array',
        'family_date' => 'array',
        'students_date' => 'array',
        'lucky_numbers' => 'array',
        'lucky_colors' => 'array',
        'status' => 'boolean',
    ];

    public function zodiac()
    {
        return $this->belongsTo(ZodiacSign::class, 'zodiac_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}