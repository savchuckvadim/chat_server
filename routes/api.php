<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DialogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Models\Dialog;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
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
});
