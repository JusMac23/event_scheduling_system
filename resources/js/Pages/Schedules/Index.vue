<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    schedules: {
        type: Array,
        default: () => [],
    },
});

// --- VIEW TOGGLE STATE ---
const currentView = ref('list'); // 'list' or 'calendar'

const toggleView = () => {
    currentView.value = currentView.value === 'list' ? 'calendar' : 'list';
};

// --- CALENDAR LOGIC ---
const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const currentDate = ref(new Date());
const selectedEvent = ref(null);
const isEventModalOpen = ref(false);

const currentMonthName = computed(() => {
    return currentDate.value.toLocaleString('default', { month: 'long', year: 'numeric' });
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const days = [];

    // Padding for previous month
    for (let i = 0; i < firstDayOfMonth; i++) {
        days.push({ date: null, isCurrentMonth: false, events: [] });
    }

    // Actual days
    for (let i = 1; i <= daysInMonth; i++) {
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        
        const dayEvents = props.schedules.filter(schedule => {
            return schedule.start_time.startsWith(dateString);
        });

        const today = new Date();
        const isToday = i === today.getDate() && month === today.getMonth() && year === today.getFullYear();

        days.push({ 
            date: i, 
            fullDate: dateString,
            isCurrentMonth: true, 
            isToday: isToday,
            events: dayEvents 
        });
    }

    // Padding for next month
    const remainingCells = 7 - (days.length % 7);
    if (remainingCells < 7) {
        for (let i = 0; i < remainingCells; i++) {
            days.push({ date: null, isCurrentMonth: false, events: [] });
        }
    }

    return days;
});

const prevMonth = () => { currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1); };
const nextMonth = () => { currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1); };

const openEventDetails = (event) => {
    selectedEvent.value = event;
    isEventModalOpen.value = true;
};
const closeEventModal = () => {
    isEventModalOpen.value = false;
    setTimeout(() => { selectedEvent.value = null; }, 200); 
};
const formatDateTime = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString('default', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};

// Helper for dynamic status colors
const getStatusClasses = (status) => {
    const s = (status || 'pending').toLowerCase();
    switch (s) {
        case 'completed': return 'bg-green-50 text-green-700 ring-green-600/20';
        case 'cancelled': return 'bg-rose-50 text-rose-700 ring-rose-600/20';
        case 'rescheduled': return 'bg-blue-50 text-blue-700 ring-blue-600/20';
        default: return 'bg-yellow-50 text-yellow-700 ring-yellow-600/20'; 
    }
};

// --- FORM LOGIC (CREATE/EDIT/DELETE) ---
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

// Store original times to check for changes
const originalStartTime = ref('');
const originalEndTime = ref('');

const form = useForm({
    title: '',
    description: '',
    start_time: '',
    end_time: '',
    status: 'pending',
});

// Check if the date or time was changed during an edit
const isDateTimeChanged = computed(() => {
    if (!isEditing.value) return false;
    return form.start_time !== originalStartTime.value || form.end_time !== originalEndTime.value;
});

// Disable save if processing OR if date/time changed but status isn't rescheduled
const isSaveDisabled = computed(() => {
    if (form.processing) return true;
    if (isDateTimeChanged.value && form.status !== 'rescheduled') return true;
    return false;
});

const openCreateModal = () => {
    isEditing.value = false;
    editingId.value = null;
    originalStartTime.value = '';
    originalEndTime.value = '';
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (schedule) => {
    if (isEventModalOpen.value) {
        closeEventModal();
    }

    isEditing.value = true;
    editingId.value = schedule.id;
    
    const formatForInput = (dateString) => {
        return dateString ? new Date(dateString).toISOString().slice(0, 16) : '';
    };

    const formattedStart = formatForInput(schedule.start_time);
    const formattedEnd = formatForInput(schedule.end_time);

    // Save originals for comparison later
    originalStartTime.value = formattedStart;
    originalEndTime.value = formattedEnd;

    form.title = schedule.title;
    form.description = schedule.description;
    form.start_time = formattedStart;
    form.end_time = formattedEnd;
    form.status = schedule.status || 'pending';
    
    form.clearErrors();
    showModal.value = true;
};

const closeCreateModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitSchedule = () => {
    if (isEditing.value) {
        form.put(route('schedules.update', editingId.value), { onSuccess: () => closeCreateModal() });
    } else {
        form.post(route('schedules.store'), { onSuccess: () => closeCreateModal() });
    }
};

const deleteSchedule = (id) => {
    if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
        if (isEventModalOpen.value) closeEventModal(); 
        router.delete(route('schedules.destroy', id));
    }
};
</script>

