<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ChatCreator extends Component
{
    public function createChat()
    {
        $this->emitUp('updateChat');
    }

    public function render()
    {
        return view('livewire.chat-creator');
    }
}
