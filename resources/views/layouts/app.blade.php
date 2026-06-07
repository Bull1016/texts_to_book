<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Texts to Book')</title>
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
                        <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900">Reports</a>
                        <a href="{{ route('reports.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">New Report</a>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                        </form>
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
</body>
</html>
