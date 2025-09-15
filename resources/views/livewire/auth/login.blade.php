@extends('components.layouts.app.main-layout')
@section('body')
    <div class="max-w-lg mx-auto my-20 space-y-4">
        <div class="flex items-center space-x-4">
            <img src="http://placehold.co/75x75" alt="UE Logo" class="">
            <div class="block">
                <h4 class="font-medium text-red-800 text-xl">University of the East</h4>
                <p>Syllabus Generator</p>
            </div>
        </div>
        <div class="w-full bg-white p-4 shadow-sm border space-y-6 rounded">
            <div>
                <h4 class="text-2xl font-medium">Login</h4>
                <p class="text-gray-600">Please enter your credentials to access your account.</p>
            </div>
            <form method="post" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="text-gray-600 font-medium">Email</label>
                    <input type="email" name="email" id="email" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                </div>
                <div>
                    <label for="password" class="text-gray-600 font-medium">Password</label>
                    <input type="password" name="password" id="password" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                </div>
                <div>
                    <button class="w-full bg-red-700 text-white py-2 rounded ease duration-200 hover:bg-red-800">Login</button>
                </div>
                <div class="text-center">
                    <a href="{{ route('forgot_password') }}" class="text-red-700">Forgot your password?</a>
                </div>
            </form>
        </div>
        @error('email')
            <div class="bg-red-700 w-full p-4 rounded text-white">
                {{ $message }}
            </div>
        @enderror
        @if (session('success'))
            <div class="bg-green-800 w-full p-4 rounded text-white">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-700 w-full p-4 rounded text-white">
                {{ session('error') }}
            </div>
        @endif
    </div>
@endsection