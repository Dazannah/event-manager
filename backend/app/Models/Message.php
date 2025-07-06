<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * 
 * @property int $id
 * @property string $text
 * @property int $user_id
 * @property int $chat_id
 * 
 * @property User $user
 * 
 * @package App\Models
 */

class Message extends Model {
    protected $table = "messages";
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'text',
        'user_id',
        'chat_id'
    ];

    protected $with = ['user'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
