<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface IEventService {
  public function getUserEvents(int $user_id): Collection;
  public function create(array $validated_data, int $user_di): void;
}
