<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showSuccessAlert = ref(false);

const form = useForm({
    firstname: '',
    middlename: '',
    lastname: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    // Hide alert if they are resubmitting
    showSuccessAlert.value = false;

    form.post(route('register'), {
        onSuccess: () => {
            // Show the success notification
            showSuccessAlert.value = true;
            
            // Clear the form fields upon successful registration
            form.reset();
        },
        onFinish: () => {
            // Only reset passwords if there was an error, so they have to type them again
            if (form.hasErrors) {
                form.reset('password', 'password_confirmation');
            }
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                Create an account
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                Join us to start scheduling your events easily.
            </p>
        </div>

        <div v-if="showSuccessAlert" class="mb-6 rounded-lg bg-green-50 p-4 text-sm font-medium text-green-700 border border-green-200 shadow-sm flex items-start gap-3">
            <svg class="h-5 w-5 text-green-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold">Account created successfully!</p>
                <p class="mt-1 text-green-600">Please <Link :href="route('login')" class="underline hover:text-green-800">log in</Link> to access your dashboard.</p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            
            <div>
                <InputLabel for="firstname" value="First Name" class="text-gray-700 font-semibold" />
                <TextInput
                    id="firstname"
                    type="text"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] transition-colors placeholder:text-gray-400"
                    v-model="form.firstname"
                    required
                    autofocus
                    autocomplete="given-name"
                    placeholder="e.g. Jane"
                />
                <InputError class="mt-2" :message="form.errors.firstname" />
            </div>

            <div>
                <InputLabel for="middlename" value="Middle Name (Optional)" class="text-gray-700 font-semibold" />
                <TextInput
                    id="middlename"
                    type="text"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] transition-colors placeholder:text-gray-400"
                    v-model="form.middlename"
                    autocomplete="additional-name"
                    placeholder="e.g. Smith"
                />
                <InputError class="mt-2" :message="form.errors.middlename" />
            </div>

            <div>
                <InputLabel for="lastname" value="Last Name" class="text-gray-700 font-semibold" />
                <TextInput
                    id="lastname"
                    type="text"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] transition-colors placeholder:text-gray-400"
                    v-model="form.lastname"
                    required
                    autocomplete="family-name"
                    placeholder="e.g. Doe"
                />
                <InputError class="mt-2" :message="form.errors.lastname" />
            </div>
            
            <div>
                <InputLabel for="email" value="Email" class="text-gray-700 font-semibold" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] transition-colors placeholder:text-gray-400"
                    v-model="form.email"
                    required
                    autocomplete="email"
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
                    autocomplete="new-password"
                    placeholder="Create a strong password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-gray-700 font-semibold" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#133e5e] focus:ring-[#133e5e] transition-colors placeholder:text-gray-400"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm your password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full justify-center py-3 text-sm font-bold rounded-lg shadow-md hover:shadow-lg transition-all"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    {{ form.processing ? 'Creating account...' : 'Register' }}
                </PrimaryButton>
            </div>

            <p class="mt-6 text-center text-sm text-gray-600">
                Already have an account?
                <Link
                    :href="route('login')"
                    class="font-medium text-[#133e5e] hover:text-sky-600 transition-colors focus:outline-none focus:underline"
                >
                    Sign in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>