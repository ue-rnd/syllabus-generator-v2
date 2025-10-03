<aside class="w-16 z-70 h-screen fixed top-0 left-0 flex flex-col items-center py-4 space-y-6 bg-white border-r">
	<a class="text-red-600 text-2xl font-bold" href="{{ route('home') }}">UE</a>
	<a href="{{ route('profile') }}" class="hover:bg-gray-200 p-2 rounded-xl ease duration-200 group relative">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
			<path
				d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
		</svg>
		<span
			class="border shadow pointer-events-none select-none absolute left-full top-1/2 -translate-y-1/2 ml-2 w-max bg-white text-black text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition font-bold whitespace-nowrap z-10">
			Profile
		</span>
	</a>
	<a href="{{ route('home') }}" class="hover:bg-gray-200 p-2 rounded-xl ease duration-200 group relative">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
			<path
				d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
			<path
				d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
		</svg>

		<span
			class="border shadow pointer-events-none select-none absolute left-full top-1/2 -translate-y-1/2 ml-2 w-max bg-white text-black text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition font-bold whitespace-nowrap z-10">
			Home
		</span>
	</a>
	<a href="{{ route('notifications') }}" class="hover:bg-gray-200 p-2 rounded-xl ease duration-200 group relative">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
			<path fill-rule="evenodd"
				d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z"
				clip-rule="evenodd" />
		</svg>

		<span
			class="border shadow pointer-events-none select-none absolute left-full top-1/2 -translate-y-1/2 ml-2 w-max bg-white text-black text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition font-bold whitespace-nowrap z-10">
			Notifications
		</span>
	</a>
	<a href="{{ route('bookmarks') }}" class="hover:bg-gray-200 p-2 rounded-xl ease duration-200 group relative">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
			<path fill-rule="evenodd"
				d="M10 2c-1.716 0-3.408.106-5.07.31C3.806 2.45 3 3.414 3 4.517V17.25a.75.75 0 0 0 1.075.676L10 15.082l5.925 2.844A.75.75 0 0 0 17 17.25V4.517c0-1.103-.806-2.068-1.93-2.207A41.403 41.403 0 0 0 10 2Z"
				clip-rule="evenodd" />
		</svg>
		<span
			class="border shadow pointer-events-none select-none absolute left-full top-1/2 -translate-y-1/2 ml-2 w-max bg-white text-black text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition font-bold whitespace-nowrap z-10">
			Bookmarks
		</span>
	</a>
	<a href="{{ route('logout')  }}" class="hover:bg-gray-200 p-2 rounded-xl ease duration-200 mt-auto group relative">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
			<path fill-rule="evenodd"
				d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z"
				clip-rule="evenodd" />
			<path fill-rule="evenodd"
				d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z"
				clip-rule="evenodd" />
		</svg>
		<span
			class="border shadow pointer-events-none select-none absolute left-full top-1/2 -translate-y-1/2 ml-2 w-max bg-white text-black text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition font-bold whitespace-nowrap z-10">
			Logout
		</span>
	</a>
</aside>