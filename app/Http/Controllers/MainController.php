<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Services\Operations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use League\Uri\UriTemplate\Operator;

class MainController extends Controller
{
    public function index()
    {
        // load users notes
        $id = session('user.id');
        $notes = User::find($id)
            ->notes()
            ->whereNull('deleted_at')
            ->get()
            ->toArray();

        return view('home', [
            'notes' => $notes
        ]);
    }

    public function newNote()
    {
        // show new note view
        return view('new_note');
    }

    public function newNoteSubmit(Request $request)
    {
        // validate request
        $request->validate(

            // Rules 
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            // Messages
            [
                'text_title.required' => "O titulo é obrigatorio",
                'text_title.min' => "O titulo deve conter pelo menos :min caracteres",
                'text_title.max' => "O titulo não pode ultrapassar :max caracteres",

                'text_note.required' => "A nota é obrigatoria",
                'text_note.min' => "A nota deve conter pelo menos :min caracteres",
                'text_note.max' => "A nota não pode ultrapassar de :max caracteres",
            ]
        );

        // get user id 
        $id = session('user.id');
        // create new note
        $note = new Note();
        $note->user_id = $id;
        $note->title = $request->text_title;
        $note->text = $request->text_note;

        $note->save();
        // redirect

        return redirect()->route('home');
    }

    public function editNote($id)
    {
        // $id = $this->decryptId($id);
        $id = Operations::decrypyId($id);

        // load note
        $note = Note::find($id);


        return view('edit_note', ['note' => $note]);
    }

    public function editNoteSubmit(Request $request)
    {
        //validate request
        $request->validate(

            // Rules 
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            // Messages
            [
                'text_title.required' => "O titulo é obrigatorio",
                'text_title.min' => "O titulo deve conter pelo menos :min caracteres",
                'text_title.max' => "O titulo não pode ultrapassar :max caracteres",

                'text_note.required' => "A nota é obrigatoria",
                'text_note.min' => "A nota deve conter pelo menos :min caracteres",
                'text_note.max' => "A nota não pode ultrapassar de :max caracteres",
            ]
        );
        
        // Check if note id exists

        if($request->note_id == null){
            return redirect()->route('home');
        }
        
        // decrypt note id 

        $id = Operations::decrypyId($request->note_id);


        // load note 

        $note = Note::find($id);

        // update note

        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();

        // redirect to home

        return redirect()->route('home');
    }

    public function deleteNote($id)
    {
        // $id = $this->decryptId($id);
        $id = Operations::decrypyId($id);

        // load note

        $note = Note::find($id);

        // show delete note confirmation

        return view('delete_note', ['note' => $note]); 
    }

    public function deleteNoteConfirm(Request $request){
        $id = Operations::decrypyId($request->id);

        // load note

        $note = Note::find($id);

        // hard delete 

        // $note->delete();

        // soft delete 

        // $note->deleted_at = date('Y:m:d H:i:s');
        // $note->save();

        // Soft delete using traits

        $note->delete();

        // Hard delete using traits on model

        // $note->forceDelete();

        // redirecto to home
    
        return redirect()->route('home');
    }
}
