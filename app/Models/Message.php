<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

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

    public function to()
    {
        $dialog = $this->dialog;
        $users = $dialog->users;
        return $users;
    }
}
