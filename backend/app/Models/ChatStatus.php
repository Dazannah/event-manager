<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ChatStatus
 * 
 * @property int $id
 * @property int $technical_name
 * @property int $display_name
 * 
 * @package App\Models
 */

class ChatStatus extends Model {
    protected $table = "chat_statuses";
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'technical_name',
        'display_name'
    ];
}
