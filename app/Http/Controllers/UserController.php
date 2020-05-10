<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\JwtAuth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Lista de usuarios',
            'users' => User::all()
        );

        return response()->json($data,$data['code']);
    }

    /**
     * Login 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function login(Request $request)
    {

        // Recoger el json y convertirlo a PHP 
        $json = $request->input('json',null);
        $params_array = json_decode($json,true); // Return Array

        if($params_array){
            $validate = Validator::make($params_array, User::$login_rules,User::$messages);

            if($validate->fails()){ // Falla la validacion
                $data = array(
                    'status' => 'error',
                    'code' => 406,
                    'message' => $validate->errors()
                );
            }else{  // Los datos son valido
                
                $jwt = new JwtAuth();
                // $email = 'julio@gmail.com';
                // $passwd = 'admin';
                if(array_key_exists('gettoken',$params_array)){ // Si existe el token

                    $signup = $jwt->signup($params_array['email'],$params_array['password'],$params_array['gettoken']);
                    
                    if( $signup['status'] == 'error'){
                        $data = $signup;
                    }else{
                        $data = array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Login OK',
                            'signup' => $signup
                        );
                    }
                }else{ // El el token no existe 
                    $signup = $jwt->signup($params_array['email'],$params_array['password']);
                    if( $signup['status'] == 'error'){
                        $data = $signup;
                    }else{
                        $data = array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Login OK',
                            'signup' => $signup
                        );
                    }
                }
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 422,
                'message' => 'JSON mal formado'
            );
        }
        

        return response()->json($data,$data['code']);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        // Recoger el json y convertirlo a PHP 
        $json = $request->input('json',null);
        $params_array = json_decode($json,true); // Return Array

        if($params_array){
            $validate = Validator::make($params_array, User::$create_rules,User::$messages);

            if($validate->fails()){ // Falla la validacion
                $data = array(
                    'status' => 'error',
                    'code' => 406,
                    'message' => $validate->errors()
                );
            }else{  // Los datos son valido
                if(array_key_exists('description',$params_array)){
                    $description = $params_array['description'];
                }else{
                    $description = null;
                }

                $user = User::create([
                    'name' => $params_array['name'],
                    'surname' => $params_array['surname'],
                    'description' => $description,
                    'email' => $params_array['email'],
                    'password' => Hash::make($params_array['password']),
                ]);
    
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se creo OK',
                    'user' => $user
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 422,
                'message' => 'JSON mal formado'
            );
        }
        

        return response()->json($data,$data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Show user',
            'user' => $user
        );

        return response()->json($data,$data['code']);
    }

    /**
     * Upload the avatar the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $image_file = $request->file('file0');
        
        
        $validate = Validator::make($request->all(),
        ['file0' => 'required|image|mimes:jpg,jpeg,png,gif']);
        
        if($validate->fails()){ // Falla la validacion
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Debe subir una imagen valida'
            );
        }else{
            $image_name = $image_file->getClientOriginalName();
            Storage::disk('users')->put($image_name, File::get($image_file));

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El avatar se subio OK',
                'avatar' => $image_name
            );
        }
    return response()->json($data,$data['code']);
    }
    
    /**
     * Display the specified avatar.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getImage($filename)
    {
        $exist = Storage::disk('users')->exists($filename);
        if($exist){
            $avatar = Storage::disk('users')->get($filename);
            return new Response($avatar,200);
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'La imagen no existe'
            );
            return response()->json($data,$data['code']);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,User $user)
    {
            // Recoger el json y convertirlo a PHP 
            $json = $request->input('json',null);
            $params_array = json_decode($json,true); // Return Array
            // return $params_array;
            if($params_array){

                $rules = Arr::add(User::$update_rules, 'email', 'required|string|email|unique:users,email,'.$user->id);

                $validate = Validator::make($params_array, $rules,User::$messages);

                if($validate->fails()){ // Falla la validacion
                    $data = array(
                        'status' => 'error',
                        'code' => 406,
                        'message' => $validate->errors()
                    );
                }else{  // Los datos son valido

                    if(array_key_exists('description',$params_array)){
                        $description = $params_array['description'];
                    }else{
                        $description = null;
                    }

                    if(array_key_exists('avatar',$params_array)){
                        $avatar = $params_array['avatar'];
                    }else{
                        $avatar = null;
                    }

                    $user->name = $params_array['name'];
                    $user->surname = $params_array['surname'];
                    $user->description = $description;
                    $user->email = $params_array['email'];
                    $user->avatar = $avatar;
                    $user->save();

                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El usuario se modifico OK',
                        'user' => $user
                    );
                }
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 422,
                    'message' => 'JSON mal formado'
                );
            }
        return response()->json($data,$data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Delete user',
            'user' => $user
        );

        return response()->json($data,$data['code']);
    }
}
