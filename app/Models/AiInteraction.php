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
        'model_used',
        'tokens_used',
        'response_time_ms',
        'was_successful',
        'error_message',
        'tool_calls',
        'raw_events',
    ];

    protected $casts = [
        'tool_calls' => 'array',
        'raw_events' => 'array',
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

    /**
     * Get list of tools used in this interaction
     */
    public function getToolNamesAttribute(): array
    {
        if (!$this->tool_calls) {
            return [];
        }

        return array_column($this->tool_calls, 'name');
    }

    /**
     * Check if a specific tool was called
     */
    public function usedTool(string $toolName): bool
    {
        return in_array($toolName, $this->getToolNamesAttribute());
    }
}
