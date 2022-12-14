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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function create($request)
    {
        //userId
        //isGroup:false
        $authUser = Auth::user();
        $contactUser = User::findOrFail($request->userId);
        $authUserId = $authUser->id;
        $contact = Contact::create([
            'user_id' => $authUserId,
            'contact_id' => $request->userId,
        ]);


        $contact->save();


        $authUserDialogs= User::find($authUserId)->getNotGroupDialogs();
        // UserDialog::where('user_id', Auth::user()->id)->get();
        //есть ли у пользователя не групповой диалог с этим контактом

        $contactsDialog = null;
        foreach ($authUserDialogs as $dialog) {
           $contactsDialog = $contactUser->isDialogExistInNotGroupDialogs($dialog->id);

        }
        $dialog = null;
        if (!$contactsDialog) {
            $dialog = Dialog::create();
            $dialog->isGroup = $request->isGroup;
            $userDialogRelations = UserDialog::create([
                'user_id' => $authUserId,
                'dialog_id' => $dialog->id
            ]);
            $contactDialogRelations = UserDialog::create([
                'user_id' => $contact->contact_id,
                'dialog_id' => $dialog->id
            ]);
            $dialog->save();
            $userDialogRelations->save();
            $contactDialogRelations->save();
        }

        return response([
            'resultCode' => 1,
            'newContact' => User::find($request->userId),
            'contactsDialog' => $contactsDialog,
            'newDialog' => $dialog,


        ]);
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public static function destroy($userId)
    {
        $authUserId = Auth::user()->id;

        // UserDialog::where('user_id', $authUserId)->where('dialog_id', );
        $contact = Contact::where('user_id', $authUserId)->where('contact_id', $userId)->first();
        $contact->delete();

        return response([
            'resultCode' => 1,


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
