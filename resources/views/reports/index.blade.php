@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Reports') }}</h1>
            <a href="{{ route('reports.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center shadow-sm">
                <i class="fa-solid fa-plus mr-1"></i>{{ __('New Report') }}
            </a>
        </div>

        <livewire:reports-list />
    </div>
</div>
@endsection
