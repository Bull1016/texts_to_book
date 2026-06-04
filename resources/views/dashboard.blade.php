@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Welcome, {{ auth()->user()->name }}! 👋</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Quick Stats</h3>
                <p class="text-3xl font-bold text-blue-600">{{ auth()->user()->reports()->count() }}</p>
                <p class="text-gray-600">Reports Created</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Recently Completed</h3>
                <p class="text-3xl font-bold text-green-600">{{ auth()->user()->reports()->where('status', 'completed')->count() }}</p>
                <p class="text-gray-600">Finished Reports</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Get Started</h3>
                <a href="{{ route('reports.create') }}" class="block w-full bg-blue-600 text-white text-center py-2 rounded hover:bg-blue-700">
                    Create New Report
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Recent Reports</h2>

            @if(auth()->user()->reports()->exists())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b">
                            <tr>
                                <th class="text-left py-2 px-4">Title</th>
                                <th class="text-left py-2 px-4">Status</th>
                                <th class="text-left py-2 px-4">Created</th>
                                <th class="text-right py-2 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(auth()->user()->reports()->latest()->limit(5)->get() as $report)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4">{{ $report->title }}</td>
                                    <td class="py-2 px-4">
                                        <span class="px-3 py-1 rounded text-sm
                                            @if($report->status === 'completed') bg-green-100 text-green-800
                                            @elseif($report->status === 'generating') bg-yellow-100 text-yellow-800
                                            @elseif($report->status === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4">{{ $report->created_at->format('M d, Y') }}</td>
                                    <td class="py-2 px-4 text-right">
                                        <a href="{{ route('reports.show', $report) }}" class="text-blue-600 hover:text-blue-700 mr-4">View</a>
                                        @if($report->status === 'completed')
                                            <a href="{{ route('reports.download', $report) }}" class="text-green-600 hover:text-green-700 mr-4">Download PDF</a>
                                        @endif
                                        <form method="POST" action="{{ route('reports.destroy', $report) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600 text-center py-8">No reports yet. <a href="{{ route('reports.create') }}" class="text-blue-600 hover:text-blue-700">Create one now!</a></p>
            @endif
        </div>
    </div>
</div>
@endsection
