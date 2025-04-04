<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoiceChatEvent extends Model
{
    protected $fillable = ["user_id", "action"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
