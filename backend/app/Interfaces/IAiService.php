<?php

namespace App\Interfaces;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

interface IAiService {
    public function handleUserMessage(Message $user_message, Collection $events): void;
}
