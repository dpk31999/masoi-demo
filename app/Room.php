<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{   
    protected $fillable = ['number','count','status'];

    public function users()
    {   
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
