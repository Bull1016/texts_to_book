<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now();
            this.toasts.push({
                id: id,
                type: toast.type,
                message: toast.message,
                show: false
            });
            // Small delay to trigger transition
            setTimeout(() => {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index !== -1) this.toasts[index].show = true;
            }, 10);

            // Auto-remove after 5s
            setTimeout(() => {
                this.remove(id);
            }, 5000);
        },
        remove(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300); // Wait for transition out
            }
        }
    }"
    x-init="
        @if(session('success')) add({ type: 'success', message: '{{ addslashes(session('success')) }}' }); @endif
        @if(session('error')) add({ type: 'error', message: '{{ addslashes(session('error')) }}' }); @endif
        @if(session('warning')) add({ type: 'warning', message: '{{ addslashes(session('warning')) }}' }); @endif
        @if(session('info')) add({ type: 'info', message: '{{ addslashes(session('info')) }}' }); @endif
        @if(session('status')) add({ type: 'info', message: '{{ addslashes(session('status')) }}' }); @endif
    "
    @notify.window="add($event.detail)"
    class="fixed bottom-5 right-5 z-[9999] flex flex-col space-y-3 w-full max-w-sm pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="max-w-sm w-full bg-white shadow-2xl rounded-xl pointer-events-auto overflow-hidden border border-gray-100"
        >
            <div class="p-4" :class="{
                'bg-green-50': toast.type === 'success',
                'bg-red-50': toast.type === 'error',
                'bg-yellow-50': toast.type === 'warning',
                'bg-blue-50': toast.type === 'info'
            }">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <template x-if="toast.type === 'success'">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p x-text="toast.message" class="text-sm font-semibold text-gray-900"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="remove(toast.id)" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
