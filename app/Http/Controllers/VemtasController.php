<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Productos;
use App\Models\Venta_Detalle;
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
        // $users = Ventas::All();
        // return response()->json($users);
        $ventas = Ventas::orderBy('created_at', 'desc')->get();
        return response()->json($ventas);
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
        $venta =  Ventas::create([
            'nombre' => $request->nombre,
            'codigo_venta' => $request->codigo,
            'user_id' => $request->usuario,
            'tipo_venta' => $request->tipo,
            'ganancia' => $request->ganancia,
        ]);

        if ($venta) {
            // Ahora, iteramos sobre los detalles de la venta y los guardamos
            foreach ($request->data as $detalle) {
                $valores = Venta_Detalle::create([
                    'user_id' => $request->usuario,
                    'venta_id' => $venta->id,
                    'producto_id' => $detalle['id'],
                    'precio' => $detalle['precio_venta'],
                    'cantidad' => $detalle['cantidad'],
                    'monto' => $detalle['total'],
                ]);

                $producto = Productos::find($detalle['id']);
                if ($producto) {
                    $producto->stock -= $detalle['cantidad'];
                    $producto->save();
                } else {
                    throw new \Exception('Producto no encontrado');
                }
            }
            // Inserción exitosa
            return response()->json([
                "sw_error" => 0,
                "titulo" => "Éxito",
                "type" => "success",
                "message" => "Se registró correctamente la venta"
            ]);
        } else {
            // Hubo un error en la inserción
            return response()->json([
                "sw_error" => 1,
                "titulo" => "Error",
                "type" => "error",
                "message" => "Error al registrar la venta"
            ]);
        }
    }

    public function agregarVentas3(Request $request)
    {
        $venta = Ventas::create([
            'nombre' => $request->nombre,
            'codigo_venta' => $request->codigo,
            'user_id' => $request->usuario,
            'tipo_venta' => $request->tipo,
            'ganancia' => $request->ganancia,
        ]);

        if ($venta) {
            // Inicializar una lista para almacenar los IDs de productos y las cantidades vendidas
            $productosParaActualizarStock = [];

            // Iterar sobre los detalles de la venta y guardarlos
            foreach ($request->data as $detalle) {
                $valores = Venta_Detalle::create([
                    'user_id' => $request->usuario,
                    'venta_id' => $venta->id,
                    'producto_id' => $detalle['id'],
                    'precio' => $detalle['precio_venta'],
                    'cantidad' => $detalle['cantidad'],
                    'monto' => $detalle['total'],
                ]);

                // Guardar el ID del producto y la cantidad vendida para actualizar el stock posteriormente
                $productosParaActualizarStock[] = [
                    'id' => $detalle['id'],
                    'cantidadVendida' => $detalle['cantidad'],
                ];
            }
            // Actualizar el stock de los productos vendidos
            $this->actualizarStock($productosParaActualizarStock);
            // Inserción exitosa
            return response()->json([
                "sw_error" => 0,
                "titulo" => "Éxito",
                "type" => "success",
                "message" => "Se registró correctamente la venta"
            ]);
        } else {
            // Hubo un error en la inserción
            return response()->json([
                "sw_error" => 1,
                "titulo" => "Error",
                "type" => "error",
                "message" => "Error al registrar la venta"
            ]);
        }
    }


    public function actualizarStock($productos)
    {
        foreach ($productos as $producto) {
            $productoDB = Productos::find($producto['id']);
            if ($productoDB) {
                $productoDB->stock -= $producto['cantidadVendida'];
                $productoDB->save();
            }
        }
    }

    public function editarVenta(Request $request)
    {
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

    public function eliminarVenta($idVenta){
        // Verifica si se realizaron cambios
        $Usuario = Ventas::where('id',$idVenta)->update(['status'=>0]);
        if ($Usuario) {
            // Inserción exitosa
            echo json_encode(array("sw_error" => 0, "titulo" => "Éxito", "type" => "success", "message" => "Se eliminó correctamente la venta"));
        } else {
            // Hubo un error en la inserción
            echo json_encode(array("sw_error" => 1, "titulo" => "Error", "type" => "error", "message" => "Error al eliminar la venta"));
        }
	}
}
