<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Chat
 * 
 * @property int $id
 * @property int $user_id
 * @property int $chat_status_id
 * 
 * @property User $user
 * @property ChatStatus $chatStatus
 * @property Collection|Message $messages
 * 
 * @package App\Models
 */

class Chat extends Model {

    protected $table = "chats";
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'chat_status_id'
    ];

    protected $with = ['user', 'chatStatus'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chatStatus() {
        return $this->hasOne(ChatStatus::class, 'chat_status_id');
    }

    public function messages() {
        return $this->hasMany(Message::class, 'chat_id');
    }
}
