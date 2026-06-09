<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatTransaction extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_transactions';

    protected $fillable = [
        'user_id',
        'session_id',
        'message_id',
        'amount',
        'balance_before',
        'balance_after',
        'type',
        'remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'session_id');
    }

    public function message()
    {
        return $this->belongsTo(AiChatMessage::class, 'message_id');
    }   
}
