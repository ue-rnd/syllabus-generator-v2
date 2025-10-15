<div class="sticky top-0 z-70 backdrop-blur-md p-4 ml-16 bg-accent-foreground/90 flex justify-between items-center border-b border-accent-ghost-dark"
    x-data="{darkMode: false}"
    x-init="darkMode = (localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')"
    x-init="darkMode = (localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')">
    <div class="flex items-center space-x-4">
        @php($college = auth()->user()?->college)
        @if($college?->logo_url)
            <img src="{{ $college->logo_url }}" alt="{{ $college->name }} Logo" class="size-10 object-contain">
        @else
            <div
                class="size-10 rounded bg-accent-foreground border flex items-center justify-center text-accent-ghost-dark border-accent-ghost-dark">
                <span class="text-xs">N/A</span>
            </div>
        @endif
        <h4 class="text-accent-error font-semibold text-lg">{{ $college->name ?? 'Your College' }}</h4>
    </div>
    <div class="flex items-center space-x-4">
        <button type="button" title="Toggle dark mode"
            @click="darkMode = !darkMode; if(darkMode) { document.documentElement.classList.add('dark'); localStorage.setItem('darkMode','true') } else { document.documentElement.classList.remove('dark'); localStorage.setItem('darkMode','false') }"
            class="px-3 py-1.5 rounded-xl border border-accent-ghost-dark text-sm text-accent-text hover:bg-accent-ghost-dark bg-accent-ghost">
            <span x-show="!darkMode">Light</span>
            <span x-show="darkMode">Dark</span>
        </button>
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
</div>