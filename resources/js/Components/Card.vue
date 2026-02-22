<script setup>
import { computed } from 'vue'

import Dropdown from "@/Components/Dropdown.vue";
import DropdownButton from "@/Components/DropdownButton.vue";

const props = defineProps({
    ticket: { type: Object, required: true },
    view: { type: String, required: true }, // dispatcher | technician
    technicians: { type: Array, default: () => [] },
    assignTo: { type: Object, required: true },
})

const emit = defineEmits([
    'assign',
    'cancel',
    'take',
    'complete',
])

const isDispatcher = computed(() => props.view === 'dispatcher')
const isTechnician = computed(() => props.view === 'technician')

const statusLabel = (s) => ({
    new: 'New',
    assigned: 'Assigned',
    in_progress: 'In progress',
    done: 'Done',
    canceled: 'Canceled',
}[s] ?? s)
</script>

<template>
    <div class="rounded-lg border bg-white p-4 space-y-3">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-sm text-gray-500">Ticket #{{ ticket.id }}</div>
                <div class="font-semibold">{{ ticket.client_name }}</div>
                <div class="text-sm text-gray-600">
                    {{ ticket.phone }} · {{ ticket.address }}
                </div>
            </div>

            <div class="text-sm rounded border px-2 py-1">
                {{ statusLabel(ticket.status) }}
            </div>
        </div>

        <div v-if="ticket.problem_text" class="text-sm text-gray-700 whitespace-pre-wrap">
            {{ ticket.problem_text }}
        </div>

        <div v-if="isDispatcher" class="flex items-center gap-3 pt-2">
            <div class="text-sm text-gray-700">
                Assigned: {{ ticket.assigned_to?.name ?? '—' }}
            </div>

            <Dropdown
                v-if="ticket.status === 'new' && !ticket.assigned_to"
                align="left"
                width="48"
            >
                <template #trigger>
                    <button type="button"
                            class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Assign technician
                        <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </template>

                <template #content>
                    <DropdownButton
                        v-for="tech in technicians"
                        :key="tech.id"
                        @click="emit('assign', { ticketId: ticket.id, techId: tech.id })"
                    >
                        {{ tech.name }}
                    </DropdownButton>
                </template>
            </Dropdown>
            <button
                class="h-9 rounded border px-3 disabled:opacity-50"
                :disabled="ticket.status === 'done' || ticket.status === 'canceled'"
                @click="emit('cancel', ticket)"
            >
                Cancel
            </button>
        </div>

        <div v-else-if="isTechnician" class="flex flex-wrap items-center gap-2 pt-2">
            <button
                class="h-9 rounded bg-black text-white px-3 disabled:opacity-50"
                :disabled="ticket.status !== 'assigned'"
                @click="emit('take', ticket)"
            >
                Take
            </button>

            <button
                class="h-9 rounded border px-3 disabled:opacity-50"
                :disabled="ticket.status !== 'in_progress'"
                @click="emit('complete', ticket)"
            >
                Complete
            </button>
        </div>
    </div>
</template>
