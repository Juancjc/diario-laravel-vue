# 📝 Diário de Notas com Laravel 12 + Vue 3 + Inertia.js

Este projeto é uma aplicação de diário simples, onde usuários autenticados podem adicionar, visualizar, editar e excluir notas. Utiliza Laravel 12, Vue 3, Inertia.js e Breeze.

---

## ✅ Requisitos

- PHP 8.2+
- Composer
- Node.js e npm
- SQLite, MySQL ou outro banco compatível
- Laravel Herd (opcional)
- Git (opcional)

---

## 🚀 Instalação

1. Clone o repositório (ou crie o projeto):

   Se for criar do zero:

   ```bash
   composer create-project laravel/laravel diario
   cd diario
   ```

2. Configure o arquivo .env:

   Copie o exemplo:

   ```bash
   cp .env.example .env
   ```

   Gere a key da aplicação:

   ```bash
   php artisan key:generate
   ```

   Configure o banco de dados no .env (por exemplo):

   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```

   (crie o arquivo database/database.sqlite se necessário)

3. Instale o Breeze com Inertia + Vue:

   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install vue
   npm install
   php artisan migrate
   ```

4. Inicie o servidor:

   ```bash
   php artisan serve
   npm run dev
   ```

---

## 📁 Estrutura das Notas

1. Criar Model e Migration
   ```bash
   php artisan make:model Note -m
   ```

2. Crie a migration:

   Em database/migrations:

   ```php
   Schema::create('notes', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained()->onDelete('cascade');
       $table->text('content');
       $table->timestamps();
       $table->softDeletes();
   });
   ```

   Execute:

   ```bash
   php artisan migrate
   ```
3. Crie a Model Note:

   Em app/Models/Note.php:

   ```php
   namespace App\Models;

   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Database\Eloquent\SoftDeletes;
   use Illuminate\Database\Eloquent\Factories\HasFactory;

   class Note extends Model
   {
       use HasFactory, SoftDeletes;

       protected $fillable = ['user_id', 'content'];
   }
   ```

4. Crie o Controller:

   ```bash
   php artisan make:controller NoteController
   ```

   Em app/Http/Controllers/NoteController.php:

   ```php
   namespace App\Http\Controllers;

   use App\Models\Note;
   use Illuminate\Http\Request;
   use Inertia\Inertia;
   use Illuminate\Support\Facades\Auth;

   class NoteController extends Controller
   {
       public function index()
       {
           $notes = Auth::user()->notes()->latest()->get();
           return Inertia::render('Notes/Index', ['notes' => $notes]);
       }

       public function store(Request $request)
       {
           $request->validate(['content' => 'required']);
           Auth::user()->notes()->create($request->only('content'));
           return redirect()->back();
       }

       public function destroy(Note $note)
       {
           $this->authorize('delete', $note);
           $note->delete();
           return redirect()->back();
       }
   }
   ```

4. Relacionamento na Model User:

   Em app/Models/User.php:

   ```php
   public function notes()
   {
       return $this->hasMany(Note::class);
   }
   ```

5. Adicione as rotas:

   Em routes/web.php:

   ```php
   use App\Http\Controllers\NoteController;

   Route::middleware(['auth', 'verified'])->group(function () {
       Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
       Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
       Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
   });
   ```

---


## 🖼️ Componentes Vue (Inertia)

1. resources/js/Pages/Notes/Index.vue

Esse componente usa o layout padrão do Breeze e importa NoteForm e NoteList.

```vue
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
```

2. resources/js/Components/Note/Form.vue

Componente do formulário de criação de nota.

```vue
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
```

3. resources/js/Components/Note/List.vue

Componente para listagem e exclusão de notas.

```vue
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
    if (confirm("Excluir esta nota?")) {
        Inertia.delete(`/notes/${id}`);
    }
};

const formatDate = (dateStr) => new Date(dateStr).toLocaleString("pt-BR");
</script>
```

✅ Pronto! Agora é só rodar php artisan serve, acessar /notes autenticado, e começar a usar 📝
