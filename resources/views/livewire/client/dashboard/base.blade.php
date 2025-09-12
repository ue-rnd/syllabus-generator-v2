<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>UE | Syllabus Generator</title>
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
