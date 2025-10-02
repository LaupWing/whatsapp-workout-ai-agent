<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'whatsapp_message_id',
        'direction',
        'message_type',
        'message_content',
        'media_url',
        'media_mime_type',
        'status',
        'whatsapp_metadata',
        'sent_at',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'whatsapp_metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aiInteraction()
    {
        return $this->hasOne(AiInteraction::class);
    }
}
