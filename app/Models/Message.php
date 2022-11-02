<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'body',
        'author_id',
    ];

    public function dialog()
    {
        return $this->belongsTo(Dialog::class);
    }

    public function usersOfDialog()
    {
        $dialog = $this->dialog;
        $users = $dialog->users;
        return $users;
    }
    // public function users()
    // {
    //     return $this->hasManyThrough(User::class, UserDialog::class);
    // }

    public function recipients()
    {
        $recipients = [];

        foreach ($this->usersOfDialog() as $user) {
            if ($this->author_id !== $user->id) {
                array_push($recipients, $user);
            }
        }

        return $recipients;
    }
}
