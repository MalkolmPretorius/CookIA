<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $fillable = ['user_id', 'conversation_id', 'message', 'sender'];
    
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
