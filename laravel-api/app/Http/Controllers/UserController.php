<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller {

    public function register(Request $request) {
        //Recibir los datos de la peticion
        $json = $request->input('json', null);
        $user_decode = json_decode($json, true); //devuelve un array, sino $user_decode = json_decode($json); devuelve un objeto

        $data = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'No se encontraron datos',
        );
        if (!empty($user_decode)) {
            //Limpiar datos
            $user_decode = array_map('trim', $user_decode);
            //Validar los datos
            $validate = \Validator::make($user_decode, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        'email' => 'email|unique:users|required',
                        'password' => 'required'
            ]);

            //Comprueba si hubo fallos en la validacion
            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error al guardar datos',
                    'error' => $validate->errors()
                );
            } else {
                //Hashear la contraseña
                $pwd = hash('sha256', $user_decode['password']);
                //Guardar el usuario
                $user = new User();
                $user->name = $user_decode['name'];
                $user->surname = $user_decode['surname'];
                $user->email = $user_decode['email'];
                $user->password = $pwd;
                $user->role = "ROLE_USER";
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se guardo correctamente'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se encontraron datos'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        $json = $request->input('json', null);
	$object_params = json_decode($json);
        $json_array = json_decode($json, true);
        //validar datos
        $validate = \Validator::make($json_array, [
                    'email' => 'required|email',
                    'password' => 'required'
        ]);
        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Error al identificarse',
                'error' => $validate->errors()
            );
        } else {
            $jwt = new \JwtAuth();
            $email = $json_array['email'];
            $password = hash('sha256', $json_array['password']);

            $signUp = $jwt->signUp($email, $password, false);
		
	    if(!empty($object_params->gettoken)){
		$signUp = $jwt->signUp($email, $password, true);
	    }
             
            
        }
        return response()->json($signUp, 200);
    }

    public function update(Request $request) {
        //Tomar los valores del token
        $token = $request->header('Authorization');
        $jwt = new \JwtAuth();
        $token = str_replace('"', '', $token);
        //Checkear el token y decodificarlo
        $user = $jwt->checkToken($token, true);     
        //Sacar los valores de la peticion
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        if($user && !empty($params_array)){
        //Validar datos
        $validate = \Validator::make($params_array, [
            'name' => 'required|alpha',
            'surname' => 'required|alpha',
            'email' => 'required|email|unique:users,email,'.$user->sub.'id'
        ]);
        //Borrar datos que no quiero actualizar
        unset($params_array['id']);
        unset($params_array['created_at']);
        unset($params_array['remember_token']);
        unset($params_array['role']);
        unset($params_array['password']);
        
        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se pudo actualizar correctamente',
                'error' => $validate->errors()
             ];
        }else{
            $user_update = User::where('id', $user->sub)->update($params_array);
            //Obtengo usuario con los datos actualizados
            $user_update = User::where('id', $user->sub)->get();
            
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'El usuario se actualizo correctamente',
                'token' => $user,
                'user_update' => $user_update
             ];
        }
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se actualizo correctamente',
             ];
        }
        return response()->json($data, $data['code']);
    }

    public function uploadAvatar(Request $request){
        //Tomar los valores del token
        $token = $request->header('Authorization');
        $jwt = new \JwtAuth();
        $token = str_replace('"', '', $token);
        //Checkear el token y decodificarlo
        $user = $jwt->checkToken($token, true);     
        
        //Obtener Imagen Temporal
        $image = $request->file('file0');
        $validate = \Validator::make($request->all(), [
            'file0' => 'mimes:jpg,png,jpeg,gif|image'
        ]);
        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se actualizo correctamente',
                'error' => $validate->errors()
            ];
            
        }else{
            $image_name= time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            $save_image = User::where('id',$user->sub)->update(['image'=> $image_name]);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => 'El avatar se ha subido correctamente',
                'user' => $user
            );
        }
        return response()->json($data, $data['code']);
    }
  
    public function getImage($filename){
        $exists = \Storage::disk('users')->exists($filename);
        //Comprobar si existe la imagen
         if($exists){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe la imagen',
            );
            return response()->json($data, $data['code']);
        }
        
    }
    
    public function detail($id){
        $user = User::find($id);
        if(is_object($user) && !empty($user)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => 'Usuario encontrado correctamente',
                'user' => $user
            );
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se encontro el usuario',
            );
        }
        return response()->json($data, $data['code']);
    }
    
}
