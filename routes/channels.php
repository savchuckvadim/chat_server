<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // return (int) $user->id === (int) $id;
    return true;
});



Broadcast::channel('chat', function ($user) {
    // if ($user->canJoinRoom($roomId)) {
    // $user = User::find($user->id);
    // $user->isActive = true;
    // $user->save();
    // $resource = new UserResource($user);
    // return
    return $user->id;
    // }
});
Broadcast::channel('dialog.{dialogId}', function ($dialogId) {
    $authUser = Auth::user();
    $user = User::where('id', $authUser->id)->first();
    if ($user->canJoinDialog($dialogId)) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});

Broadcast::channel('new-message.{userId}', function ($user) {

    $authUser = Auth::user();
    $authUserId = $authUser->id;

    return (int) $authUserId === (int) $user->id;
});
