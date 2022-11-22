<?php

use App\Events\Presence;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DialogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserCollection;
use App\Listeners\PresenceListener;
use App\Models\Dialog;
use App\Models\Message;
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

    Route::put('name', function (Request $request) {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $updatingUser = $user->updateName($request->name);
        return response([
            'resultCode' => 1,
            'updatingUser' => $updatingUser
        ]);
    });
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
        //userId
        //isGroup:false
        return ContactController::create($request);
    });

    Route::delete('contact/{userId}', function ($userId) {
        return  ContactController::destroy($userId);
    });

    //DIALOGS



    Route::get('dialogs', function () {
        return UserController::getDialogs();
    });

    Route::get('dialog/{dialogId}', function ($dialogId) {
        return DialogController::getDialog($dialogId);
    });
    Route::post('group-dialog', function (Request $request) {
        //$users, $dialogsName, $isGroup, id?=null if null->add else -> edit
        return DialogController::addGroupDialog($request, true);
    });
    Route::delete('dialog/{dialogId}', function ($dialogId) {
        $dialog = Dialog::find($dialogId);
        if ($dialog) {
            $controller = new DialogController($dialog);
            return $controller->destroy($dialog);
        };
        return DialogController::getDialog($dialogId);
    });

    //MESSAGES
    Route::post('message', function (Request $request) {
        //dialogId, body, isForwarded, isEdited
        return MessageController::create($request->dialogId, $request->body, $request->isForwarded, $request->isEdited);
    });
    Route::put('message', function (Request $request) {
        //dialogId, body, isForwarded, isEdited

        return MessageController::edit($request->messageId, $request->body);
    });
    Route::delete('message/{messageId}', function ($messageId) {
        //messageId

        return MessageController::destroy($messageId);
    });

    // TODO: create method in Controller
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

    // Route::post('forward-message', function (Request $request) {
    //     return MessageController::create($request->dialogId, $request->body);
    // });

    Route::get('/testingevent', function () {
        $user = Auth::user();
        Presence::dispatch($user);
        return response([
            'результат' => 'задиспатчилось',
            // 'handle'=> PresenceListener::handle()
        ]);
    });
});
