<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['user_id'];
    
    public function chatHistory()
    {
        return $this->hasMany(ChatHistory::class);
    }
}
