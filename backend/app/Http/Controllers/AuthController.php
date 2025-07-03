<?php

namespace App\Http\Controllers;

use App\Interfaces\IAuth;
use Exception;
use Illuminate\Http\Request;
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
}
