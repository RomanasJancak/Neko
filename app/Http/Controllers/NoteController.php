<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        //
    }
    public function getNoteInfo($id)
    {
        $note = Note::find($id);
        if ($note) {
            return response()->json([
                'success' => true,
                'note' => [
                  'id' => $note->id,
                  'content' => $note->content,
                  'created_at' => $note->created_at->toDateTimeString(),
                  'user' => [
                      'id' => $note->user->id,
                      'name' => $note->user->name,
                  ],
                ],
                'previous' => $note->previous() ? $note->previous()->id : null,
                'next' => $note->next() ? $note->next()->id : null,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], 404);
        }
    }
}
