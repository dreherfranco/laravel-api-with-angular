<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class PostController extends Controller {

    public function __construct() {
        //Token middleware
        $this->middleware('api.auth', ['except' => ['show', 'index']]);
    }

    public function index(){
        $posts = Post::all();
        return response()->json($posts, 200);
    }
    
    public function store(Request $request) {
        $json = $request->input('json', null);
        $data_array = json_decode($json, true);
        if (!empty($data_array)) {
            $validate = \Validator::make($data_array, [
                        'category_id' => 'required|numeric',
                        'title' => 'required',
                        'content' => 'required',
                        'image' => 'required',
            ]);
            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se recibieron datos del formulario',
                    'errors' => $validate->errors()
                );
            } else {
                $header = $request->header('Authorization');
                //Obtengo los datos del usuario logueado
                $user = $this->tokenDecoded($header);
                //Instancio un objeto
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $data_array['category_id'];
                $post->title = $data_array['title'];
                $post->content = $data_array['content'];
                $post->image = $data_array['image'];
                $post->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El post se guardo correctamente',
                    'post' => $post
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se recibieron datos del formulario'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function update(Request $request, $id) {
        $json = $request->input('json', null);
        $data_array = json_decode($json, true);
        if (!empty($data_array)) {
            if (!empty($data_array)) {
                $validate = \Validator::make($data_array, [
                            'category_id' => 'required|numeric',
                            'title' => 'required',
                            'content' => 'required',
                ]);
                //borrar datos que no quiero actualizar
                unset($data_array['user_id']);
                unset($data_array['image']);
                unset($data_array['created_at']);
                
                if($validate->fails()){
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'No se recibieron datos del formulario',
                        'errors' => $validate->errors()
                    );
                } else {
                    $header = $request->header('Authorization');
                    $user = $this->tokenDecoded($header);
                    $update = Post::where('id', $id)->where('user_id', $user->sub)->update($data_array);
                    $post_update = Post::find($id)->get();
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El post se guardo correctamente',
                        'post' => $post_update
                    );
                }
            } else {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se recibieron datos del formulario'
                );
            }
        }
        return response()->json($data, $data['code']);
    }
    
    public function destroy($id, Request $request){
        $header = $request->header('Authorization');
        $user = $this->tokenDecoded($header);
        $post = Post::find($id);
        if($user->sub == $post->user_id){
        $delete = Post::destroy($id);
        $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El post se borro correctamente'
                    );
        }else{
            $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No se pudo borrar correctamente'
                );
        }
        return response()->json($data, $data['code']);
    }
    //OBTENER TOKEN
    public function tokenDecoded($header) {
        $token = $header;
        $jwt = new \JwtAuth();
        $token = str_replace('"', '', $token);
        //Checkear el token decodificado
        $user = $jwt->checkToken($token, true);
        return $user;
    }

}
