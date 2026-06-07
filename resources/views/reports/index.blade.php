@extends('layouts.app')

@section('title', __('Reports') . ' - Texts to Book')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <h1 class="text-5xl font-black text-gray-900 tracking-tight mb-4">
                    {{ __('Your Library') }}
                </h1>
                <p class="text-xl text-gray-500 font-medium max-w-xl">
                    {{ __('Manage, view, and download all your AI-generated books in one place.') }}
                </p>
            </div>

            <a href="{{ route('reports.create') }}"
               class="inline-flex items-center gap-3 bg-blue-600 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-blue-100 hover:bg-blue-700 hover:-translate-y-1 transition-all group">
               <i class="fa-solid fa-plus text-sm group-hover:rotate-90 transition-transform"></i>
               {{ __('New Report') }}
            </a>
        </div>

        <!-- Livewire Component -->
        @livewire('reports-list')
    </div>
</div>
@endsection
