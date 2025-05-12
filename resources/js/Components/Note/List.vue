<template>
    <div
        v-for="note in notes"
        :key="note.id"
        class="bg-white shadow-sm sm:rounded-lg p-6"
    >
        <p class="text-gray-800 whitespace-pre-line">{{ note.content }}</p>
        <small class="text-gray-500 block mt-1">
            Criado em: {{ formatDate(note.created_at) }}
        </small>
        <div class="mt-2 space-x-4">
            <button
                @click="editNote(note.id)"
                class="text-blue-600 hover:underline"
            >
                Editar
            </button>
            <button
                @click="deleteNote(note.id)"
                class="text-red-600 hover:underline"
            >
                Excluir
            </button>
        </div>
    </div>
</template>
<script setup>
import { Inertia } from "@inertiajs/inertia";
defineProps({ notes: Array });
const editNote = (id) => Inertia.get(`/notes/${id}/edit`);
const deleteNote = (id) => {
    if (confirm("Excluir esta nota?")) Inertia.delete(`/notes/${id}`);
};
const formatDate = (dateStr) => new Date(dateStr).toLocaleString("pt-BR");
</script>
