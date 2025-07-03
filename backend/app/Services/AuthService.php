<?php

namespace App\Services;

use App\Models\User;
use App\Interfaces\IAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService implements IAuth {
  function tryLogin($validated_data): array|bool {
    if (!$user = User::where("email", "=", $validated_data['email'])->first())
      $this->invalidCredentialsError();

    $is_login_success = Hash::check($validated_data['password'], $user->password);

    if (!$is_login_success)
      $this->invalidCredentialsError();


    $token = $user->createToken('token')->accessToken;

    return ["token" => $token];
  }

  function invalidCredentialsError() {
    throw ValidationException::withMessages([
      "email" => ["Invalid credentials."]
    ]);
  }
}
