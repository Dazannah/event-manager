<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * 
 * @property int $id
 * @property string $text
 * @property int $user_id
 * @property int $helpdesk_id
 * 
 * @property User $user
 * @property User $helpdesk
 * 
 * @package App\Models
 */

class Message extends Model {
    protected $table = "messages";
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'text',
        'user_id',
        'helpdesk_id'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function helpdesk() {
        return $this->belongsTo(User::class, 'helpdesk_id');
    }
}
