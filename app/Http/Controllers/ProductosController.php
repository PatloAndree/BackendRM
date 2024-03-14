<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Productos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductosController extends Controller
{

    public function index()
    {
        $data['title'] = 'Productos';
        return view('login', $data);
    }

    public function productosGeneral()
    {
        $productos = Productos::All();
        return response()->json($productos);
    }

    public function agregarProducto(Request $request)
    {
        $sw_insert =  Productos::create([
			'nombre'=>$request->nombre,
			'abreviacion'=>$request->abreviacion,
			'precio_compra'=>$request->precio_compra,
			'precio_venta'=>$request->precio_venta,
			'stock'=>$request->stock,

		]);
        if ($sw_insert) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se registró correctamente la venta"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al registrar la venta"));
        }
    }

    public function editarProducto(Request $request){
        // Verifica si se realizaron cambios
        $dataUpdate = array();
        $dataUpdate['nombre']=$request->nombre;
        $dataUpdate['abreviacion'] = $request->abreviacion;
        $dataUpdate['precio_compra']= $request->precio_compra;
        $dataUpdate['precio_venta'] = $request->precio_venta;
        $dataUpdate['stock'] = $request->stock;
		$sw_update = Productos::where('id', $request->id)->update($dataUpdate);
        if ($sw_update) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se edito correctamente el producto"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al editar el producto"));
        }
	}

    public function eliminarProducto($idProducto){
       
        // Verifica si se realizaron cambios
        $Usuario = Productos::where('id',$idProducto)->update(['status'=>0]);
        if ($Usuario) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se eliminó correctamente el producto"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al eliminar el producto"));
        }
	}



}
