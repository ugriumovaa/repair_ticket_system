<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

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

                <!-- success message -->
                <div
                    v-if="page.props.flash?.success"
                    class="mb-4 rounded-md border border-green-300 bg-green-50 p-3 text-sm text-green-800"
                >
                    {{ page.props.flash.success }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Client name -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Client name</label>
                        <input
                            v-model="form.client_name"
                            type="text"
                            class="w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        />
                        <p v-if="form.errors.client_name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.client_name }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input
                            v-model="form.phone"
                            type="text"
                            class="w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        />
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <input
                            v-model="form.address"
                            type="text"
                            class="w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        />
                        <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">
                            {{ form.errors.address }}
                        </p>
                    </div>

                    <!-- Problem -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Problem description</label>
                        <textarea
                            v-model="form.problem_text"
                            rows="5"
                            class="w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        ></textarea>
                        <p v-if="form.errors.problem_text" class="mt-1 text-sm text-red-600">
                            {{ form.errors.problem_text }}
                        </p>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-black px-5 py-2 text-white disabled:opacity-50"
                        >
                            Create ticket
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </GuestLayout>
</template>
