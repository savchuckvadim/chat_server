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

            $dialogsMessages = $dialog->messages;

            $resultDialogsUsers = [];



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
            // 'authUser' => Auth::user(),
            // '$dialogs' => $dialogs,

        ]);
    }
}
