@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Texts to Book</h1>
        <h2 class="text-xl font-bold text-center text-gray-900 mb-6">Sign In</h2>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="mb-6 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 font-medium mb-4">
                Sign In
            </button>
        </form>

        <p class="text-center text-gray-600">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700">Register here</a>
        </p>

        <p class="text-center text-gray-600 mt-4">
            <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-700">Forgot your password?</a>
        </p>
    </div>
</div>
@endsection
