<?php

namespace App\Providers;

use App\Http\Livewire\ChatGPT;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireComponentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        Livewire::component('chat-gpt', ChatGPT::class);
    }
}
