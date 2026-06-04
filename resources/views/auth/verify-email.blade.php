@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow p-8">
        <h2 class="text-xl font-bold text-center text-gray-900 mb-6">Verify Email</h2>

        <p class="text-gray-600 mb-6">Check your email for a verification link to confirm your email address.</p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 font-medium">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full text-center text-gray-600 hover:text-gray-700">
                Back to login
            </button>
        </form>
    </div>
</div>
@endsection
