<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use Illuminate\Support\Facades\Validator;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PostController extends Controller
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
            'message' => 'Lista de posts',
            'posts' => Post::all()
        );

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
            $validate = Validator::make($params_array, Post::$rules,Post::$messages);

            if($validate->fails()){ // Falla la validacion
                $data = array(
                    'status' => 'error',
                    'code' => 406,
                    'message' => $validate->errors()
                );
            }else{  // Los datos son valido
                // Obtener el usuario desde el token
                $user = $this->getUser($request);
                
                if(array_key_exists('image',$params_array)){
                    $image = $params_array['image'];
                }else{
                    $image = null;
                }

                $post = Post::create([
                    'title' => $params_array['title'],
                    'content' => $params_array['content'],
                    'image' => $image,
                    'category_id' => $params_array['category_id'],
                    'user_id' => $user->sub,
                ]);
    
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El post se creo OK',
                    'post' => $post
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
    public function show(Post $post)
    {
        $post =  $post->load('category')->load('user');
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Show post',
            'post' => $post
        );

        return response()->json($data,$data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        // Recoger el json y convertirlo a PHP 
        $json = $request->input('json',null);
        $params_array = json_decode($json,true); // Return Array

        $data = array(
            'status' => 'success',
            'code' => 200,
            'image' => $params_array['image']
        );
        
        // return response()->json($data,$data['code']);

        if($params_array){
            $validate = Validator::make($params_array, Post::$rules,Post::$messages);

            if($validate->fails()){ // Falla la validacion
                $data = array(
                    'status' => 'error',
                    'code' => 406,
                    'message' => $validate->errors()
                );
            }else{  // Los datos son valido

                if(array_key_exists('image',$params_array)){
                    $image = $params_array['image'];
                }else{
                    $image = null;
                }
                
                $user = $this->getUser($request);
                
                if($post->user_id == $user->sub){
                    $post->title = $params_array['title'];
                    $post->content = $params_array['content'];
                    if($post->image && $image!= $post->image)
                    {
                        Storage::disk('posts')->delete($post->image);
                    }
                    $post->image = $image;
                    $post->category_id = $params_array['category_id'];
                    $post->user_id = $user->sub;
                    $post->save();
    
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El post se modifico OK',
                        'post' => $post
                    );
                }else {
                    $data = array(
                        'status' => 'error',
                        'code' => 401,
                        'message' => 'No autorizado'
                    );
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
            Storage::disk('posts')->put($image_name, File::get($image_file));

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'La imagen se subio OK',
                'image' => $image_name
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
        $exist = Storage::disk('posts')->exists($filename);
        if($exist){
            $avatar = Storage::disk('posts')->get($filename);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Post $post)
    {
        // Obtener el usuario desde el token
        $user = $this->getUser($request);

        if($post->user_id == $user->sub){

            if($post->image =! null)
            {
                Storage::disk('posts')->delete($post->image);
            }

            $post->delete();

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Delete post',
                'post' => $post
            );
        }else {
            $data = array(
                'status' => 'error',
                'code' => 401,
                'message' => 'No autorizado'
            );
        }
        

        return response()->json($data,$data['code']);
    }

    private function getUser($request){
        $jwt = new JwtAuth();
        $token = $request->header('Authorization');
        return $jwt->checktoken($token,true);
    }
}
