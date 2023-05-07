<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'id',
        'title',
        'session_id',
    ];

    public function chatGptPrompts()
    {
        return $this->hasMany(ChatGptPrompt::class);
    }
}
