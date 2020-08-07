<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use App\User;
use Illuminate\Support\Facades\DB;

class JwtAuth {

    public $key;

    public function __construct() {
        $this->key = "clave_secreta_del_token-444558798";
    }

    public function signUp($email, $password, $getToken = false) {
        $user = User::where([
                    'email' => $email,
                    'password' => $password
                ])->first();

        $signUp = false;
        if (is_object($user)) {
            $signUp = true;
        }
        if ($signUp) {
            //crear token
            $token = array(
                'sub' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'image' => $user->image,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $jwt_decoded = JWT::decode($jwt, $this->key, ['HS256']);

            if ($getToken == false) {
                $data = $jwt;
            } else {
                $data = $jwt_decoded;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'No se encontro el usuario',
                'code' => 404
            );
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;
        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $ex) {
            $auth = false;
        } catch (\DomainException $ex) {
            $auth = false;
        }
        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }

}
