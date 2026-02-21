<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

const page = usePage()

const form = useForm({
    client_name: '',
    phone: '',
    address: '',
    problem_text: '',
})

const submit = () => {
    form.post(route('tickets.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Create Ticket" />

    <GuestLayout>
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow-sm border rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-6">Create ticket</h1>

                <div
                    v-if="page.props.flash?.success"
                    class="mb-4 rounded-md border border-green-300 bg-green-50 p-3 text-sm text-green-800"
                >
                    {{ page.props.flash.success }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <InputLabel value="Client name" />
                    <TextInput
                        v-model="form.client_name"
                        type="text"
                        class="block w-full"
                        autocomplete="name"
                    />
                    <InputError :message="form.errors.client_name" />

                    <InputLabel value="Phone" />
                    <TextInput
                        v-model="form.phone"
                        type="text"
                        class="block w-full"
                        autocomplete="tel"
                    />
                    <InputError :message="form.errors.phone" />

                    <InputLabel value="Address" />
                    <TextInput
                        v-model="form.address"
                        type="text"
                        class="block w-full"
                        autocomplete="street-address"
                    />
                    <InputError :message="form.errors.address" />

                    <InputLabel value="Problem description" />
                    <textarea
                        v-model="form.problem_text"
                        rows="5"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError :message="form.errors.problem_text" />

                    <PrimaryButton
                        :disabled="form.processing"
                        class="mt-4"
                    >
                        Create ticket
                    </PrimaryButton>

                </form>            </div>
        </div>
    </GuestLayout>
</template>
