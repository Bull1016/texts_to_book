@extends('layouts.app')

@section('title', __('Create New Report') . ' - Texts to Book')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl mb-6 shadow-sm">
                <i class="fa-solid fa-wand-magic-sparkles text-2xl"></i>
            </div>
            <h1 class="text-4xl font-black text-gray-900 mb-4 tracking-tight">{{ __('Create a New Masterpiece') }}</h1>
            <p class="text-lg text-gray-500 font-medium">{{ __('Provide a title and a subject, and our AI will do the rest.') }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100 overflow-hidden">
            <form action="{{ route('reports.store') }}" method="POST" class="p-8 md:p-12 space-y-8">
                @csrf

                <!-- Title -->
                <div class="space-y-2">
                    <label for="title" class="text-sm font-black uppercase tracking-widest text-gray-400 ml-1">{{ __('Book Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full bg-gray-50 border-2 border-transparent focus:border-blue-100 focus:bg-white focus:ring-4 focus:ring-blue-50 rounded-2xl px-6 py-4 text-lg font-bold transition-all placeholder-gray-300"
                           placeholder="{{ __('e.g., The Future of Artificial Intelligence') }}">
                    @error('title') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Language -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-sm font-black uppercase tracking-widest text-gray-400 ml-1">{{ __('Language') }}</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['fr' => '🇫🇷 FR', 'en' => '🇺🇸 EN', 'es' => '🇪🇸 ES', 'de' => '🇩🇪 DE'] as $code => $label)
                                <label class="cursor-pointer group">
                                    <input type="radio" name="language" value="{{ $code }}" {{ old('language', 'fr') === $code ? 'checked' : '' }} class="peer hidden">
                                    <div class="flex items-center justify-center py-3 rounded-2xl border-2 border-gray-100 bg-gray-50 text-sm font-bold text-gray-500 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 group-hover:border-gray-200 transition-all">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('language') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Subject/Content -->
                <div class="space-y-2">
                    <label for="subject" class="text-sm font-black uppercase tracking-widest text-gray-400 ml-1">{{ __('What is it about?') }}</label>
                    <div class="relative">
                        <textarea name="subject" id="subject" rows="6" required
                                  class="w-full bg-gray-50 border-2 border-transparent focus:border-blue-100 focus:bg-white focus:ring-4 focus:ring-blue-50 rounded-[2rem] px-8 py-6 text-base font-medium leading-relaxed transition-all placeholder-gray-300 min-h-[200px]"
                                  placeholder="{{ __('Describe the subject, key points you want to cover, and the overall tone of the book...') }}">{{ old('subject') }}</textarea>
                        <div class="absolute bottom-6 right-8 text-[10px] font-black uppercase tracking-widest text-gray-300">
                            {{ __('Minimum 10 characters') }}
                        </div>
                    </div>
                    @error('subject') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Submit -->
                <div class="pt-6">
                    <button type="submit"
                            class="w-full bg-blue-600 text-white rounded-[1.5rem] py-5 text-xl font-black shadow-xl shadow-blue-200 hover:bg-blue-700 hover:shadow-2xl hover:-translate-y-1 active:translate-y-0 active:scale-[0.98] transition-all flex items-center justify-center gap-3 group">
                        <span>{{ __('Generate My Book') }}</span>
                        <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    <p class="text-center text-gray-400 text-xs font-bold mt-6 flex items-center justify-center gap-2 uppercase tracking-tighter">
                        <i class="fa-solid fa-shield-halved text-blue-300"></i>
                        {{ __('Powered by Gemini 1.5 Pro & Unsplash') }}
                    </p>
                </div>
            </form>
        </div>

        <!-- Secondary CTA -->
        <div class="mt-10 text-center">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('Back to my reports') }}
            </a>
        </div>
    </div>
</div>
@endsection
