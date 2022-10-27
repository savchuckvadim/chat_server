<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageCollection;
use App\Models\Dialog;
use App\Models\UserDialog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DialogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dialog  $dialog
     * @return \Illuminate\Http\Response
     */
    public function show(Dialog $dialog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dialog  $dialog
     * @return \Illuminate\Http\Response
     */
    public function edit(Dialog $dialog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dialog  $dialog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dialog $dialog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dialog  $dialog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dialog $dialog)
    {
        //
    }

    public static function getMessages(Dialog $dialog)
    {
        $messages = $dialog->messages->reverse();
        $messgesCollection = new MessageCollection($messages);
        return $messgesCollection;
    }

    public static function addGroupDialog($request)
    {
        //users, dialogsName
        if (count($request->users) < 1) {
            return response([
                'resultCode' => 0,
                'message' => 'no users!'
            ]);
        }
        if ($request->dialogsName == '') {
            return response([
                'resultCode' => 0,
                'message' => 'no name!'
            ]);
        }

        $authUser = Auth::user();
        $dialog = Dialog::create();
        $dialog->isGroup = true;
        $dialog->name = $request->dialogsName;
        $dialog->save();

        $authDialogRelations = UserDialog::create([
            'user_id' => $authUser->id,
            'dialog_id' => $dialog->id
        ]);
        $authDialogRelations->save();
        $users = [];
        foreach ($request->users as $user) {
            array_push($users, $user);
               UserDialog::create([
                    'user_id' => $user['id'],
                    'dialog_id' => $dialog->id
                ])->save();
        }
        return response([
            'resultCode' => 1,
            'createdDialog' => $dialog,
            

        ]);
    }
}
