export const statusLabel = (s) => ({
    new: 'New',
    assigned: 'Assigned',
    in_progress: 'In progress',
    done: 'Done',
    canceled: 'Canceled',
}[s] ?? s)
