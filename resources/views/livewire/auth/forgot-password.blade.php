@extends('components.layouts.app.main-layout')
@section('body')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Forgot Password Form -->
            <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-gray-200">
                <!-- Header with Logo -->
                <div class="text-center mb-8">
                    <div class="flex justify-center items-center space-x-4 mb-6">
                        <img src="{{ asset('images/logo_ue.png') }}" alt="University of the East Logo" class="h-16 w-16 object-contain">
                        <div class="text-left">
                            <h1 class="text-2xl font-bold text-gray-900">University of the East</h1>
                            <p class="text-lg text-gray-600">Syllabus Generator</p>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Reset your password</h2>
                    <p class="mt-2 text-sm text-gray-600">Enter your email address and we'll send you a link to reset your password.</p>
                </div>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 shadow-sm hover:border-gray-300"
                               placeholder="Enter your email address"
                               required>
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-[1.02]">
                            Send Reset Link
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" 
                           class="text-sm font-medium text-red-600 hover:text-red-500 transition-colors duration-200">
                            ← Back to login
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('error'))
            <div class="max-w-md w-full mt-6">
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="max-w-md w-full mt-6">
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection