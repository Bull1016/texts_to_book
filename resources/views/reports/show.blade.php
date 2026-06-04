@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $report->title }}</h1>
                <p class="text-gray-600 mt-2">{{ $report->subject }}</p>
            </div>
            <div class="text-right">
                <span class="px-4 py-2 rounded text-white font-medium
                    @if($report->status === 'completed') bg-green-600
                    @elseif($report->status === 'generating') bg-yellow-600
                    @elseif($report->status === 'failed') bg-red-600
                    @else bg-gray-600
                    @endif">
                    {{ ucfirst($report->status) }}
                </span>
                @if($report->status === 'generating')
                    <p class="text-sm text-gray-600 mt-2">{{ $report->progress }}% Complete</p>
                @endif
            </div>
        </div>

        @if($report->status === 'failed')
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <h3 class="font-bold text-red-900 mb-2">Generation Failed</h3>
                <p class="text-red-800">{{ $report->error_message }}</p>
                <div class="mt-4 space-x-4">
                    <a href="{{ route('reports.destroy', $report) }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete Report</a>
                    <a href="{{ route('reports.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Try Again</a>
                </div>
            </div>
        @else
            <div class="flex space-x-4 mb-6">
                <a href="{{ route('reports.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">← Back</a>
                @if($report->status === 'completed')
                    <a href="{{ route('reports.download', $report) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">📥 Download PDF</a>
                @endif
                <form method="POST" action="{{ route('reports.destroy', $report) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </div>

            @if($report->status === 'completed' && $sections->count())
                <div class="space-y-8">
                    @foreach($sections as $index => $section)
                        <article class="bg-white rounded-lg shadow p-8">
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $section->title }}</h2>

                                @if($section->images->count())
                                    <div class="mb-6">
                                        @foreach($section->images as $image)
                                            <img src="{{ $image->image_url }}" alt="{{ $image->prompt }}" class="w-full rounded-lg shadow-md mb-4">
                                        @endforeach
                                    </div>
                                @endif

                                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                    {!! nl2br(e($section->content)) !!}
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <p class="text-blue-900 mb-4">Ready to share? Download the professional PDF version:</p>
                    <a href="{{ route('reports.download', $report) }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 text-lg font-medium">
                        📥 Download as PDF
                    </a>
                </div>
            @elseif($report->status === 'generating')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
                    <div class="mb-6">
                        <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-2">Generating Your Report</h3>
                    <p class="text-blue-800 mb-4">Our AI is working hard to create your report...</p>
                    <div class="w-full bg-blue-200 rounded-full h-4 mb-2">
                        <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $report->progress }}%"></div>
                    </div>
                    <p class="text-blue-700 font-medium">{{ $report->progress }}% Complete</p>
                    <p class="text-sm text-blue-600 mt-4">This page will refresh automatically...</p>
                </div>

                <script>
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                </script>
            @elseif($report->status === 'pending')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                    <p class="text-gray-800 text-lg">Preparing to generate your report...</p>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
