<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conversation_id',
        'agent_name',
        'user_input',
        'agent_response',
        'parsed_intent',
        'context_data',
        'model_used',
        'tokens_used',
        'response_time_ms',
        'was_successful',
        'error_message',
        'tool_calls',
    ];

    protected $casts = [
        'parsed_intent' => 'array',
        'context_data' => 'array',
        'tool_calls' => 'array',
        'was_successful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
