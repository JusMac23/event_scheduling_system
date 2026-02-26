<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// The 'schedules' prop receives the array of items from your database
const props = defineProps({
    schedules: {
        type: Array,
        default: () => [],
    },
});

/** * STATISTICS LOGIC 
 * These computed properties automatically scan your database results
 */

// 1. Total count of all rows in your schedules table
const totalMeetings = computed(() => props.schedules.length);

// 2. Count where status is 'done' or 'completed'
const doneMeetings = computed(() => {
    return props.schedules.filter(s => 
        s.status?.toLowerCase() === 'done' || 
        s.status?.toLowerCase() === 'completed'
    ).length;
});

// 3. Count where status is 'cancelled'
const cancelledMeetings = computed(() => {
    return props.schedules.filter(s => 
        s.status?.toLowerCase() === 'cancelled'
    ).length;
});

// 4. Count where status is 'rescheduled'
const rescheduledMeetings = computed(() => {
    return props.schedules.filter(s => 
        s.status?.toLowerCase() === 'rescheduled'
    ).length;
});

// 5. Calculate Pending (Total minus completed, cancelled, and rescheduled)
const pendingMeetings = computed(() => {
    return totalMeetings.value - (doneMeetings.value + cancelledMeetings.value + rescheduledMeetings.value);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Overview
            </h2>
        </template>

        <div class="pt-4 pb-12">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase">Total Schedules</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ totalMeetings }}</p>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-[#133e5e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                <span class="font-semibold text-blue-600">{{ pendingMeetings }}</span> upcoming sessions
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase">Completed</p>
                                    <p class="text-3xl font-bold text-green-600">{{ doneMeetings }}</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                Successfully finished meetings
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase">Cancelled</p>
                                    <p class="text-3xl font-bold text-red-600">{{ cancelledMeetings }}</p>
                                </div>
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                Meetings that were dropped
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase">Rescheduled</p>
                                    <p class="text-3xl font-bold text-orange-500">{{ rescheduledMeetings }}</p>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                Moved to a different time
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-8 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Recent Schedules</h3>
                        <Link :href="route('schedules')" class="text-sm text-[#133e5e] hover:underline font-medium">
                            View All →
                        </Link>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-[#275c85] text-white text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Title</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="schedule in schedules.slice(0, 5)" :key="schedule.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ schedule.title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ new Date(schedule.start_time).toLocaleDateString() }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold capitalize" :class="{
                                            'bg-green-50 text-green-700 ring-green-600/20': schedule.status === 'done' || schedule.status === 'completed',
                                            'bg-red-50 text-red-700 ring-red-600/20': schedule.status === 'cancelled',
                                            'bg-blue-50 text-blue-700 ring-blue-600/20': schedule.status === 'rescheduled',
                                            'bg-yellow-50 text-yellow-700 ring-yellow-600/20': !schedule.status || schedule.status === 'pending'
                                        }">
                                            {{ schedule.status || 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="schedules.length === 0">
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">No schedules found in database.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>