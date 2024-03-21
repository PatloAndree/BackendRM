<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VemtasController;
use App\Http\Controllers\ProductosController;
use App\Models\Productos;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/usersGeneral',[ApiController::class,'usersGeneral']);
Route::get('/usersInactivos',[ApiController::class,'usersInactive']);

Route::post('/login',[ApiController::class,'login']);

// Route::post('/validarLogin',[UsersController::class,'validarLogin']);
Route::post('/validarLogin', [UsersController::class, 'validarLogin'])->name('users.validarLogin');


Route::get('/usuarios', [UsersController::class, 'index'])->name('users.index');

Route::get('/usuarios/listar', [UsersController::class, 'usersGeneral'])->name('users.usersGeneral');
Route::post('/usuarios/agregarUsuario', [UsersController::class, 'agregarUsuario'])->name('users.agregarUsuario');
Route::put('/usuarios/editarUsuario', [UsersController::class, 'editarUsuario'])->name('users.editarUsuario');
Route::delete('/usuarios/eliminarUsuario/{id?}', [UsersController   ::class, 'eliminarUsuario'])->name('users.eliminarUsuario');   

Route::get('/ventas/listar', [VemtasController::class, 'ventasGeneral'])->name('ventas.ventasGeneral');
Route::post('/ventas/listarTipo', [VemtasController::class, 'ventasTipo'])->name('ventas.ventasTipo');
Route::post('/ventas/agregarVenta', [VemtasController::class, 'agregarVentas'])->name('ventas.agregarVentas');
Route::put('/ventas/editarVenta', [VemtasController::class, 'editarVenta'])->name('ventas.editarVenta');
Route::delete('/ventas/eliminarVenta/{id?}', [VemtasController::class, 'eliminarVenta'])->name('ventas.eliminarVenta');   


Route::get('/productos/listar', [ProductosController::class, 'productosGeneral'])->name('productos.productosGeneral');
Route::post('/productos/agregarProducto', [ProductosController::class, 'agregarProducto'])->name('productos.agregarProducto');
Route::put('/productos/editarProducto', [ProductosController::class, 'editarProducto'])->name('productos.editarProducto');
Route::delete('/productos/eliminarProducto/{id?}', [ProductosController::class, 'eliminarProducto'])->name('productos.eliminarProducto');   




