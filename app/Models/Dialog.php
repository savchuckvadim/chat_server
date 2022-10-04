<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dialog extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_dialogs', 'dialog_id', 'user_id');
    }
    public function messages(){
        return $this->hasMany(Message::class);
    }
}
