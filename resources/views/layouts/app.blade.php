<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Texts to Book')</title>
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @if(auth()->check())
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">📚 Texts to Book</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Reports') }}</a>
                        <a href="{{ route('reports.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                           <i class="fa-solid fa-plus mr-1"></i>{{ __('New Report') }}
                        </a>

                        <!-- Language Switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-900 focus:outline-none">
                                <i class="fa-solid fa-earth-americas mr-1"></i>
                                <span>{{ strtoupper(app()->getLocale()) }}</span>
                                <i class="fa-solid fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50" style="display: none;">
                                <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <span class="mr-2">🇺🇸</span> English
                                </a>
                                <a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <span class="mr-2">🇫🇷</span> Français
                                </a>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-900 focus:outline-none">
                                <i class="fa-solid fa-user-astronaut text-xl"></i>
                                <i class="fa-solid fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50" style="display: none;">
                                <div class="px-4 py-2 text-sm text-gray-500 border-b">
                                    {{ auth()->user()->name }}
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fa-solid fa-user mr-2 text-gray-400"></i> {{ __('Profile') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                        <i class="fa-solid fa-rocket mr-2 text-red-400"></i> {{ __('Logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    {{-- Toast notifications --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3" aria-live="polite">
            @if(session('success'))
                <div class="toast flex items-start gap-3 bg-green-600 text-white px-5 py-4 rounded-lg shadow-lg max-w-sm transition-all duration-500 opacity-0" role="alert">
                    <span class="text-xl leading-none">✅</span>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white leading-none text-lg">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="toast flex items-start gap-3 bg-red-600 text-white px-5 py-4 rounded-lg shadow-lg max-w-sm transition-all duration-500 opacity-0" role="alert">
                    <span class="text-xl leading-none">❌</span>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white leading-none text-lg">&times;</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="toast flex items-start gap-3 bg-yellow-500 text-white px-5 py-4 rounded-lg shadow-lg max-w-sm transition-all duration-500 opacity-0" role="alert">
                    <span class="text-xl leading-none">⚠️</span>
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white leading-none text-lg">&times;</button>
                </div>
            @endif
            @if(session('info'))
                <div class="toast flex items-start gap-3 bg-blue-600 text-white px-5 py-4 rounded-lg shadow-lg max-w-sm transition-all duration-500 opacity-0" role="alert">
                    <span class="text-xl leading-none">ℹ️</span>
                    <p class="text-sm font-medium">{{ session('info') }}</p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white leading-none text-lg">&times;</button>
                </div>
            @endif
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.toast').forEach(function (toast) {
                    // Fade in
                    setTimeout(() => toast.classList.replace('opacity-0', 'opacity-100'), 50);
                    // Auto-dismiss after 5s
                    setTimeout(() => {
                        toast.classList.replace('opacity-100', 'opacity-0');
                        setTimeout(() => toast.remove(), 500);
                    }, 5000);
                });
            });
        </script>
    @endif

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <footer class="bg-gray-100 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600">
            <p>&copy; {{ date('Y') }} Texts to Book. Transform your ideas into beautiful books.</p>
        </div>
    </footer>
    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        /**
         * Affiche une modale de confirmation Swal avant de soumettre un formulaire de suppression.
         * @param {HTMLElement} form - Le formulaire DELETE à soumettre si confirmé.
         * @param {string} title    - Titre de la modale (optionnel).
         * @param {string} text     - Corps du message (optionnel).
         */
        function swalDelete(form, title, text) {
            Swal.fire({
                title: title || 'Confirmer la suppression',
                text: text || 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                focusCancel: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
