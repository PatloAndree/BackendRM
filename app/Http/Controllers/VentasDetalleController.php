<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Venta_Detalle;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VemtasDetalleController extends Controller
{

    public function index()
    {
        $data['title'] = 'Usuarios';
        return view('login', $data);
    }

    public function ventasGeneral()
    {
        $ventas = Venta_Detalle::All();
        return response()->json($ventas);
    }

  

}
