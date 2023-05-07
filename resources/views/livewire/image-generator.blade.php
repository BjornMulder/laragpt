<div>
    <h1>Image Generator</h1>
    <input type="text" wire:model="prompt" placeholder="Enter a prompt" />
    <button wire:click="generateImage">Generate Image</button>

    @if($imageUrl)
        <div>
            @if(filter_var($imageUrl, FILTER_VALIDATE_URL))
                <img src="{{ $imageUrl }}" alt="Generated Image" />
            @else
                <p>{{ $imageUrl }}</p>
            @endif
        </div>
    @endif
</div>
