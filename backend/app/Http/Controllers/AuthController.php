<?php

namespace App\Http\Controllers;

use Exception;
use App\Interfaces\IAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    private IAuthService $authService;

    function __construct(IAuthService $authService) {
        $this->authService = $authService;
    }

    function login(Request $req) {
        try {
            $validated_data = $req->validate(
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($response_data = $this->authService->tryLogin($validated_data))
                return response()->json($response_data);

            return response()->json(["error" => ["email" => ["Invalid credentials."]]]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => [$err->getMessage()]]
            );
        }
    }

    function forgot_password(Request $req) {
        try {
            $req->validate(
                [
                    'email' => 'required|email',
                ]
            );

            $reset_response = Password::sendResetLink(
                $req->only('email')
            );

            $response = ["success" => ["Reset link successfully sent in email."]];
            if (Password::RESET_THROTTLED === $reset_response) $response = ["error" => ["Too many request."]];

            return response()->json($response);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => [$err->getMessage()]]
            );
        }
    }

    function reset_password(Request $req) {
        try {
            $validated_data = $req->validate(
                [
                    'email' => 'required|email',
                    'password' => 'required',
                    'passwordConfirm' => 'required|same:password',
                    'token' => 'required'
                ]
            );

            if ($this->authService->reset_password($validated_data))
                return response()->json(["success" => ["Pasword set successfully"]]);

            return response()->json(["error" => ["Something went wrong."]]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => [$err->getMessage()]]
            );
        }
    }
}
