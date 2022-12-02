<?php

namespace App\Http\Controllers;

use App\Http\Resources\DialogResource;
use App\Http\Resources\MessageCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public static function getUsers($request)
    {
        $resultCode = 0;
        $authUser = Auth::user();
        $touchUser = User::find($authUser->id);
        $touchUser->touch();

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

    public static function getUser($request)
    {
        $authUser = Auth::user();
        $touchUser = User::find($authUser->id);
        $touchUser->touch();

        return response([
            'resultCode' => 1,
            'user' =>  $request->user()
        ]);
    }

    public static function findUser($name)
    {
        $users = User::where('name', 'LIKE', "%{$name}%")->get();
        $collection = new UserCollection($users);

        if ($users) {
            return response(['searchingUsers' => $collection]);
        }
    }

    public static function getDialogs()
    {
        $user = Auth::user();
        $touchUser = User::find($user->id);
        $touchUser->touch();
        $dialogs = $user->dialogs;
        $resultDialogs = [];
        $resultGroupDialogs = [];

        foreach ($dialogs as $dialog) {

            if (!$dialog->isGroup) {
                $resultDialog = new DialogResource($dialog);
                array_push($resultDialogs, $resultDialog);
            } else {
                $resultDialog = new DialogResource($dialog);
                array_push($resultGroupDialogs, $resultDialog);
            };
        }
        return response([
            'resultCode' => 1,
            'dialogs' => array_reverse($resultDialogs),
            'groupDialogs' => array_reverse($resultGroupDialogs),


        ]);
    }

    public static function updateName($name)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $updatingUser = $user->updateName($name);
        return response([
            'resultCode' => 1,
            'updatingUser' => $updatingUser
        ]);
    }

    public static function updateSound($isSound)
    {
        $authUserId = Auth::user()->id;
        $user = User::find($authUserId);
        if ($user->isSound != $isSound) {
            $user->isSound = $isSound;
            $user->save();
        }

        $updatingUser = new UserResource($user);
        return response([
            'resultCode' => 1,
            'updatingUser' => $updatingUser,

        ]);
    }
}
