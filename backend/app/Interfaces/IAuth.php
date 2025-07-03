<?php

namespace App\Interfaces;

interface IAuth {
    public function tryLogin($validated_data);
}
