<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{chat_id}', function ($user, $chat_id) {
    $chat = Chat::where('id', '=', $chat_id)->first();

    return (int) $user->id === (int) $chat->user_id;
});

Broadcast::channel('helpdesk.{chat_id}', function ($user) {
    return $user->isHelpdeskAgent;
});

Broadcast::routes(['middleware' => ['auth:api']]);
