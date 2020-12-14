<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class NoteController extends BaseController
{
    public function index()
    {
        $user_id=Auth::id();
        $Notes=Note::all()->where('user_id','=',$user_id);
        if($Notes->count()==0)
        {
             return $this->sendError('There is no notes for this user');
        }
        return $this->sendResponse($Notes, 'Notes retrieved Successfully!');
    }

    public function store(Request $request)
    {
        $input=$request->all();
        $validator = Validator::make($input,[
        'title'=>'required',
        'content'=>'required'
        ]);
        if( $validator->fails()) {
           return $this->sendError('Validate Error',$validator->errors());
        }
        $user=Auth::user();
        $input['user_id']=$user->id;
        $note=Note::create($input);
        return $this->sendResponse($note, 'Note added Successfully!');
    }

    public function show($id)
    {
        $note=Note::find($id);
        if(is_null($note)) {
            return $this->sendError('Note not found');
        }
        if ($note->user_id != Auth::id()) {
            return $this->sendError('You do not have rights to show this note');
        }
        return $this->sendResponse($note, 'Note retrieved Successfully!');
    }

    public function update(Request $request, $id)
    {
        $note=Note::find($id);
        $input = $request->all();
        $validator = Validator::make($input , [
            'title'=>'required',
            'content'=>'required'
        ]  );
        if ($validator->fails()) {
         return $this->sendError('Please validate error' ,$validator->errors() );
        }
        if(is_null($note)) {
            return $this->sendError('Note not found');
        }
        if ($note->user_id != Auth::id()) {
            $i =Auth::id();
            return $this->sendError('You do not have rights to update this note');
        }
        $note->title=$input['title'];
        $note->content=$input['content'];
        $note->save();
        return $this->sendResponse($note, 'Note updated Successfully!');
    }

    public function destroy($id)
    {
        $note=Note::find($id);
        if(is_null($note)) {
            return $this->sendError('Note not found');
        }
        if ($note->user_id != Auth::id()) {
            $i =Auth::id();
            return $this->sendError('You do not have rights to delete this note');
        }
        $note->delete();
        return $this->sendResponse($note, 'Note deleted Successfully!');
    }
}
