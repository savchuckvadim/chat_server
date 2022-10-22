<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function dialogs()
    {
        return $this->belongsToMany(Dialog::class, 'user_dialogs', 'user_id', 'dialog_id');
    }

    public function activeDialogs()
    {

        $contacts = $this->contacts;
        $contactsIds = [];

        foreach ($contacts as $contact) {
            array_push($contactsIds, $contact->id);
        };

        return $this->dialogs()->collection()->diff(Dialog::whereIn('user_id', $contactsIds)->get());
    }

    public function canJoinDialog($dialogId)
    {
        $result = false;
        $dialogs = $this->dialogs();
        // foreach ($dialogs as $dialog) {
        //     if ($dialog->id === $dialogId) {
        $result = true;
        //     }
        // }


        return $result;
    }
}
