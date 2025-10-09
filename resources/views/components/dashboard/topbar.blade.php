<div class="sticky top-0 z-70 backdrop-blur-md p-4 ml-16 bg-accent-foreground/90 flex justify-between items-center border-b border-accent-ghost-dark">
    <div class="flex items-center space-x-4">
        @php($college = auth()->user()?->college)
        @if($college?->logo_url)
            <img src="{{ $college->logo_url }}" alt="{{ $college->name }} Logo" class="size-10 object-contain">
        @else
            <div class="size-10 rounded bg-accent-foreground border flex items-center justify-center text-accent-ghost-dark border-accent-ghost-dark">
                <span class="text-xs">N/A</span>
            </div>
        @endif
        <h4 class="text-accent-error font-semibold text-lg">{{ $college->name ?? 'Your College' }}</h4>
    </div>
    <a class="flex items-center space-x-4" href="{{ route("profile")}}">
        @php($user = auth()->user())
        @if(method_exists($user, 'initials'))
            <div class="size-10 rounded-full bg-accent-main text-white flex items-center justify-center font-semibold">
                <span>{{ $user->initials() }}</span>
            </div>
        @else
            <div class="size-10 rounded-full bg-accent-ghost-dark"></div>
        @endif
        <span class="text-accent-desc">Hello, <span class="font-medium">{{ $user?->name }}</span></span>
    </a>
</div>