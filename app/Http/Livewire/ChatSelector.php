<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use Livewire\Component;

class ChatSelector extends Component
{
    public $chats;

    public function mount()
    {
        $this->loadChats();
    }

    public function loadChats()
    {
        $this->chats = Chat::all();
    }

    public function selectChat($chatId)
    {
        $this->emitUp('updateChat', $chatId);
    }

    public function render()
    {
        return view('livewire.chat-selector');
    }
}
