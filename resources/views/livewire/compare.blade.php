<div>
    <div class="p-6">
        <div class="mb-4">
            <input wire:model="inputText" type="text" class="w-full border border-gray-300 px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:border-indigo-500" placeholder="Enter your prompt">
        </div>
        <div class="mb-4">
            <button wire:click="generateResponse" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none">Generate Responses</button>
        </div>
        <div wire:loading class="text-blue-500 font-bold mb-4">
            Loading responses...
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($responses as $response)
                <div class="border border-gray-200 p-4 rounded-lg">
                    <h2 class="text-lg font-bold mb-2">{{ $response['model'] }}</h2>
                    <p>{{ $response['response'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
