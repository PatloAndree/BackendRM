<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ventas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VemtasController extends Controller
{

    public function index()
    {
        $data['title'] = 'Usuarios';
        return view('login', $data);
    }

    public function ventasGeneral()
    {
        $users = Ventas::All();
        return response()->json($users);
    }

    // public function ventasTipo(Request $request){
    //     if($request->tipoVenta) {
    //         $ventas = Ventas::where('tipo_venta', $request->tipoVenta)->get();
    //     } else {
    //         $ventas = Ventas::all();
    //     }
    //     return response()->json(["Sw_error" => 0, "Data" => $ventas]);
    // }

    public function ventasTipo(Request $request)
    {
        $fechaInicio = $request->fechaInicioFormateada;
        $fechaFin = $request->fechaFinFormateada;
        if ($request->tipoVenta ==  0) {
            $ventas = Ventas::where('status', 1);
        } else {
            $ventas = Ventas::where('tipo_venta', $request->tipoVenta);
        }
        if ($fechaInicio && $fechaFin) {
            $ventas->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }
        $ventas->orderBy('created_at', 'desc');
        $ventas = $ventas->get();
        return response()->json(["Sw_error" => 0, "Data" => $ventas]);
    }

        
    public function ventasTipo3(Request $request)
{
    $fechaInicio = $request->fechaInicioFormateada;
    $fechaFin = $request->fechaFinFormateada;

    $nivelesVenta = [
        1 => 'alto',
        2 => 'medio',
        3 => 'bajo'
    ];
    $ventas = Ventas::where('status', 1);
    // Filtrar por tipo de venta si se proporciona
    if ($request->tipoVenta != 0) {
        $ventas->where('tipo_venta', $request->tipoVenta);
    }
    // Filtrar por rango de fechas si se proporciona
    if ($fechaInicio && $fechaFin) {
        $ventas->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }
    // Ordenar las ventas
    $ventas->orderBy('created_at', 'desc');
    // Obtener las ventas
    $ventas = $ventas->get();
   
    $ventas = $ventas->map(function ($venta) use ($nivelesVenta) {
        // Obtener el tipo de venta y convertirlo a entero
        $tipoVenta = intval($venta->tipo_venta);

        // Verificar si el tipo de venta está definido en los niveles de venta
        if (isset($nivelesVenta[$tipoVenta])) {
            // Asignar el nivel de venta correspondiente
            $venta->nivel_venta = $nivelesVenta[$tipoVenta];
        } else {
            // Si el tipo de venta no está definido, asignar 'desconocido'
            $venta->nivel_venta = 'desconocido';
        }

        // Imprimir para depuración
        echo "tipo_venta: $tipoVenta, nivel_venta: $venta->nivel_venta\n";

        return $venta;
    });     

    // Retornar la respuesta JSON
    return response()->json(["Sw_error" => 0, "Data" => $ventas]);
}



    public function agregarVentas(Request $request)
    {
        $sw_insert =  Ventas::create([
			'nombre'=>$request->nombre,
			'tipo_venta'=>$request->tipo,
			'monto'=>$request->monto,
			'ganancia'=>$request->ganancia,
		]);
        if ($sw_insert) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se registró correctamente la venta"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al registrar la venta"));
        }
    }

    public function editarVenta(Request $request){
        // Verifica si se realizaron cambios
        $dataUpdate = array();
        $dataUpdate['nombre'] = $request->nombre;
        $dataUpdate['tipo_venta'] = $request->tipo;
        $dataUpdate['monto'] = $request->monto;
        $dataUpdate['ganancia'] = $request->ganancia;

		$sw_update = Ventas::where('id', $request->id)->update($dataUpdate);
        if ($sw_update) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se edito correctamente la venta"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al editar la venta"));
        }
	}

}
