<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, reactive } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const page = usePage()

const props = defineProps({
    view: { type: String, required: true },
    tickets: { type: Object, default: null },

    filters: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    technicians: { type: Array, default: () => [] },
})

console.log(props)

const isDispatcher = computed(() => props.view === 'dispatcher')
const isTechnician = computed(() => props.view === 'technician')
const rows = computed(() => props.tickets?.data ?? [])

const assignTo = reactive({})
const patch = (id, payload) => {
    router.patch(
        route('tickets.update', { ticket: id }),
        payload,
        { preserveScroll: true }
    )
}

const assign = (t) => {
    const techId = assignTo[t.id]
    if (!techId) return
    patch(t.id, { assigned_to: techId })
}

const cancel = (t) => patch(t.id, { status: 'canceled' })
const take = (t) => patch(t.id, { status: 'in_progress' })
const complete = (t) => patch(t.id, { status: 'done' })

const applyStatusFilter = (e) => {
    router.get(
        route('tickets.index'),
        { status: e.target.value || undefined },
        { preserveState: true, preserveScroll: true }
    )
}

const go = (url) => {
    if (!url) return
    router.visit(url, { preserveState: true, preserveScroll: true })
}

const statusLabel = (s) => ({
    new: 'New',
    assigned: 'Assigned',
    in_progress: 'In progress',
    done: 'Done',
    canceled: 'Canceled',
}[s] ?? s)

console.log('first row', rows.value?.[0])
</script>

<template>
    <Head title="Tickets" />

    <GuestLayout v-if="view === 'guest'">
        <div class="text-center space-y-6">
            <h1 class="text-3xl font-semibold">Support</h1>
            <p class="text-gray-600">Create a ticket and we will contact you.</p>
            <Link :href="route('tickets.create')" class="inline-block rounded bg-black px-6 py-3 text-white">
                Create ticket
            </Link>
        </div>
    </GuestLayout>

    <AuthenticatedLayout v-else>
        <div class="max-w-6xl mx-auto p-6 space-y-6">

            <div v-if="page.props.flash?.success" class="rounded border p-3 text-sm">
                {{ page.props.flash.success }}
            </div>
            <div v-if="isDispatcher" class="flex items-center gap-3">
                <span class="text-sm text-gray-600">Status:</span>
                <select class="rounded border px-3 py-2" :value="filters?.status ?? ''" @change="applyStatusFilter">
                    <option value="">All</option>
                    <option v-for="s in statuses" :key="s" :value="s">{{ statusLabel(s) }}</option>
                </select>
            </div>

            <div v-if="isDispatcher || isTechnician" class="grid grid-cols-1 gap-4">
                <div
                    v-for="t in rows"
                    :key="t.id"
                    class="rounded-lg border bg-white p-4 space-y-3"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-sm text-gray-500">Ticket #{{ t.id }}</div>
                            <div class="font-semibold truncate">{{ t.client_name }}</div>
                            <div class="text-sm text-gray-600">{{ t.phone }} · {{ t.address }}</div>
                        </div>

                        <div class="text-sm rounded border px-2 py-1 whitespace-nowrap">
                            {{ statusLabel(t.status) }}
                        </div>
                    </div>

                    <div v-if="t.problem_text" class="text-sm text-gray-700 whitespace-pre-wrap">
                        {{ t.problem_text }}
                    </div>
                    <div v-if="isDispatcher" class="flex flex-wrap items-center gap-2 pt-2">
                        <div v-if="t.assigned_to" class="text-sm text-gray-900">
                            Assigned: {{ t.assigned_to.name }}
                        </div>

                        <select
                            v-else
                            class="h-9 w-56 rounded border px-2"
                            v-model.number="assignTo[t.id]"
                            :disabled="t.status !== 'new'"
                        >
                            <option value="" disabled>Select technician</option>
                            <option
                                v-for="tech in technicians"
                                :key="tech.id"
                                :value="tech.id"
                            >
                                {{ tech.name }}
                            </option>
                        </select>

                        <button
                            v-if="!t.assigned_to"
                            class="h-9 rounded bg-black text-white px-3 disabled:opacity-50"
                            :disabled="t.status !== 'new' || !assignTo[t.id]"
                            @click="assign(t)"
                        >
                            Assign
                        </button>

                        <button
                            class="h-9 rounded border px-3 disabled:opacity-50"
                            :disabled="t.status === 'done' || t.status === 'canceled'"
                            @click="cancel(t)"
                        >
                            Cancel
                        </button>
                    </div>

                    <!-- Technician actions -->
                    <div v-else-if="isTechnician" class="flex flex-wrap items-center gap-2 pt-2">
                        <button
                            class="h-9 rounded bg-black text-white px-3 disabled:opacity-50"
                            :disabled="t.status !== 'assigned'"
                            @click="take(t)"
                        >
                            Take
                        </button>

                        <button
                            class="h-9 rounded border px-3 disabled:opacity-50"
                            :disabled="t.status !== 'in_progress'"
                            @click="complete(t)"
                        >
                            Complete
                        </button>
                    </div>
                </div>

                <div v-if="rows.length === 0" class="rounded border bg-white p-4 text-sm">
                    No tickets
                </div>
            </div>

            <div v-if="tickets?.links" class="flex flex-wrap gap-2">
                <button
                    v-for="link in tickets.links"
                    :key="link.label"
                    class="px-3 py-2 border rounded disabled:opacity-50"
                    :disabled="!link.url"
                    v-html="link.label"
                    @click="go(link.url)"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
