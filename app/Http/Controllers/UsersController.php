<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function index(){
        $data['title']='Usuarios';
        $data['contra']='Usuarios';


        return view('login',$data);
    }

    public function usersGeneral21(Request $request)
    {
        $users = User::all()->map(function ($user) {
            $user->password = $user->decrypted_password; // Aquí se accede al atributo desencriptado
            return $user;
        });

        return response()->json($users);
    }

    
    public function usersGeneral(Request $request){

        $users = User::All();
        // if($request->has('active')){
        //     $users = User::where('active',true)->get();
        // }else{
        //     $users = User::all();
        // }
        // $users = User::where('status',1)->get();
        return response()->json($users);
    }

    public function validarLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // El usuario ha iniciado sesión correctamente
            return response()->json([
                'success' => 1,
                'message' => 'Inicio de sesión exitoso',
                'user' => Auth::user()
            ]);
        } else {
            // Las credenciales no son válidas
            return response()->json([
                'success' => 0,
                'message' => 'Credenciales incorrectas'
            ], 401); // 401 Unauthorized
        }
    }

    public function agregarUsuario(Request $request)
    {

        $sw_insert =  User::create([
			'name'=>$request->nombres,
			'apellidos'=>$request->apellidos,
			'email'=>$request->correo,
			'dni'=>$request->dni,
			'telefono'=>$request->telefono,
			// 'password'=>$request->cotrasena,
            // 'password'=> Hash::make($request->contrasena)
            'password'=> bcrypt($request->contrasena)

            
		]);
        if ($sw_insert) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se registró correctamente la categoría"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al registrar la categoría"));
        }
    }

    public function editarUsuario(Request $request){
        // Verifica si se realizaron cambios
        $dataUpdate = array();
        $dataUpdate['name'] = $request->nombres;
        $dataUpdate['apellidos'] = $request->apellidos;
        $dataUpdate['email'] = $request->correo;
        $dataUpdate['dni'] = $request->dni;
        $dataUpdate['telefono'] = $request->telefono;
        $dataUpdate['password'] = Hash::make($request->contrasena);

		$sw_update = User::where('id', $request->id)->update($dataUpdate);
        if ($sw_update) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se edito correctamente la categoría"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al editar la categoría"));
        }
	}

    
    public function eliminarUsuario($idUsuario){
       
        // Verifica si se realizaron cambios
        $Usuario = User::where('id',$idUsuario)->update(['status'=>0]);

  
        if ($Usuario) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se eliminó correctamente el usuario"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al eliminar el usuario"));
        }
	}

}