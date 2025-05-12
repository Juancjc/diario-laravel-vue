<template>
    <form @submit.prevent="createNote" class="space-y-4">
        <textarea
            v-model="newNote"
            placeholder="Escreva sua nota..."
            class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-500"
        ></textarea>
        <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
            Adicionar
        </button>
    </form>
</template>
<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
const newNote = ref("");
const createNote = () => {
    Inertia.post("/notes", { content: newNote.value });
    newNote.value = "";
};
</script>
