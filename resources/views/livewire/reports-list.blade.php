<?php

use App\Models\Report;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $selectedStatuses = [];

    protected $queryString = ['search', 'selectedStatuses'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatuses()
    {
        $this->resetPage();
    }

    public function getData()
    {
        $query = Report::where('user_id', auth()->id())->latest();

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->selectedStatuses)) {
            $query->whereIn('status', $this->selectedStatuses);
        }

        return $query->paginate(12);
    }
}; ?>

<div class="flex flex-col md:flex-row gap-6">
    <!-- Sidebar Filters -->
    <aside class="w-full md:w-64 flex-shrink-0">
        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fa-solid fa-filter mr-2 text-blue-500"></i> {{ __('Filters') }}
            </h3>

            <!-- Search -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search by Name') }}</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-10"
                           placeholder="{{ __('Search') }}...">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Filter by Status') }}</label>
                <div class="space-y-2">
                    @foreach(['pending', 'generating', 'completed', 'failed'] as $status)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="selectedStatuses" value="{{ $status }}"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 capitalize">{{ __($status) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            @if(!empty($search) || !empty($selectedStatuses))
                <button wire:click="$set('search', ''); $set('selectedStatuses', [])"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    {{ __('Clear all filters') }}
                </button>
            @endif
        </div>
    </aside>

    <!-- Main Content: Card Grid -->
    <div class="flex-1">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->getData() as $report)
                <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                    <!-- Cover Image -->
                    <div class="h-48 bg-gray-200 relative overflow-hidden group">
                        @php
                            $firstImage = $report->images()->first();
                        @endphp
                        @if($firstImage)
                            <img src="{{ $firstImage->url }}" alt="{{ $report->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <i class="fa-solid fa-book text-4xl mb-2"></i>
                                <span class="text-xs uppercase font-bold">{{ __('No Image') }}</span>
                            </div>
                        @endif

                        <!-- Status Badge Overlay -->
                        <div class="absolute top-3 right-3">
                            <span class="px-2 py-1 text-xs font-bold rounded-md shadow-sm
                                @if($report->status === 'completed') bg-green-500 text-white
                                @elseif($report->status === 'generating') bg-yellow-500 text-white
                                @elseif($report->status === 'failed') bg-red-500 text-white
                                @else bg-gray-500 text-white
                                @endif">
                                {{ __(ucfirst($report->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col">
                        <h4 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1" title="{{ $report->title }}">
                            {{ $report->title }}
                        </h4>
                        <p class="text-xs text-gray-500 mb-4 flex items-center">
                            <i class="fa-regular fa-calendar mr-1 text-blue-400"></i> {{ $report->created_at->format('M d, Y') }}
                        </p>

                        <!-- Actions (Bottom) -->
                        <div class="mt-auto pt-4 border-t border-gray-100 grid grid-cols-2 gap-2">
                            <a href="{{ route('reports.show', $report) }}"
                               class="flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors">
                                <i class="fa-solid fa-eye mr-2"></i> {{ __('View') }}
                            </a>
                            <a href="{{ route('reports.destroy', $report) }}"
                               class="flex items-center justify-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-md transition-colors">
                                <i class="fa-solid fa-trash mr-2"></i> {{ __('Delete') }}
                            </a>
                            @if($report->status === 'completed')
                                <a href="{{ route('reports.download', $report) }}"
                                   class="flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 text-sm font-medium rounded-md transition-colors">
                                    <i class="fa-solid fa-download mr-2"></i> {{ __('Download') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-lg shadow">
                    <i class="fa-solid fa-folder-open text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500">{{ __('No reports found.') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $this->getData()->links() }}
        </div>
    </div>
</div>
