<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        @include('components.dashboard.topbar')
		@include('components.dashboard.sidebar')
		<main class="ml-16 my-10">
            @yield('content')
        </main>
    </body>
</html>