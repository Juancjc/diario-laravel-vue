<?php
namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = auth()->user()->notes()->latest()->get();
        return inertia('Notes/Index', ['notes' => $notes]);
    }

    public function store(Request $request)
    {
        $request->validate(['content' => 'required']);
        auth()->user()->notes()->create(['content' => $request->content]);
        return redirect()->route('notes.index');
    }

    public function edit(Note $note)
    {
        $this->authorize('update', $note);
        return inertia('Notes/Edit', ['note' => $note]);
    }

    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);
        $note->update(['content' => $request->content]);
        return redirect()->route('notes.index');
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index');
    }
}
