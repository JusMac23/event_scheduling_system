<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome - Event Scheduling System" />
    
    <div class="min-h-screen bg-gray-50 text-gray-800 font-sans selection:bg-[#133e5e] selection:text-white relative overflow-hidden">
        
        <div class="absolute top-0 -left-4 w-48 h-48 md:w-72 md:h-72 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-48 h-48 md:w-72 md:h-72 bg-[#133e5e] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-48 h-48 md:w-72 md:h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col min-h-screen">
            
            <header class="flex flex-col sm:flex-row items-center justify-between py-6 gap-4 sm:gap-0">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="flex h-8 w-8 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-xl bg-[#133e5e] text-sky-400 shadow-lg">
                        <ApplicationLogo class="block h-8 w-auto fill-current text-sky-400" />
                    </div>
                    <span class="text-lg sm:text-xl font-extrabold tracking-tight text-[#133e5e] text-center sm:text-left">
                        Event Scheduling System
                    </span>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-center sm:justify-end">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-full px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-semibold text-white bg-[#133e5e] hover:bg-[#1a537d] transition-all shadow-md hover:shadow-lg text-center"
                    >
                        Go to Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-full px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-full px-4 py-2 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-semibold text-white bg-[#133e5e] hover:bg-[#1a537d] transition-all shadow-md hover:shadow-lg"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="flex-1 flex flex-col items-center justify-center text-center py-12 sm:py-16 md:py-24">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs sm:text-sm font-medium mb-6 border border-sky-200 shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-sky-500"></span>
                    Now available for teams
                </span>
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 tracking-tight max-w-4xl px-2">
                    Schedule events with <br class="hidden sm:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#133e5e] to-sky-500">absolute precision.</span>
                </h1>
                
                <p class="mt-6 text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl leading-relaxed px-4">
                    The ultimate event scheduling system designed to eliminate double-bookings, streamline your calendar, and save your team hours every week.
                </p>

                <div class="mt-8 sm:mt-10 flex flex-col sm:flex-row gap-4 w-full sm:w-auto px-4 sm:px-0">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-full px-6 py-3 sm:px-8 sm:py-4 text-sm sm:text-base font-bold text-white bg-[#133e5e] hover:bg-[#1a537d] transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1"
                    >
                        Access Your Dashboard
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>
            </main>

            <section class="py-12 sm:py-16 grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 relative z-10 px-4 sm:px-0">
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mb-4 sm:mb-6">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">Smart Scheduling</h3>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed">Automate your availability and let invitees pick times that work for both of you instantly.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-4 sm:mb-6">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">Team Collaboration</h3>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed">Round-robin scheduling, collective availability, and seamless team management in one place.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4 sm:mb-6">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.956 11.956 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">No Double Bookings</h3>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed">Real-time conflict checks ensure you are never overbooked, giving you complete peace of mind.</p>
                </div>
            </section>

            <footer class="mt-auto py-8 border-t border-gray-200 flex items-center justify-center text-xs sm:text-sm text-gray-500 text-center px-4">
                <p>&copy; {{ new Date().getFullYear() }} Event Scheduling System. All rights reserved.</p>
            </footer>

        </div>
    </div>
</template>

<style>
/* Custom animations for the background blobs */
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}
</style>