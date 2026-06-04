@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Your Reports</h1>
            <a href="{{ route('reports.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + New Report
            </a>
        </div>

        @if($reports->count())
            <div class="grid grid-cols-1 gap-6">
                @foreach($reports as $report)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $report->title }}</h2>
                                <p class="text-gray-600 mb-4">{{ Str::limit($report->subject, 150) }}</p>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-500">Created {{ $report->created_at->diffForHumans() }}</span>
                                    <span class="px-3 py-1 rounded text-sm font-medium
                                        @if($report->status === 'completed') bg-green-100 text-green-800
                                        @elseif($report->status === 'generating') bg-yellow-100 text-yellow-800
                                        @elseif($report->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                    @if($report->status === 'generating')
                                        <span class="text-sm text-blue-600">{{ $report->progress }}% Complete</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.show', $report) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    View
                                </a>
                                @if($report->status === 'completed')
                                    <a href="{{ route('reports.download', $report) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                        Download
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('reports.destroy', $report) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-gray-600 text-lg mb-4">No reports yet.</p>
                <a href="{{ route('reports.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Create Your First Report
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
