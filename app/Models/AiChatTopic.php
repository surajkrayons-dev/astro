<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatTopic extends Model
{
    use HasFactory;
    
    protected $table = 'ai_chat_topics';

    protected $fillable = [
        'name',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function sessions()
    {
        return $this->hasMany(AiChatSession::class, 'topic_id');
    }
}