<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Dialog;
use App\Models\User;
use App\Models\UserDialog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
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
    public static function create($request)
    {

        $authUserId = Auth::user()->id;
        $contact = Contact::create([
            'user_id' => $authUserId,
            'contact_id' => $request->userId,
        ]);

        $dialog = Dialog::create();

        $userDialogRelations = UserDialog::create([
            'user_id' => $authUserId,
            'dialog_id' => $dialog->id
        ]);
        $contactDialogRelations = UserDialog::create([
            'user_id' => $contact->id,
            'dialog_id' => $dialog->id
        ]);
        $contact->save();
        $dialog->save();
        $userDialogRelations->save();
        $contactDialogRelations->save();

        return response([
            'resultCode' => 1,
            'newContact' => User::find($request->userId)

        ]);
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
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public static function destroy($request)
    {
        $authUserId = Auth::user()->id;

        // UserDialog::where('user_id', $authUserId)->where('dialog_id', );
        $contact = Contact::where('user_id', $authUserId)->where('contact_id', $request->userId);
        $contact->delete();

        return response([
            'resultCode' => 1,
            'newContact' => User::find($request->userId)

        ]);
    }

    public static function getContacts()
    {
        $user = Auth::user();
        return response([
            'resultCode' => 1,
            'contacts' => $user->contacts
        ]);
    }
}
