@extends('components.layouts.app.main-layout')
@section('body')
    <div class="max-w-lg mx-auto my-24 space-y-4">
        <div class="flex items-center space-x-4">
            <img src="http://placehold.co/75x75" alt="UE Logo" class="">
            <div class="block">
                <h4 class="font-medium text-red-800 text-xl">University of the East</h4>
                <p>Syllabus Generator</p>
            </div>
        </div>
        <div class="w-full bg-white p-6 shadow-sm border space-y-6 rounded">
            <div>
                <h4 class="text-2xl font-medium">Forgot Password</h4>
                <p class="text-gray-600">Please enter your email address to reset your password.</p>
            </div>
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="text-gray-600 font-medium">Email</label>
                    <input type="email" name="email" id="email" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                </div>
                <div>
                    <button class="w-full bg-red-700 text-white py-2 rounded ease duration-200 hover:bg-red-800">Send Password Reset Link</button>
                </div>
            </form>
        </div>
        @if (session('error'))
            <div class="text-red-600 font-medium">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="p-3 rounded bg-green-800 text-white">
                {{ session('success') }}
            </div>
        @endif
    </div>
@endsection