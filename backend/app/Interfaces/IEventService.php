<?php

namespace App\Interfaces;

interface IEventService {
  public function create(array $validated_data, int $user_di): void;
}
