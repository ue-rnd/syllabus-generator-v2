<div class="p-4 ml-16 bg-white flex justify-between items-center border-b">
    <div class="flex items-center space-x-4">
        @php($college = auth()->user()?->college)
        @if($college?->logo_url)
            <img src="{{ $college->logo_url }}" alt="{{ $college->name }} Logo" class="size-10 object-contain">
        @else
            <div class="size-10 rounded bg-gray-100 border flex items-center justify-center text-gray-500">
                <span class="text-xs">N/A</span>
            </div>
        @endif
        <h4 class="text-red-700 font-semibold text-lg">{{ $college->name ?? 'Your College' }}</h4>
    </div>
    <div class="flex items-center space-x-4">
        @php($user = auth()->user())
        @if(method_exists($user, 'initials'))
            <div class="size-10 rounded-full bg-red-600 text-white flex items-center justify-center font-semibold">
                <span>{{ $user->initials() }}</span>
            </div>
        @else
            <div class="size-10 rounded-full bg-gray-200"></div>
        @endif
        <span class="text-gray-600">Hello, <span class="font-medium">{{ $user?->name }}</span></span>
    </div>
</div>