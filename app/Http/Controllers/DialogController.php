<?php

namespace App\Http\Controllers;

use App\Http\Resources\DialogResource;
use App\Http\Resources\MessageCollection;
use App\Models\Contact;
use App\Models\Dialog;
use App\Models\Message;
use App\Models\UserDialog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DialogController extends Controller
{
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dialog  $dialog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dialog $dialog)
    {
        $dialogsUsers = $dialog->users;
        $messages = $dialog->messages;
        $contactIds = [];
        Message::destroy($messages);

        $authUserId = Auth::user()->id;
        $dialogId = $dialog->id;
        $dialogsRelations = UserDialog::where('dialog_id', $dialogId)->get();
        foreach ($dialogsRelations as $relation) {
            $relation->delete();
        }

        if (!$dialog->isGroup) {
            array_push($contactIds, $authUserId);
            $dialogsUsers = $dialog->users;
            foreach ($dialogsUsers as $user) {
                if ($user->id !== $authUserId) {
                    array_push($contactIds, $user->id);
                    $contactsAuth = Contact::where('user_id', $authUserId)->where('contact_id', $user->id)->get();
                    $contactsUser = Contact::where('user_id', $user->id)->where('contact_id', $authUserId)->get();

                    foreach ($contactsAuth as $contact) {
                        $contact->delete();
                    }
                    foreach ($contactsUser as $contact) {
                        $contact->delete();
                    }
                }
            }
        }

        $dialog->delete();
        return response([
            'resultCode' => 1,
            'deletedDialogId' => $dialogId,
            'deletedContactsIds' => $contactIds,


        ]);
    }

    public static function getMessages(Dialog $dialog)
    {
        $messages = $dialog->messages->reverse();
        $messgesCollection = new MessageCollection($messages);
        return $messgesCollection;
    }

    public static function addGroupDialog($request, $isGroup)
    {
        //TODO:  fixed to addDialog if Group if Else
        //$request: users, dialogsName  $isGroup
        if (count($request->users) < 1) {
            return response([
                'resultCode' => 0,
                'message' => 'no users!'
            ]);
        }
        if ($request->dialogsName == '' && $isGroup) {
            return response([
                'resultCode' => 0,
                'message' => 'no name!'
            ]);
        }

        $authUser = Auth::user();
        if (!$request->dialogId) { //если с фронта не передан ID диалога, значит диалог создается
            $dialog = Dialog::create();
            $dialog->isGroup = $isGroup;
            if ($isGroup) {
                $dialog->name = $request->dialogsName;
            }

            $dialog->save();
            $resultDialog = new DialogResource($dialog);
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
                'createdDialog' => $resultDialog,
                'editedDialog' => null


            ]);
        } else { //если ID пришёл, значит надо отредактировать диалог
            $dialog = Dialog::findOrFail($request->dialogId);
            if ($dialog) {
                $dialog->name = $request->dialogsName;
                $oldUsers = $dialog->users;
                foreach ($oldUsers as $oldUser) {
                    $isOldUserInNewUsers = false;
                    if ($oldUser['id'] != $authUser->id) {
                        foreach ($request->users as $newUser) {
                            if ($oldUser['id'] != $newUser['id']) {
                                $isOldUserInNewUsers = true;
                            }
                        }
                        if (!$isOldUserInNewUsers) {
                            $oldRelations = UserDialog::where('user_id', $oldUser['id'])->where('dialog_id', $request->dialogId)->get();
                            foreach ($oldRelations as  $oldRelation) {
                                $oldRelation->delete();
                            }
                        }
                    }
                }
                foreach ($request->users as $newUser) {
                    $isOldUserInNewUsers = false;
                    if ($newUser['id'] != $authUser->id) {
                        foreach ($oldUsers as $oldUser) {
                            if ($oldUser['id'] != $newUser['id']) {
                                $isOldUserInNewUsers = true;
                            }
                        }
                        if (!$isOldUserInNewUsers) {
                            $newRelation = UserDialog::create([
                                'user_id' => $newUser['id'],
                                'dialog_id' => $request->dialogId
                            ])->save();

                            // foreach ($oldRelations as  $oldRelation) {
                            //     $oldRelation->delete();
                            // }
                        }
                    }
                }
                $dialog->save();
                $resultDialog = new DialogResource($dialog);
                return response([
                    'resultCode' => 1,
                    'createdDialog' =>  null,
                    'editedDialog' => $resultDialog


                ]);
            }
        }
    }
    public static function getDialog($dialogId)
    {

        $resultCode = 1;
        $message = '';

        $dialog = Dialog::findOrFail($dialogId);
        if (!$dialog) {
            $resultCode = 0;
            $message = 'dialog not found!';
        };

        return response([
            'resultCode' => $resultCode,
            'dialog' => new DialogResource($dialog),
            'message' => $message,

        ]);
    }

    public static function updateSound($dialogId, $isSound)
    {
        $authUserId = Auth::user()->id;
        $relation = UserDialog::where('user_id', $authUserId)->where('dialog_id', $request->dialogId)->first();

        $relation->isSound = $isSound;
        $relation->save();

        $dialog = Dialog::find($dialogId);
        $resultDialog = new DialogResource($dialog);

        return response([
            'resultCode' => 1,
            'updatingDialog' => $resultDialog,

        ]);
    }
}