<template>
    <Head title="Schedules" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Schedules Management
            </h2>

                <div class="flex items-center gap-3">
                    <button 
                        @click="toggleView"
                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition-colors shadow-sm w-[150px]"
                    >
                        <svg v-if="currentView === 'list'" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>

                        <svg v-else class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>

                        {{ currentView === 'list' ? 'Calendar' : 'List' }}
                    </button>

                    <button 
                        @click="openCreateModal"
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#133e5e] text-white text-sm font-medium rounded-md hover:bg-[#1a537d] transition-colors shadow-sm"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Schedule
                    </button>
                </div>
            </div>
        </template>

        <div v-if="$page.props.flash && $page.props.flash.success" class="mx-auto max-w-full px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md bg-green-50 p-4 border border-green-200 shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ $page.props.flash.success }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 pb-12">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    
                    <div v-if="schedules.length === 0 && currentView === 'list'" class="p-6 text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new schedule.</p>
                    </div>

                    <div v-else-if="currentView === 'calendar'">
                        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">{{ currentMonthName }}</h3>
                            <div class="flex items-center gap-2 relative z-10">
                                <button @click="prevMonth" class="p-2 text-gray-600 hover:bg-gray-200 rounded-md transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="currentDate = new Date()" class="px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 border border-gray-300 rounded-md transition-colors">
                                    Today
                                </button>
                                <button @click="nextMonth" class="p-2 text-gray-600 hover:bg-gray-200 rounded-md transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="grid grid-cols-7 mb-2">
                                <div v-for="day in weekDays" :key="day" class="text-center text-xs font-semibold uppercase tracking-wide text-gray-500 py-2">
                                    {{ day }}
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-px bg-gray-200 rounded-lg overflow-hidden border border-gray-200">
                                <div v-for="(day, index) in calendarDays" :key="index" :class="['min-h-[120px] bg-white p-2 transition-colors', !day.isCurrentMonth ? 'bg-gray-50 text-gray-400' : 'text-gray-900 hover:bg-gray-50']">
                                    <div class="flex justify-between items-start">
                                        <span v-if="day.date" :class="['text-sm font-medium h-7 w-7 flex items-center justify-center rounded-full', day.isToday ? 'bg-[#133e5e] text-white' : '']">
                                            {{ day.date }}
                                        </span>
                                    </div>
                                    <div v-if="day.events.length > 0" class="mt-2 flex flex-col gap-1">
                                        <div v-for="event in day.events" :key="event.id" @click="openEventDetails(event)" class="truncate rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-[#133e5e] border border-blue-100 cursor-pointer hover:bg-blue-200 transition-all" :title="event.title">
                                            {{ new Date(event.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }} - {{ event.title }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#275c85] text-white border-b border-white/10">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider text-white">Title</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider text-white">Description</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider text-white">Date</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider text-white">Status</th>
                                    <th scope="col" class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider text-white">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="schedule in schedules" :key="schedule.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ schedule.title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" :title="schedule.description">{{ schedule.description || 'No description provided.' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ new Date(schedule.start_time).toLocaleDateString() }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span :class="getStatusClasses(schedule.status)" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset capitalize">
                                            {{ schedule.status || 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-right">
                                        <div class="flex justify-end gap-3">
                                            <button @click="openEditModal(schedule)" title="Edit" class="p-1.5 text-[#133e5e] hover:text-[#1a537d] hover:bg-gray-100 rounded-md transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                            <button @click="deleteSchedule(schedule.id)" title="Delete" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-md transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div v-if="isEventModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" @click.self="closeEventModal">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-[#133e5e] text-white">
                    <h3 class="text-lg font-bold">{{ selectedEvent?.title }}</h3>
                    <button @click="closeEventModal" class="text-gray-300 hover:text-white focus:outline-none transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span :class="getStatusClasses(selectedEvent?.status)" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset capitalize">
                            {{ selectedEvent?.status || 'Pending' }}
                        </span>
                    </div>
                    <div class="flex items-start gap-3 text-gray-700">
                        <svg class="h-5 w-5 mt-0.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div>
                            <p class="text-sm font-medium">Starts: <span class="font-normal">{{ formatDateTime(selectedEvent?.start_time) }}</span></p>
                            <p v-if="selectedEvent?.end_time" class="text-sm font-medium mt-1">Ends: <span class="font-normal">{{ formatDateTime(selectedEvent?.end_time) }}</span></p>
                        </div>
                    </div>
                    <div v-if="selectedEvent?.description" class="pt-3 border-t border-gray-100">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ selectedEvent.description }}</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div class="flex gap-2">
                        <button @click="openEditModal(selectedEvent)" class="p-2 text-[#133e5e] bg-blue-50 hover:bg-blue-100 rounded-md transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                        <button @click="deleteSchedule(selectedEvent.id)" class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-md transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                    </div>
                    <button @click="closeEventModal" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-sm font-medium">Close</button>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" @click="closeCreateModal"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                        <form @submit.prevent="submitSchedule">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-base font-semibold leading-6 text-gray-900 border-b pb-2 flex items-center gap-2">
                                            <svg class="h-5 w-5 text-[#133e5e]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            {{ isEditing ? 'Edit Schedule' : 'Create New Schedule' }}
                                        </h3>
                                        <div class="mt-4 space-y-4 text-left">
                                            <div>
                                                <label for="title" class="block text-sm font-medium text-gray-700">Event Title <span class="text-red-500">*</span></label>
                                                <input type="text" id="title" v-model="form.title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] sm:text-sm" placeholder="E.g., Team Meeting">
                                                <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                                            </div>
                                            <div>
                                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                                <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] sm:text-sm" placeholder="Details about this schedule..."></textarea>
                                                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                                            </div>
                                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                <div>
                                                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time <span class="text-red-500">*</span></label>
                                                    <input type="datetime-local" id="start_time" v-model="form.start_time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] sm:text-sm">
                                                    <div v-if="form.errors.start_time" class="text-red-500 text-xs mt-1">{{ form.errors.start_time }}</div>
                                                </div>
                                                <div>
                                                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time <span class="text-red-500">*</span></label>
                                                    <input type="datetime-local" id="end_time" v-model="form.end_time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] sm:text-sm">
                                                    <div v-if="form.errors.end_time" class="text-red-500 text-xs mt-1">{{ form.errors.end_time }}</div>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                                <select id="status" v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] sm:text-sm">
                                                    <option value="pending">Pending</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="cancelled">Cancelled</option>
                                                    <option value="rescheduled">Rescheduled</option>
                                                </select>
                                                
                                                <div v-if="isDateTimeChanged && form.status !== 'rescheduled'" class="mt-2 text-sm text-yellow-600 bg-yellow-50 border border-yellow-200 p-2 rounded flex items-center gap-2">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    Please set status to "Rescheduled" to save changes to the date/time.
                                                </div>

                                                <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" :disabled="isSaveDisabled" class="inline-flex w-full justify-center rounded-md bg-[#133e5e] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1a537d] sm:ml-3 sm:w-auto transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    {{ form.processing ? 'Saving...' : 'Save Schedule' }}
                                </button>
                                <button @click="closeCreateModal" type="button" :disabled="form.processing" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors disabled:opacity-50">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>