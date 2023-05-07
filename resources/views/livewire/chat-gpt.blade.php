<div>
    <h1 class="text-2xl font-bold">ChatGPT</h1>

    <div class="my-4">
        <label for="inputText" class="block text-sm font-medium text-gray-700">Your message:</label>
        <textarea id="inputText" wire:model="inputText" class="form-input mt-1 block w-full"></textarea>
    </div>

    <div>
        <button wire:click="generateResponse" class="btn btn-primary">Generate Response</button>

        <!-- Loading indicator -->
        <div wire:loading wire:target="generateResponse" class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>

        <!-- GPT-3.5-turbo response -->
        <div class="mt-3">
            <h5>GPT-4 Response:</h5>
            <div class="border p-2">
                <textarea id="responseText4" wire:model="responseText4" readonly class="form-input mt-1 block w-full"></textarea>
            </div>
        </div>

        <!-- Davinci-Codex response -->
        <div class="mt-3">
            <h5>GPT-3.5-turbo Response:</h5>
            <div class="border p-2">
                <textarea id="responseText35" wire:model="responseText35" readonly class="form-input mt-1 block w-full"></textarea>
            </div>
        </div>
    </div>
</div>
