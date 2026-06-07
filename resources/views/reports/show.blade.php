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
                <span class="px-4 py-2 rounded-full text-white font-semibold text-sm
                    @if($report->status === 'completed') bg-green-600
                    @elseif($report->status === 'generating') bg-amber-500
                    @elseif($report->status === 'failed') bg-red-600
                    @else bg-gray-500
                    @endif">
                    {{ __(ucfirst($report->status)) }}
                </span>
                @if($report->status === 'generating')
                    <p class="text-sm text-gray-600 mt-2">{{ $report->progress }}% {{ __('Complete') }}</p>
                @endif
            </div>
        </div>

        @if($report->status === 'failed')
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                <h3 class="font-bold text-red-900 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ __('Generation Failed') }}
                </h3>
                <p class="text-red-800 mb-4">{{ $report->error_message }}</p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <form id="delete-failed-form" method="POST" action="{{ route('reports.destroy', $report) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            onclick="swalDelete(document.getElementById('delete-failed-form'), '{{ __('Delete this report') }} ?', '{{ addslashes(__('The report') . ' &quot;' . addslashes($report->title) . '&quot; ' . __('will be permanently deleted.')) }}')"
                            class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-150">
                            <i class="fa-solid fa-trash"></i> {{ __('Delete') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('reports.retry', $report) }}" style="display:inline;">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-150">
                            <i class="fa-solid fa-rotate-right"></i> {{ __('Try Again') }}
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="flex flex-wrap gap-3 mb-6">
                <a href="{{ route('reports.index') }}"
                   class="inline-flex items-center gap-2 bg-gray-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-150">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
                </a>
                @if($report->status === 'completed')
                    <a href="{{ route('reports.download', $report) }}"
                       class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-150">
                        <i class="fa-solid fa-download"></i> {{ __('Download PDF') }}
                    </a>
                @endif
                <form id="delete-form" method="POST" action="{{ route('reports.destroy', $report) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        onclick="swalDelete(document.getElementById('delete-form'), '{{ __('Delete this report') }} ?', '{{ addslashes(__('The report') . ' &quot;' . addslashes($report->title) . '&quot; ' . __('will be permanently deleted.')) }}')"
                        class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-150">
                        <i class="fa-solid fa-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>

            @if($report->status === 'completed' && $sections->count())
                <div class="space-y-8">
                    @foreach($sections as $index => $section)
                        <article class="bg-white rounded-xl shadow p-8">
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $index + 1 }}. {{ $section->title }}</h2>

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

                                @if($section->children->count())
                                    <div class="mt-8 space-y-8">
                                        @foreach($section->children as $subIndex => $sub)
                                            <div class="border-t border-gray-100 pt-6">
                                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $index + 1 }}.{{ $subIndex + 1 }} {{ $sub->title }}</h3>
                                                <div class="prose prose-md max-w-none text-gray-700 leading-relaxed">
                                                    {!! nl2br(e($sub->content)) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
                    <p class="text-blue-900 mb-4">{{ __('Ready to share? Download the professional PDF version:') }}</p>
                    <a href="{{ route('reports.download', $report) }}"
                       class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 text-lg font-semibold transition-all duration-150">
                        <i class="fa-solid fa-download"></i> {{ __('Download as PDF') }}
                    </a>
                </div>
            @elseif($report->status === 'generating')
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center">
                    <div class="mb-6">
                        <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-2">{{ __('Generating Your Report') }}</h3>
                    <p class="text-blue-800 mb-4">{{ __('Our AI is working hard to create your report...') }}</p>
                    <div class="w-full bg-blue-200 rounded-full h-4 mb-2">
                        <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $report->progress }}%"></div>
                    </div>
                    <p class="text-blue-700 font-medium">{{ $report->progress }}% {{ __('Complete') }}</p>
                    <p class="text-sm text-blue-600 mt-4">{{ __('This page will refresh automatically...') }}</p>
                </div>

                <script>
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                </script>
            @elseif($report->status === 'pending')
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center">
                    <p class="text-gray-800 text-lg">{{ __('Preparing to generate your report...') }}</p>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
