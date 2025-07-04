<?php

namespace App\Http\Controllers;

use Exception;
use App\Interfaces\IAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    private IAuth $auth;

    function __construct(IAuth $auth) {
        $this->auth = $auth;
    }

    function login(Request $req) {
        try {
            $validated_data = $req->validate(
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($response_data = $this->auth->tryLogin($validated_data))
                return response()->json($response_data);

            return response()->json(["error" => ["email" => ["Invalid credentials."]]]);
        } catch (ValidationException $err) {
            return response()->json(["error" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => $err->getMessage()]
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

            Password::sendResetLink(
                $req->only('email')
            );

            return response()->json(["success" => ["Reset link successfully sent in email."]]);
        } catch (ValidationException $err) {
            return response()->json(["error" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => $err->getMessage()]
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

            if ($this->auth->reset_password($validated_data))
                return response()->json(["success" => ["Pasword set successfully"]]);

            return response()->json(["error" => ["Something went wrong."]]);
        } catch (ValidationException $err) {
            return response()->json(["error" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => $err->getMessage()]
            );
        }
    }
}
