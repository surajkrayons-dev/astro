<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatSession extends Model
{
    use HasFactory;
    
    protected $table = 'ai_chat_sessions';

    protected $fillable = [
        'user_id',
        'topic_id',
        'free_messages_used',
        'paid_messages',
        'total_amount',
        'started_at',
        'last_message_at',
        'closed_at',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function topic()
    {
        return $this->belongsTo(AiChatTopic::class, 'topic_id');
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class, 'session_id');
    }

    public function transactions()
    {
        return $this->hasMany(AiChatTransaction::class, 'session_id');
    }
}