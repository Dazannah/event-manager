<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('helpdesk', function ($user) {
    return $user->isHelpdeskAgent;
});

Broadcast::routes(['middleware' => ['auth:api']]);
