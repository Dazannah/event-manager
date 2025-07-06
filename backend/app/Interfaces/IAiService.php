<?php

namespace App\Interfaces;

use App\Models\Message;

interface IAiService {
    public function handleUserMessage(Message $user_message): void;
}
