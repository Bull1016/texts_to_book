@extends('layouts.app')

@section('content')
<div class="text-center py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-4">📚 Texts to Book</h1>
    <p class="text-xl text-gray-600 mb-8">Transform your ideas into beautifully formatted professional books with AI</p>

    @if(auth()->check())
        <div class="space-y-4">
            <a href="{{ route('reports.create') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-blue-700">
                Create Your First Book
            </a>
            <br>
            <a href="{{ route('reports.index') }}" class="inline-block text-blue-600 hover:text-blue-700 text-lg">
                View Your Reports
            </a>
        </div>
    @else
        <div class="space-x-4">
            <a href="{{ route('login') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-blue-700">
                Login
            </a>
            <a href="{{ route('register') }}" class="inline-block bg-gray-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-gray-700">
                Register
            </a>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-8 mt-12">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold mb-2">🧠 Intelligent Outlines</h3>
            <p class="text-gray-600">AI-powered book structure generation</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold mb-2">✍️ Auto Content</h3>
            <p class="text-gray-600">Professional chapter content generation</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold mb-2">🖼️ Rich Images</h3>
            <p class="text-gray-600">Beautiful illustrations for each section</p>
        </div>
    </div>
</div>
@endsection
