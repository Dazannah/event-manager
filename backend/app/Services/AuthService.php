<?php

namespace App\Services;

use App\Models\User;
use App\Interfaces\IAuthService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class AuthService implements IAuthService {
  function reset_password($validated_data): bool {
    $result = Password::reset(
      [
        'email' => $validated_data['email'],
        'token' => $validated_data['token'],
        'password' => $validated_data['password'],
        'password_confirmation' => $validated_data['passwordConfirm'],
      ],
      function (User $user, string $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
      }
    );

    if ($result === "passwords.reset")
      return true;

    if ($result === "passwords.token")
      throw ValidationException::withMessages([
        "token" => ["Invalid token."]
      ]);

    return false;
  }

  function tryLogin($validated_data): array|bool {
    if (!$user = User::where("email", "=", $validated_data['email'])->first())
      $this->invalidCredentialsError();

    $is_login_success = Hash::check($validated_data['password'], $user->password);

    if (!$is_login_success)
      $this->invalidCredentialsError();

    $authLevels = $user->isHelpdeskAgent ? 'helpdeskAgent' : null;

    $token = $user->createToken('token', [$authLevels])->accessToken;

    return ["token" => $token];
  }

  function invalidCredentialsError() {
    throw ValidationException::withMessages([
      "email" => ["Invalid credentials."]
    ]);
  }
}
