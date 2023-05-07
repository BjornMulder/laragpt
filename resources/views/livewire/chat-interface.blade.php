<div class="w-full h-full flex flex-col">
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
