<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                Welcome back
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                Please enter your details to sign in.
            </p>
        </div>

        <div v-if="status" class="mb-6 rounded-lg bg-green-50 p-4 text-sm font-medium text-green-700 border border-green-200 shadow-sm flex items-center gap-2">
            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email" class="text-gray-700 font-semibold" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] transition-colors placeholder:text-gray-400"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="text-gray-700 font-semibold" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] transition-colors placeholder:text-gray-400"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between mt-2">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <Checkbox 
                        name="remember" 
                        v-model:checked="form.remember" 
                        class="rounded border-gray-300 text-[#133e5e] shadow-sm focus:ring-[#133e5e]"
                    />
                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
                        Remember me
                    </span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-[#133e5e] hover:text-sky-600 transition-colors focus:outline-none focus:underline"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-3 text-sm font-bold rounded-lg shadow-md hover:shadow-lg transition-all"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    {{ form.processing ? 'Signing in...' : 'Sign in' }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>