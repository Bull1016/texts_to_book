<?php

use App\Models\Report;
use Livewire\Volt\Component;

new class extends Component {
    public Report $report;

    public function mount(Report $report)
    {
        $this->report = $report;
    }

    public function getListeners()
    {
        return [
            // We could use echo if we had pusher, but we'll use polling for now as per requirements of "not refreshing"
        ];
    }

    // This will be called by wire:poll
    public function refreshReport()
    {
        $this->report->refresh();

        if ($this->report->status === 'completed' || $this->report->status === 'failed') {
            // We can stop polling or just let it stay at 100%
        }
    }

    public function getSectionsProperty()
    {
        return $this->report->sections()
            ->whereNull('parent_id')
            ->with(['images', 'children.images'])
            ->get();
    }
}; ?>

<div wire:poll.3s="refreshReport">
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
                @endif shadow-sm transition-colors duration-500">
                {{ __(ucfirst($report->status)) }}
            </span>
        </div>
    </div>

    @if($report->status === 'failed')
        <div class="bg-red-50 border border-red-200 rounded-2xl p-8 mb-8 shadow-sm animate-fade-in">
            <div class="flex items-center gap-4 mb-4">
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fa-solid fa-circle-exclamation text-2xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-red-900">{{ __('Generation Failed') }}</h3>
            </div>
            <p class="text-red-800 mb-6 bg-white/50 p-4 rounded-lg border border-red-100">{{ $report->error_message }}</p>
            <div class="flex flex-wrap gap-3">
                <form id="delete-failed-form" method="POST" action="{{ route('reports.destroy', $report) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        onclick="swalDelete(document.getElementById('delete-failed-form'), '{{ __('Delete this report') }} ?', '{{ addslashes(__('The report') . ' &quot;' . addslashes($report->title) . '&quot; ' . __('will be permanently deleted.')) }}')"
                        class="inline-flex items-center gap-2 bg-white text-red-600 border border-red-200 px-5 py-2.5 rounded-xl font-semibold hover:bg-red-50 transition-all duration-200">
                        <i class="fa-solid fa-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('reports.retry', $report) }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-red-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-red-700 shadow-md shadow-red-200 transition-all duration-200">
                        <i class="fa-solid fa-rotate-right"></i> {{ __('Try Again') }}
                    </button>
                </form>
            </div>
        </div>
    @elseif($report->status === 'generating' || $report->status === 'pending')
        <div class="bg-white border border-gray-100 rounded-3xl p-10 text-center shadow-xl shadow-blue-50/50 mb-10 animate-pulse-slow">
            <div class="relative w-24 h-24 mx-auto mb-8">
                <div class="absolute inset-0 border-4 border-blue-50 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-t-blue-600 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-wand-magic-sparkles text-3xl text-blue-600"></i>
                </div>
            </div>

            <h3 class="text-2xl font-black text-gray-900 mb-3">{{ __('Generating Your Masterpiece') }}</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">{{ __('Our AI is weaving your ideas into a professional book. This might take a few moments.') }}</p>

            <div class="max-w-xl mx-auto">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-sm font-bold text-blue-600 uppercase tracking-wider">{{ $report->current_step ?: __('Initializing...') }}</span>
                    <span class="text-2xl font-black text-gray-900">{{ $report->progress }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden border border-gray-50 p-1">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $report->progress }}%"></div>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-4 text-left">
                    <div class="flex items-center gap-3 p-4 rounded-2xl {{ $report->progress >= 10 ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-400' }} transition-colors duration-500">
                        <i class="fa-solid {{ $report->progress >= 10 ? 'fa-circle-check' : 'fa-circle-notch fa-spin' }}"></i>
                        <span class="font-semibold">{{ __('Cover Image & Strategy') }}</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-2xl {{ $report->progress >= 20 ? 'bg-green-50 text-green-700' : ($report->progress >= 10 ? 'bg-blue-50 text-blue-700 animate-pulse' : 'bg-gray-50 text-gray-400') }} transition-colors duration-500">
                        <i class="fa-solid {{ $report->progress >= 20 ? 'fa-circle-check' : 'fa-circle-notch fa-spin' }}"></i>
                        <span class="font-semibold">{{ __('Outline & Structure') }}</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-2xl {{ $report->progress >= 95 ? 'bg-green-50 text-green-700' : ($report->progress >= 20 ? 'bg-blue-50 text-blue-700 animate-pulse' : 'bg-gray-50 text-gray-400') }} transition-colors duration-500">
                        <i class="fa-solid {{ $report->progress >= 95 ? 'fa-circle-check' : 'fa-circle-notch fa-spin' }}"></i>
                        <span class="font-semibold">{{ __('Content Generation & Illustrations') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($report->status === 'completed')
        <div class="flex flex-wrap gap-3 mb-8">
            <a href="{{ route('reports.index') }}"
               class="inline-flex items-center gap-2 bg-white text-gray-700 border border-gray-200 px-5 py-2.5 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-200">
                <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
            </a>
            <a href="{{ route('reports.download', $report) }}"
               class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-green-700 shadow-md shadow-green-200 transition-all duration-200">
                <i class="fa-solid fa-download"></i> {{ __('Download PDF') }}
            </a>
            <form id="delete-form" method="POST" action="{{ route('reports.destroy', $report) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="button"
                    onclick="swalDelete(document.getElementById('delete-form'), '{{ __('Delete this report') }} ?', '{{ addslashes(__('The report') . ' &quot;' . addslashes($report->title) . '&quot; ' . __('will be permanently deleted.')) }}')"
                    class="inline-flex items-center gap-2 bg-white text-red-600 border border-red-100 px-5 py-2.5 rounded-xl font-semibold hover:bg-red-50 transition-all duration-200">
                    <i class="fa-solid fa-trash"></i> {{ __('Delete') }}
                </button>
            </form>
        </div>

        <div class="space-y-12">
            @if($report->cover_image_url)
                <div class="rounded-3xl overflow-hidden shadow-2xl mb-12 border-4 border-white">
                    <img src="{{ $report->cover_image_url }}" alt="Cover" class="w-full h-[400px] object-cover">
                </div>
            @endif

            @foreach($this->sections as $index => $section)
                <article class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10 md:p-16 transition-all hover:shadow-md">
                    <div class="mb-8">
                        <div class="inline-block px-4 py-1 bg-blue-50 text-blue-600 rounded-full text-sm font-bold mb-4">
                            {{ __('Chapter') }} {{ $index + 1 }}
                        </div>
                        <h2 class="text-4xl font-black text-gray-900 mb-8">{{ $section->title }}</h2>

                        @if($section->images->count())
                            <div class="mb-10">
                                @foreach($section->images as $image)
                                    <figure class="group">
                                        <img src="{{ $image->image_url }}" alt="{{ $image->prompt }}" class="w-full rounded-2xl shadow-lg mb-2 group-hover:scale-[1.01] transition-transform duration-500">
                                        <figcaption class="text-xs text-gray-400 italic text-center">{{ $image->prompt }}</figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        @endif

                        <div class="prose prose-xl max-w-none text-gray-800 leading-relaxed font-serif">
                            {!! nl2br(e($section->content)) !!}
                        </div>

                        @if($section->children->count())
                            <div class="mt-16 space-y-16">
                                @foreach($section->children as $subIndex => $sub)
                                    <div class="border-t border-gray-50 pt-12">
                                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                                            <span class="w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center text-sm font-bold">{{ $index + 1 }}.{{ $subIndex + 1 }}</span>
                                            {{ $sub->title }}
                                        </h3>
                                        <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed font-serif">
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

        <div class="mt-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-12 text-center text-white shadow-xl shadow-blue-200">
            <h3 class="text-3xl font-black mb-4">{{ __('Ready to share?') }}</h3>
            <p class="text-blue-100 mb-8 text-lg">{{ __('Download your book in a professional PDF format, ready for print or digital distribution.') }}</p>
            <a href="{{ route('reports.download', $report) }}"
               class="inline-flex items-center gap-3 bg-white text-blue-700 px-10 py-4 rounded-2xl hover:bg-blue-50 active:scale-95 text-xl font-bold transition-all duration-200 shadow-lg shadow-blue-900/20">
                <i class="fa-solid fa-file-pdf"></i> {{ __('Download as PDF') }}
            </a>
        </div>
    @endif
</div>
