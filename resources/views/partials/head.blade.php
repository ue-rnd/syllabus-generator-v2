<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

{{-- darkmode --}}
<script>
	(function() {
		try {
			var stored = localStorage.getItem('darkMode');
			var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
			var dark = (stored === 'true') || (stored === null && prefersDark);
			if (dark) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');
		} catch (e) {
			// ignore (add error)
		}
	})();

	// Re-apply theme after Livewire updates or SPA-like navigation so the .dark class persists
	(function() {
		function applyTheme() {
			try {
				var stored = localStorage.getItem('darkMode');
				var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
				var dark = (stored === 'true') || (stored === null && prefersDark);
				if (dark) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');
			} catch (e) { /* ignore */ }
		}
		document.addEventListener('DOMContentLoaded', applyTheme);
		document.addEventListener('livewire:load', applyTheme);
		document.addEventListener('livewire:message.processed', applyTheme);
		document.addEventListener('livewire:navigated', applyTheme);
	})();
</script>

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/images/logo_ue.png" sizes="any">
<link rel="icon" href="/images/logo_ue.png" type="image/png">
<link rel="apple-touch-icon" href="/images/logo_ue.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Quill Rich Text Editor CDN --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

{{-- Livewire scripts --}}
@livewireScripts

{{-- WireUI scripts must be loaded after Alpine.js --}}
@wireUiScripts
