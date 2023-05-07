<div class="flex flex-row h-screen overflow-hidden">
    <div class="bg-gray-200 w-1/4 px-4 py-6 border-r" id="chatlist">
        <p class="mb-4 font-semibold text-lg">Chats</p>
        <ul class="divide-y divide-gray-400">
            @foreach($chatList as $chatTitle)
                <li>
                    <button type="button"
                            class="w-full px-4 py-2 text-lg text-left text-gray-800 hover:bg-gray-100 focus:bg-gray-300 focus:outline-none
                            @if($selectedChat->title === $chatTitle) bg-gray-100 @endif"
                            wire:click="selectChat('{{ $chatTitle }}')">
                        {{ $chatTitle }}
                    </button>
                </li>
            @endforeach
        </ul>
        <form class="mt-6" wire:submit.prevent="selectChat">
            <div class="flex space-x-2">
                <input type="text" class="w-full border rounded-md px-4 py-2" wire:model="newChatName" placeholder="Enter chat name">
                <button type="submit" class="px-4 py-2 rounded-md bg-green-500">Create</button>
            </div>
        </form>
    </div>
    <div class="flex-grow flex-1 p-6" id="chatinterface">
        <div class="overflow-y-auto h-full">
            <div class="overflow-y-auto flex-grow">
                <div class="p-4 space-y-4">
                    @foreach($messages as $message)
                        <div class="@if($message['role'] === 'user') text-right @endif">
                            <div class="inline-block p-2 rounded-md bg-gray-100 @if($message['role'] === 'user') bg-blue-500 text-red @else bg-white text-black @endif">
                                <x-markdown>{!! $message['content'] !!}</x-markdown>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-t mt-6 pt-4">
            <div class="flex-none p-4 border-t">
                <form class="flex space-x-2" wire:submit.prevent="sendMessage">
                    <input type="text" class="w-full border rounded-md px-4 py-2" placeholder="Type your message here" wire:model="inputText" wire:keydown.enter="sendMessage(); $event.target.value = ''">
                    <span id="charCount">{{ strlen($inputText) }}</span>/<span id="maxCharCount">4000</span>
                    <button type="submit" class="px-4 py-2 rounded-md bg-blue-500">Send</button>
                </form>
                <div wire:loading wire:target="generateResponse" class="spinner-border text-primary" role="status">
                    <p class="text-sm text-red">typing...</p>
                </div>
            </div>
        </div>
    </div>
</div>
