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

<div class="flex flex-col lg:flex-row gap-10">
    <!-- Sidebar Filters -->
    <aside class="w-full lg:w-72 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sticky top-28">
            <h3 class="text-xl font-black mb-8 flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-sliders text-sm"></i>
                </div>
                {{ __('Filters') }}
            </h3>

            <!-- Search -->
            <div class="mb-8">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">{{ __('Search by Name') }}</label>
                <div class="relative group">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="w-full bg-gray-50 border-none rounded-2xl py-3 pl-11 focus:ring-2 focus:ring-blue-100 transition-all placeholder-gray-300 font-medium"
                           placeholder="{{ __('Book title...') }}">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-blue-400 transition-colors"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="mb-8">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">{{ __('Status') }}</label>
                <div class="space-y-2">
                    @foreach(['pending' => 'bg-gray-100', 'generating' => 'bg-amber-100', 'completed' => 'bg-green-100', 'failed' => 'bg-red-100'] as $status => $color)
                        <label class="flex items-center group cursor-pointer">
                            <div class="relative flex items-center">
                                <input type="checkbox" wire:model.live="selectedStatuses" value="{{ $status }}"
                                       class="rounded-lg border-gray-200 text-blue-600 focus:ring-blue-100 w-5 h-5 transition-all">
                            </div>
                            <span class="ml-3 text-sm font-semibold text-gray-600 group-hover:text-gray-900 transition-colors capitalize">{{ __($status) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            @if(!empty($search) || !empty($selectedStatuses))
                <button wire:click="$set('search', ''); $set('selectedStatuses', [])"
                        class="w-full py-3 bg-red-50 text-red-600 rounded-2xl text-sm font-bold hover:bg-red-100 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-trash-can text-xs"></i> {{ __('Clear filters') }}
                </button>
            @endif
        </div>
    </aside>

    <!-- Main Content: Card Grid -->
    <div class="flex-1">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @forelse($this->getData() as $report)
                <div class="group bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <!-- Cover Image -->
                    <div class="h-60 bg-gray-100 relative overflow-hidden">
                        @if($report->cover_image_url)
                            <img src="{{ $report->cover_image_url }}" alt="{{ $report->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            @php $firstImage = $report->images()->first(); @endphp
                            @if($firstImage)
                                <img src="{{ $firstImage->image_url }}" alt="{{ $report->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 bg-gradient-to-br from-gray-50 to-gray-100">
                                    <i class="fa-solid fa-book-open text-5xl mb-4 opacity-20"></i>
                                    <span class="text-[10px] uppercase font-black tracking-widest">{{ __('No Image Yet') }}</span>
                                </div>
                            @endif
                        @endif

                        <!-- Status Badge Overlay -->
                        <div class="absolute top-4 left-4">
                             <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl shadow-xl backdrop-blur-md
                                @if($report->status === 'completed') bg-green-500/90 text-white
                                @elseif($report->status === 'generating') bg-amber-500/90 text-white
                                @elseif($report->status === 'failed') bg-red-500/90 text-white
                                @else bg-gray-500/90 text-white
                                @endif font-bold text-[10px] uppercase tracking-wider">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $report->status === 'generating' ? 'bg-white' : 'hidden' }}"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $report->status === 'completed' ? 'bg-white' : ($report->status === 'generating' ? 'bg-white' : ($report->status === 'failed' ? 'bg-white' : 'bg-white')) }}"></span>
                                </span>
                                {{ __(ucfirst($report->status)) }}
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-2">
                                <i class="fa-solid fa-calendar-day"></i>
                                {{ $report->created_at->format('M d, Y') }}
                            </div>
                            <h4 class="text-xl font-black text-gray-900 mb-2 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors" title="{{ $report->title }}">
                                {{ $report->title }}
                            </h4>
                            <p class="text-sm text-gray-500 line-clamp-2 font-medium mb-4">
                                {{ $report->subject }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="pt-6 border-t border-gray-50 flex items-center justify-between gap-3">
                            <div class="flex gap-2">
                                <a href="{{ route('reports.show', $report) }}"
                                   class="w-10 h-10 flex items-center justify-center bg-gray-900 text-white rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-gray-200"
                                   title="{{ __('View Details') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                @if($report->status === 'completed')
                                    <a href="{{ route('reports.download', $report) }}"
                                       class="w-10 h-10 flex items-center justify-center bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all shadow-lg shadow-green-100"
                                       title="{{ __('Download PDF') }}">
                                        <i class="fa-solid fa-download text-sm"></i>
                                    </a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('reports.destroy', $report) }}" class="inline" id="list-delete-{{ $report->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="swalDelete(document.getElementById('list-delete-{{ $report->id }}'), '{{ __('Delete report?') }}', '{{ addslashes(__('This will permanently remove') . ' &quot;' . addslashes($report->title) . '&quot;.') }}')"
                                    class="w-10 h-10 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                    title="{{ __('Delete') }}">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-[40px] shadow-sm border border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-book-open text-gray-200 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('No books found') }}</h3>
                    <p class="text-gray-500 max-w-xs mx-auto mb-8">{{ __('Start by creating your first AI-powered book today.') }}</p>
                    <a href="{{ route('reports.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all">
                        <i class="fa-solid fa-plus"></i> {{ __('Create New Report') }}
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $this->getData()->links() }}
        </div>
    </div>
</div>
