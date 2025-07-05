<?php

namespace App\Interfaces;

interface IAuthService {
    public function tryLogin($validated_data);
    public function reset_password($validated_data);
}
