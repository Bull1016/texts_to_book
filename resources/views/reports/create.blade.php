@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Report</h1>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Report Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g., The Future of AI"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">A memorable title for your report</p>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Report Subject/Topic</label>
                    <textarea name="subject" id="subject" rows="6" required placeholder="Describe the topic you want to create a report about. Be as detailed as possible..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('subject') }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">The more detail you provide, the better the AI can generate content</p>
                </div>

                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Report Language</label>
                    <select name="language" id="language" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>French</option>
                        <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                        <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                        <option value="de" {{ old('language') == 'de' ? 'selected' : '' }}>German</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">The language in which the report will be generated</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">⚡ What happens next:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>✓ AI generates a structured outline</li>
                        <li>✓ Content is written for each section</li>
                        <li>✓ Images are automatically fetched</li>
                        <li>✓ A professional PDF is created</li>
                    </ul>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-medium text-lg">
                        Generate Report
                    </button>
                    <a href="{{ route('reports.index') }}" class="flex-1 text-center bg-gray-200 text-gray-800 py-3 rounded-lg hover:bg-gray-300 font-medium text-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">🧠 Smart Outlines</h3>
                <p class="text-gray-600">Our AI generates logical, well-structured outlines for your topic.</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">✍️ Professional Content</h3>
                <p class="text-gray-600">Each section is filled with engaging, well-researched content.</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">📖 Instant PDFs</h3>
                <p class="text-gray-600">Download your beautifully formatted report as a PDF.</p>
            </div>
        </div>
    </div>
</div>
@endsection
