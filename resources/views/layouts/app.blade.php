<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Texts to Book - Transform your ideas into professional books')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Instrument Serif', serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F9FAFB] text-gray-900 h-full flex flex-col">
    @if(auth()->check())
        <nav class="glass sticky top-0 z-40 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200 group-hover:rotate-6 transition-transform">
                                <i class="fa-solid fa-book-open text-white"></i>
                            </div>
                            <span class="text-xl font-extrabold tracking-tight text-gray-900">Texts to <span class="text-blue-600">Book</span></span>
                        </a>
                    </div>

                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors">{{ __('My Reports') }}</a>

                        <!-- Language Switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 focus:outline-none bg-gray-50 px-3 py-2 rounded-lg transition-colors">
                                <i class="fa-solid fa-earth-americas text-blue-500"></i>
                                <span>{{ strtoupper(app()->getLocale()) }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden" style="display: none;">
                                <a href="{{ route('lang.switch', 'en') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                    <span class="text-lg">🇺🇸</span> English
                                </a>
                                <a href="{{ route('lang.switch', 'fr') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                    <span class="text-lg">🇫🇷</span> Français
                                </a>
                            </div>
                        </div>

                        <div class="h-6 w-px bg-gray-200"></div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                                <div class="text-right hidden lg:block">
                                    <p class="text-xs font-bold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white shadow-md group-hover:shadow-lg transition-all">
                                    <i class="fa-solid fa-user-astronaut"></i>
                                </div>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-2xl shadow-xl z-50 overflow-hidden" style="display: none;">
                                <div class="px-4 py-4 bg-gray-50 border-b border-gray-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Account') }}</p>
                                </div>
                                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 transition-colors">
                                    <i class="fa-solid fa-circle-user text-gray-400"></i> {{ __('Profile Settings') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fa-solid fa-rocket text-red-400"></i> {{ __('Sign Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <a href="{{ route('reports.create') }}"
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700 active:scale-95 shadow-lg shadow-blue-200 transition-all">
                           <i class="fa-solid fa-plus text-xs"></i>{{ __('Create') }}
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-white text-xs"></i>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-gray-900">Texts to <span class="text-blue-600">Book</span></span>
                </div>
                <p class="text-sm text-gray-500 font-medium">&copy; {{ date('Y') }} Texts to Book. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Toast notifications --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div id="toast-container" class="fixed bottom-10 right-10 z-50 space-y-4" aria-live="polite">
            @foreach(['success' => ['bg-green-600', 'fa-circle-check'], 'error' => ['bg-red-600', 'fa-circle-xmark'], 'warning' => ['bg-amber-500', 'fa-triangle-exclamation'], 'info' => ['bg-blue-600', 'fa-circle-info']] as $key => $config)
                @if(session($key))
                    <div class="toast flex items-center gap-4 {{ $config[0] }} text-white px-6 py-4 rounded-2xl shadow-2xl max-w-md transition-all duration-500 translate-y-20 opacity-0" role="alert">
                        <i class="fa-solid {{ $config[1] }} text-xl"></i>
                        <p class="text-sm font-bold">{{ session($key) }}</p>
                        <button onclick="this.parentElement.remove()" class="ml-auto hover:scale-110 transition-transform"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif
            @endforeach
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.toast').forEach(function (toast) {
                    setTimeout(() => {
                        toast.classList.remove('translate-y-20', 'opacity-0');
                    }, 100);
                    setTimeout(() => {
                        toast.classList.add('translate-y-20', 'opacity-0');
                        setTimeout(() => toast.remove(), 500);
                    }, 6000);
                });
            });
        </script>
    @endif

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function swalDelete(form, title, text) {
            Swal.fire({
                title: title || 'Are you sure?',
                text: text || "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    container: 'font-sans',
                    popup: 'rounded-3xl border-none p-8',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold bg-gray-100 text-gray-500 hover:bg-gray-200'
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
