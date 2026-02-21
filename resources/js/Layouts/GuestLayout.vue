<script setup>
import {ref} from 'vue'
import {Link} from '@inertiajs/vue3'

import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

const showingNavigationDropdown = ref(false)
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white mb-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex items-center">
                            <Link :href="route('tickets.index')" class="text-sm font-semibold text-gray-800">
                                Tickets
                            </Link>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                            >
                                Log in
                            </Link>

                        </div>

                    </div>
                </div>

                <div class="sm:hidden"
                     :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                    }">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('tickets.index')" :active="route().current('tickets.index')">
                            Tickets
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('login')" :active="route().current('login')">
                            Log in
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <main>
                <slot/>
            </main>
        </div>
    </div>
</template>
