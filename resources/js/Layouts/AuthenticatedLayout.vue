<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

// State management for sidebars
const isSidebarCollapsed = ref(false);
const isMobileSidebarOpen = ref(false);

// Date and Time Management
const currentDateTime = ref('');
let timeInterval = null;

const updateDateTime = () => {
    const now = new Date();
    // Format the date specifically for the Asia/Manila timezone
    currentDateTime.value = now.toLocaleString('en-US', {
        timeZone: 'Asia/Manila',
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
};

onMounted(() => {
    updateDateTime(); // Set initial time
    timeInterval = setInterval(updateDateTime, 1000); // Update every second
});

onUnmounted(() => {
    if (timeInterval) clearInterval(timeInterval); // Clean up the interval
});
</script>

<template>
    <div class="flex h-screen bg-gray-50 font-sans overflow-hidden">
        
        <div 
            v-if="isMobileSidebarOpen" 
            @click="isMobileSidebarOpen = false" 
            class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm transition-opacity md:hidden"
        ></div>

        <aside 
            :class="isMobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-[#133e5e] shadow-2xl transition-transform duration-300 ease-in-out md:hidden flex flex-col"
        >
            <div class="flex h-16 shrink-0 items-center justify-between px-4 border-b border-white/10">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <ApplicationLogo class="block h-8 w-auto fill-current text-sky-400" />
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-white leading-none tracking-wide">Event</span>
                        <span class="text-[10px] font-semibold text-sky-300 tracking-wider uppercase mt-1">Scheduling System</span>
                    </div>
                </Link>
                <button @click="isMobileSidebarOpen = false" class="p-2 text-slate-300 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-6">
                <Link 
                    :href="route('dashboard')" 
                    :class="route().current('dashboard') ? 'bg-white/15 text-white shadow-inner border border-white/5' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition-all duration-200"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </Link>

                <Link 
                    :href="route('schedules')" 
                    :class="route().current('schedules') ? 'bg-white/15 text-white shadow-inner border border-white/5' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                    class="mt-2 flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition-all duration-200"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Schedules</span>
                </Link>
            </nav>

            <div class="border-t border-white/10 p-4 shrink-0">
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all duration-200"
                >
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log Out
                </Link>
            </div>
        </aside>

        <aside 
            :class="isSidebarCollapsed ? 'w-20' : 'w-64'"
            class="hidden md:flex flex-col bg-[#133e5e] border-r border-white/10 transition-all duration-300 ease-in-out relative z-20 shadow-lg"
        >
            <div class="flex h-16 shrink-0 items-center border-b border-white/10 px-4" :class="isSidebarCollapsed ? 'justify-center' : 'justify-start'">
                <Link :href="route('dashboard')" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                    <ApplicationLogo class="block h-8 w-auto fill-current text-sky-400 shrink-0 drop-shadow-sm" />
                    <div 
                        :class="isSidebarCollapsed ? 'opacity-0 hidden' : 'opacity-100 flex'"
                        class="flex-col transition-opacity duration-300"
                    >
                        <span class="text-lg font-bold text-white leading-none tracking-wide">Event</span>
                        <span class="text-[10px] font-semibold text-sky-300 tracking-wider uppercase mt-1">Scheduling System</span>
                    </div>
                </Link>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-6">
                <Link 
                    :href="route('dashboard')" 
                    :title="isSidebarCollapsed ? 'Dashboard' : ''"
                    :class="[
                        route().current('dashboard') ? 'bg-white/15 text-white shadow-inner border border-white/5' : 'text-slate-300 hover:bg-white/10 hover:text-white',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3'
                    ]"
                    class="group flex items-center gap-3 rounded-lg py-2.5 font-medium transition-all duration-200"
                >
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span v-show="!isSidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
                </Link>

                <Link 
                    :href="route('schedules')" 
                    :title="isSidebarCollapsed ? 'Schedules' : ''"
                    :class="[
                        route().current('schedules') ? 'bg-white/15 text-white shadow-inner border border-white/5' : 'text-slate-300 hover:bg-white/10 hover:text-white',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3',
                        'mt-2' 
                    ]"
                    class="group flex items-center gap-3 rounded-lg py-2.5 font-medium transition-all duration-200"
                >
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span v-show="!isSidebarCollapsed" class="whitespace-nowrap">Schedules</span>
                </Link>
            </nav>

            <div class="border-t border-white/10 p-4 shrink-0">
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    :title="isSidebarCollapsed ? 'Log Out' : ''"
                    :class="[
                        'group flex w-full items-center gap-3 rounded-lg py-2.5 font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all duration-200',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3'
                    ]"
                >
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span v-show="!isSidebarCollapsed" class="whitespace-nowrap">Log Out</span>
                </Link>
            </div>
        </aside>

        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            
            <header class="flex h-16 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6 lg:px-8 shadow-sm">
                
                <div class="flex items-center">
                    <button 
                        @click="isMobileSidebarOpen = true" 
                        class="mr-4 rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 md:hidden focus:outline-none transition-colors"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <button 
                        @click="isSidebarCollapsed = !isSidebarCollapsed" 
                        class="hidden md:block mr-4 rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none transition-colors"
                        :title="isSidebarCollapsed ? 'Open Sidebar' : 'Hide Sidebar'"
                    >
                        <svg v-if="isSidebarCollapsed" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v18" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l3 3-3 3" />
                        </svg>

                        <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v18" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 9l-3 3 3 3" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    
                    <div class="flex items-center px-2 md:px-3 py-1.5 text-xs md:text-sm font-medium text-gray-600 whitespace-nowrap">
                        <svg class="h-3.5 w-3.5 md:h-4 md:w-4 mr-1.5 md:mr-2 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ currentDateTime }}</span>
                    </div>

                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button 
                                    type="button" 
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-[#133e5e] text-sm font-bold text-white transition duration-200 ease-in-out hover:bg-[#1a537d] focus:outline-none focus:ring-2 focus:ring-[#133e5e] focus:ring-offset-2"
                                    :title="$page.props.auth.user.firstname + ' ' + $page.props.auth.user.lastname"
                                >
                                    {{ $page.props.auth.user.firstname?.charAt(0).toUpperCase() }}{{ $page.props.auth.user.lastname?.charAt(0).toUpperCase() }}
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <div v-if="$slots.header" class="bg-white shadow-sm shrink-0 border-b border-gray-100 z-10">
                <div class="px-4 py-5 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </div>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>

        </div>
    </div>
</template>