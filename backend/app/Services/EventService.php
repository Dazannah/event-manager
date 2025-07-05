<?php

namespace App\Services;

use App\Models\Event;
use App\Interfaces\IEventService;
use Illuminate\Database\Eloquent\Collection;

class EventService implements IEventService {

  function getUserEvents(int $user_id): Collection {
    return Event::where('user_id', '=', $user_id)->get();
  }

  function create(array $validated_data, int $user_id): void {
    $event = new Event([
      'title' => $validated_data['title'],
      'description' => $validated_data['description'] ?? null,
      'user_id' => $user_id
    ]);

    $event->save();
  }
}
