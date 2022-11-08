<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageCollection;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public static function getUsers($request)
    {
        $resultCode = 0;
        $authUser = Auth::user();

        if ($authUser) {
            $resultCode = 1;
            $itemsCount = $request->query('count');
            $paginate = User::paginate($itemsCount);
            $collection = new UserCollection($paginate);
            return  $collection;
        } else {
            return response([
                'resultCode' => $resultCode,
                'message' => 'auth user is nod defined !'
            ]);
        }
    }

    public static function getDialogs()
    {
        $user = Auth::user();
        $dialogs = $user->dialogs;
        $resultDialogs = [];
        $resultGroupDialogs = [];
        // $allDialogsUsers = [];
        foreach ($dialogs as $dialog) {
            $dialogId = $dialog->id;
            $dialogsUsers = $dialog->users;
            // array_push($allDialogsUsers, $dialogsUsers);
            $dialogsMessages = $dialog->messages;

            $resultDialogsUsers = [];

            foreach ($dialogsUsers as $dialogsUser) {
                if ($dialogsUser->id !== $user->id) {
                    array_push($resultDialogsUsers, $dialogsUser);
                }
            }

            if (!$dialog->isGroup) {
                array_push($resultDialogs, [
                    'dialogId' => $dialogId,
                    'isGroup' => $dialog->isGroup,
                    'dialogsUsers' => $resultDialogsUsers,
                    'dialogsMessages' => new MessageCollection($dialogsMessages)
                ]);
            } else {
                array_push($resultGroupDialogs, [
                    'dialogId' => $dialogId,
                    'dialogName' => $dialog->name,
                    'isGroup' => $dialog->isGroup,
                    'dialogsUsers' => $resultDialogsUsers,
                    'dialogsMessages' => new MessageCollection($dialogsMessages)
                ]);
            };
        }
        return response([
            'resultCode' => 1,
            'dialogs' => array_reverse($resultDialogs),
            'groupDialogs' => array_reverse($resultGroupDialogs),
            'authUser' => Auth::user(),
            // '$dialogs' => $dialogs,

        ]);
    }
}
