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
                <h4 class="text-2xl font-medium">Reset Password</h4>
                <p class="text-gray-600">Please enter your new password to recover your account.</p>
            </div>
            <form action="{{ route('password.update', $token) }}" method="post" class="space-y-4">
                @csrf
                <div>
                    <label for="new_password" class="text-gray-600 font-medium">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    @error('new_password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                
                </div>
                <div>
                    <label for="confirm_password" class="text-gray-600 font-medium">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="w-full border px-2 py-1 rounded focus:outline-none focus:ring-1 focus:ring-red-400">
                    @error('confirm_password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="w-full bg-red-700 text-white py-2 rounded ease duration-200 hover:bg-red-800">Reset Password</button>
                </div>
            </form>
        </div>
        @if (session('success'))
            <div class="p-3 rounded bg-green-800 text-white">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 rounded bg-red-700 text-white">
                {{ session('error') }}
            </div>
        @endif
    </div>
@endsection