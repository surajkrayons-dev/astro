<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallSession extends Model
{
    use HasFactory;

    protected $table = 'call_sessions';

    protected $fillable = [
        'astrologer_id',
        'user_id',
        'user_number',
        'astro_number',
        'started_at',
        'ended_at',
        'call_sid',
        'billable_seconds',
        'is_deducted',
        'duration',
        'amount',
        'status',
    ];

    public function astrologer()
    {
        return $this->belongsTo(User::class, 'astrologer_id')->withTrashed();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}