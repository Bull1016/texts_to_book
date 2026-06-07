@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Reports') }}</h1>
            <a href="{{ route('reports.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 shadow-sm transition-all duration-150">
                <i class="fa-solid fa-plus"></i>{{ __('New Report') }}
            </a>
        </div>

        <livewire:reports-list />
    </div>
</div>
@endsection
