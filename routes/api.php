<?php

use App\Events\Presence;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DialogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserCollection;
use App\Listeners\PresenceListener;
use App\Models\Dialog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::middleware('auth:sanctum')->group(function () {

    ///////////////TOKENS

    // Route::post('/tokens/create', function (Request $request) {
    //     $token = $request->user()->createToken($request->token_name);

    //     return ['token' => $token->plainTextToken];
    // });
    // Route::post('/sanctum/token', TokenController::class);

    ///////////////USERS

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('find-user/{name}', function ($name) {
        $users = User::where('name', 'LIKE', "%{$name}%")->get();
        $collection = new UserCollection($users);

        if ($users) {
            return response(['searchingUsers' => $collection]);
        }
    });
    Route::get('/users', function (Request $request) {
        return UserController::getUsers($request);
    });

    Route::post('/contact', function (Request $request) {
        return ContactController::create($request);
    });

    Route::delete('contact/{userId}', function ($userId) {
        return  ContactController::destroy($userId);
    });

    Route::get('/dialogs', function () {
        return UserController::getDialogs();
    });

    Route::post('group-dialog', function (Request $request) {
        return DialogController::addGroupDialog($request);
    });
    Route::post('message', function (Request $request) {
        return MessageController::create($request->dialogId, $request->body);
    });


    Route::get('messages/{dialogId}', function ($dialogId) {
        $dialog = Dialog::find($dialogId);
        $messages = null;
        if ($dialog) {
            $messages = DialogController::getMessages($dialog);
            return response([
                'resultCode' => 1,
                'messages' => $messages,

            ]);
        } else {
            return response([
                'resultCode' => 1,
                'messages' => []
            ]);
        }
    });

    Route::get('/testingevent', function () {
        $user = Auth::user();
        Presence::dispatch($user);
        return response([
            'результат' => 'задиспатчилось',
            // 'handle'=> PresenceListener::handle()
        ]);
    });
});
