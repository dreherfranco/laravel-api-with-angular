<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Post;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();
        if($categories){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Categorias encontradas correctamente',
                'categories' => $categories
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se encontraron las categorias',
            );
        }
        return response()->json($data, $data['code']);
    }
    
    public function store(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);
            
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Error al validar',
                    'error' => $validate->errors()
                );
            }else{
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoria se guardo correctamente',
                    'category' => $category
                );
            }
            
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se encontraron datos',
            );
        }
        return response()->json($data, $data['code']);
    }
    public function show($id){
        $category = Category::find($id);
        if(is_object($category)){
            $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoria se encontro correctamente',
                    'category' => $category
                );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se encontro la categoria',
            );
        }
        
        return response()->json($data, $data['code']);
    }
    //$id recibe su valor por url
    public function update(Request $request, $id){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);
            
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Error al validar',
                    'error' => $validate->errors()
                );
            }else{
                //Quitar datos que no quiero actualizar
                unset($params_array['id']);
                unset($params_array['create_at']);
                //Actualizar registros
                $category_update = Category::find($id);
                if(is_object($category_update)){
                    $update = Category::where('id', $id)->update($params_array);
                    //Sacar datos para poder mostrarlos
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'La categoria se guardo correctamente',
                        'category_update' => $category_update
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'No existe la categoria',
                    );
                }
            }
        }else{
            $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'No se encontraron datos',
                    );
        }
        
        return response()->json($data, $data['code']);
    }
            
    public function destroy($id){
        $category = Category::find($id);
        if(is_object($category)){
            //Obtener todos los post que pertenezcan a esta categoria
            foreach($category->posts as $post){
                $post = Post::destroy($post->id);
            }
            Category::destroy($id);
            $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'La categoria se borro correctamente',
                    );
        }else{
            $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'No se pudo borrar correctamente la categoria deseada',
                    );
        }
    }
}
