<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGptResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_gpt_prompt_id',
        'response',
        'response_id',
        'object',
        'created',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'finish_reason',
    ];

    public function prompt()
    {
        return $this->belongsTo(ChatGptPrompt::class, 'chat_gpt_prompt_id');
    }
}
