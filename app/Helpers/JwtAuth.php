<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class JwtAuth {

    private $key;

    public function __construct() {
        $this->key = "tzOJT&mfCjKDo9XIT9GRqnx&EahiYI3uLYw8hnWfGYekiHKFdw";
    }
    
    public function signup($email, $password, $getToken = null){
        $user = DB::table('users')->where('email',$email)->first();

        $signup = false;

        if( !is_null($user) && Hash::check($password, $user->password)){
            $signup = true;
        }

        if($signup){
            $token = array(
                'sub' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'description' => $user->description,
                'iat' => time(),
                'exp' => time() + (24*60*60) // Expira al dia
            );
            // Codifico el usuario
            $jwt = JWT::encode($token,$this->key,'HS256');
            // Decodifico el usuario
            $decode = JWT::decode($jwt,$this->key,['HS256']);

            if(is_null($getToken)){ // No existe el token
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'token' => $jwt,
                    'message' => 'No existe'
                );
            }else{ // Devuelvo el token 
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'user' => $decode,
                    'message' => 'Existe'
                );
                return $data;
            }

        }else{
            $data = array(
                'status' => 'error',
                'code' => 401,
                'message' => 'No autorizado'
            );
        }

        return $data;
    }

    public function checktoken($token,$getIdentity = false){
        $auth = false;
        $decode = null;
        try{
            $decode = JWT::decode($token,$this->key,['HS256']);
        }
        catch(\UnexpectedValueException $e){
            $auth = false;
        }
        catch(\DomainException $e){
            $auth = false;
        }
        
        if(!is_null($decode)){
            $auth = true;
        }

        if($getIdentity){
            return $decode;
        }

        return $auth;
    }
}