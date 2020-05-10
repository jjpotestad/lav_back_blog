<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
            'message' => 'Lista de categorias',
            'categories' => Category::all()
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
            $validate = Validator::make($params_array, Category::$rules,Category::$messages);

            if($validate->fails()){ // Falla la validacion
                $data = array(
                    'status' => 'error',
                    'code' => 406,
                    'message' => $validate->errors()
                );
            }else{  // Los datos son valido

                $category = Category::create([
                    'name' => $params_array['name'],
                ]);
    
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoria se creo OK',
                    'category' => $category
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
    public function show(Category $category)
    {
        $category = $category->load('posts');
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Show category',
            'category' => $category
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
    public function update(Request $request, Category $category)
    {
        // Recoger el json y convertirlo a PHP 
        $json = $request->input('json',null);
        $params_array = json_decode($json,true); // Return Array

        if($params_array){
            $validate = Validator::make($params_array, Category::$rules,Category::$messages);

            if($validate->fails()){ // Falla la validacion
                $data = array(
                    'status' => 'error',
                    'code' => 406,
                    'message' => $validate->errors()
                );
            }else{  // Los datos son valido

                $category->name = $params_array['name'];
                $category->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoria se modifico OK',
                    'category' => $category
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
    public function destroy(Category $category)
    {
        $category->delete();
        
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Delete category',
        );

        return response()->json($data,$data['code']);
    }
}
