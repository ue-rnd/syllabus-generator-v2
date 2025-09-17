@extends('components.layouts.app.main-layout')
@section('body')
	@include('components.dashboard.topbar')
	@include('components.dashboard.sidebar')
	<main class="ml-16 my-10">
		@yield('content')
		@yield('scripts')
	</main>
@endsection