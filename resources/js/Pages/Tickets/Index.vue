<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, reactive } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import Card from "@/Components/Card.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownButton from "@/Components/DropdownButton.vue";

const page = usePage()

const props = defineProps({
    view: { type: String, required: true },
    tickets: { type: Object, default: null },

    filters: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    technicians: { type: Array, default: () => [] },
})

console.log()

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
const onAssign = ({ ticketId, techId }) => {
    router.patch(
        route('tickets.update', { ticket: ticketId }),
        { assigned_to: techId },
        { preserveScroll: true }
    )
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

</script>

<template>
    <Head title="Tickets" />

    <GuestLayout v-if="view === 'guest'">
        <div class="text-center space-y-6">
            <h1 class="text-3xl font-semibold">Repair Ticket System</h1>
            <p class="text-gray-600">Create a ticket.</p>
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
                <Dropdown align="left" width="48">
                    <template #trigger>
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            {{ statusLabel(filters?.status || 'all') }}
                            <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>

                    <template #content>
                        <DropdownButton @click="router.get(route('tickets.index'), { status: undefined }, { preserveState: true, preserveScroll: true })"                        >
                            All
                        </DropdownButton>
                        <DropdownButton
                            v-for="s in statuses"
                            :key="s"
                            @click="router.get(route('tickets.index'), { status: s }, { preserveState: true, preserveScroll: true })"
                        >
                            {{ statusLabel(s) }}
                        </DropdownButton>
                    </template>
                </Dropdown>
            </div>

            <div v-if="isDispatcher || isTechnician" class="grid grid-cols-1 gap-4">
                <Card
                    v-for="t in rows"
                    :key="t.id"
                    :ticket="t"
                    :view="view"
                    :technicians="technicians"
                    :assignTo="assignTo"
                    @assign="onAssign"
                    @cancel="cancel"
                    @take="take"
                    @complete="complete"
                />

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
