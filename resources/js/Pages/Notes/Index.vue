<template>
    <AuthenticatedLayout>
        <Head title="Minhas Notas" />
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Minhas Notas
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <NoteForm />
                </div>

                <NoteList :notes="notes" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import NoteForm from "@/Components/Note/Form.vue";
import NoteList from "@/Components/Note/List.vue";
defineProps({ notes: Array });
</script>
