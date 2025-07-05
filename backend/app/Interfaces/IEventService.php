<?php

namespace App\Interfaces;

use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface IEventService {
  public function getUserEvents(int $user_id): Paginator;
  public function create(array $validated_data, int $user_di): void;
}
