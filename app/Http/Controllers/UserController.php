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
        $users = [];
        // $allDialogsUsers = [];
        foreach ($dialogs as $dialog) {
            $dialogId = $dialog->id;
            $dialogsUsers = $dialog->users;
            // array_push($allDialogsUsers, $dialogsUsers);
            $dialogsMessages = $dialog->messages;
            foreach ($dialogsUsers as $dialogsUser) {
                if ($dialogsUser->id !== $user->id) {
                    array_push($users, ['dialogId'=>$dialogId, 'dialogsUser' => $dialogsUser, 'dialogsMessages' => new MessageCollection($dialogsMessages) ]);
                }
            }
        }
        return response([
            'resultCode' => 1,
            'dialogs' => array_reverse($users),
            'authUser' => Auth::user(),

        ]);
    }
}
