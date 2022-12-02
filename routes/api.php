<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DialogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Models\Dialog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;


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


    ///////////////USERS

    Route::get('/user', function (Request $request) {
        //$request->user()
        return UserController::getUser($request);
    });

    Route::get('find-user/{name}', function ($name) {
        //name
        return UserController::findUser($name);
    });
    Route::get('/users', function (Request $request) {
        //`users?page=${currentPage}&count=${pageSize}`

        return UserController::getUsers($request);
    });

    Route::post('/contact', function (Request $request) {
        //userId
        //isGroup:false
        return ContactController::create($request);
    });

    Route::put('name', function (Request $request) {
        //name
        return UserController::updateName($request->name);
    });

    Route::put('sound-user', function (Request $request) {
        //$isSound
        UserController::updateSound($request->isSound);

    });
    Route::delete('contact/{userId}', function ($userId) {
        return  ContactController::destroy($userId);
    });



    //DIALOGS

    Route::get('dialogs', function () {
        //
        return UserController::getDialogs();
    });

    Route::get('dialog/{dialogId}', function ($dialogId) {
        //dialogId
        return DialogController::getDialog($dialogId);
    });

    Route::post('group-dialog', function (Request $request) {
        //$users, $dialogsName, $isGroup, id?=null if null->add else -> edit
        return DialogController::addGroupDialog($request, true);
    });

    Route::put('sound-dialog', function (Request $request) {
        //$dialogId, $isSound
        return DialogController::updateSound($request->dialogId, $request->isSound);
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


});
