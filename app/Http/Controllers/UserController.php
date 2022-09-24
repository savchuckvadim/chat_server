<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public static function getUsers($request){
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
}
