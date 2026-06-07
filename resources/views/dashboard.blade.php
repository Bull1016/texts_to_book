@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Chart Box -->
            <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">{{ __('Reports Overview') }}</h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Stats or Welcome (Optional, but good for layout) -->
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-center text-center border-l-4 border-blue-500">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Hello, {{ auth()->user()->name }}!</h2>
                <p class="text-gray-600">{{ __('Transform your ideas into beautiful books using our AI-powered platform.') }}</p>
                <div class="mt-6">
                    <a href="{{ route('reports.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 shadow-sm transition-all duration-150">
                        <i class="fa-solid fa-plus"></i> {{ __('New Report') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- DataTables Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-6 text-gray-700">{{ __('Recent Reports') }}</h3>
            <div class="overflow-x-auto">
                <table id="reportsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Title') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created At') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Loaded via DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Polar Area Chart
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($statusStats);

    const labels = Object.keys(statusData).map(label => label.charAt(0).toUpperCase() + label.slice(1));
    const values = Object.values(statusData);

    new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    'rgba(34, 197, 94, 0.6)',  // Green (completed)
                    'rgba(234, 179, 8, 0.6)',   // Yellow (generating)
                    'rgba(239, 68, 68, 0.6)',   // Red (failed)
                    'rgba(107, 114, 128, 0.6)'  // Gray (pending)
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // DataTables
    $('#reportsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        serverSide: true,
        ajax: "{{ route('dashboard') }}",
        columns: [
            {data: 'title', name: 'title'},
            {data: 'status', name: 'status'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-right'},
        ],
        language: {
            search: "{{ __('Search') }}:",
            lengthMenu: "_MENU_",
            info: "",
            paginate: {
                previous: "<i class='fa-solid fa-chevron-left'></i>",
                next: "<i class='fa-solid fa-chevron-right'></i>"
            }
        },
        drawCallback: function() {
            // Close all open menus on redraw
            document.querySelectorAll('.action-menu').forEach(m => m.classList.add('hidden'));
        }
    });
});

// Toggle action dropdown with fixed positioning (avoids overflow clipping)
window.toggleActionMenu = function(btn) {
    const menu = btn.closest('div').querySelector('.action-menu');
    const allMenus = document.querySelectorAll('.action-menu');

    // Close all other menus
    allMenus.forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });

    if (menu.classList.contains('hidden')) {
        const rect = btn.getBoundingClientRect();
        const menuWidth = 176; // w-44 = 11rem = 176px
        let left = rect.right - menuWidth;
        let top = rect.bottom + 4;

        // Prevent going off-screen left
        if (left < 8) left = 8;
        // Prevent going off-screen right
        if (left + menuWidth > window.innerWidth - 8) left = window.innerWidth - menuWidth - 8;
        // Flip up if not enough space below
        if (top + 160 > window.innerHeight) top = rect.top - 160;

        menu.style.top = top + 'px';
        menu.style.left = left + 'px';
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
};

// Close menus on outside click or scroll
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-btn') && !e.target.closest('.action-menu')) {
        document.querySelectorAll('.action-menu').forEach(m => m.classList.add('hidden'));
    }
});
window.addEventListener('scroll', function() {
    document.querySelectorAll('.action-menu').forEach(m => m.classList.add('hidden'));
}, true);
</script>

<style>
/* Custom DataTables Styling */
.dataTables_wrapper .dataTables_filter {
    float: right;
    margin-bottom: 1.5rem;
}
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    margin-left: 0.5rem;
    outline: none;
}
.dataTables_wrapper .dataTables_length select {
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 0.5rem 2rem 0.5rem 1rem;
    outline: none;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.5rem;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #2563eb !important;
    color: white !important;
    border: none;
    border-radius: 0.375rem;
}
</style>
@endsection
