<?php

namespace App\Http\Controllers;

use App\Models\asign;
use App\Models\cntr;
use App\Models\statu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class cntrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $booking = $request['booking'];
        $qb = DB::table('cntr')->where('booking', '=', $booking)->get();
        $numero = $qb->count() + 1;

        if ($request['cntr_number']) {

            $cntr_number = $request['cntr_number'];

        } else {
            
            $cntr_number = $booking . $numero;

        }

        $cntr = new cntr();
        $cntr->booking = $booking;
        $cntr->cntr_number = $cntr_number;
        $cntr->cntr_seal = $request['cntr_seal'];
        $cntr->cntr_type = $request['cntr_type'];
        $cntr->retiro_place = $qb[0]->retiro_place;
        $cntr->confirmacion = $request['confirmacion'];
        $cntr->user_cntr = $request['user_cntr'];
        $cntr->company = $request['company'];
        $cntr->save();

        if ($cntr) {

            $asign = new asign();
            $asign->cntr_number = $cntr_number;
            $asign->booking = $booking;
            $asign->save();

            $idCarga = DB::table('carga')->where('booking', '=', $cntr->booking)->select('carga.id')->get();

            if ($asign->id) {
                return response()->json([
                    'detail' => $cntr, // Aquí accedemos directamente al objeto $cntr
                    'idCarga' => $idCarga[0]->id // Aquí accedemos al primer elemento del array $idCarga
                ], 200);
            } else {
                return response()->json(['errores' => 'Algo salió mal, hubo un errro en la asignación', 'id' => $idCarga[0]->id], 500);
            }
        } else {

            return response()->json(['errores' => 'Algo salió mal: el contenedor ya existe o faltó algun dato.', 'cntr_number' => $cntr_number], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cntr = cntr::find($id);

        if ($cntr) {

            $cntrOld = $cntr->cntr_number;

        }

        $cntr->cntr_number = $request['cntr_number'];
        $cntr->cntr_seal = $request['cntr_seal'];
        $cntr->confirmacion = $request['confirmacion'];
        $cntr->save();




        $asign = asign::where('cntr_number', $cntrOld)->update(['cntr_number' => $request['cntr_number']]);
        $status = statu::where('cntr_number', $cntrOld)->update(['cntr_number' => $cntr->cntr_number]);
        $idCarga = DB::table('carga')->where('booking', '=', $cntr->booking)->select('carga.id')->get();

      

        if ($asign === 1) {
            return response()->json([
                'detail' => $cntr, // Aquí accedemos directamente al objeto $cntr
                'idCarga' => $idCarga[0]->id // Aquí accedemos al primer elemento del array $idCarga
            ], 200);
        } else {
            return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción. Revise si el cntr no está asignado a otra unidad.', 'id' => $idCarga[0]->id], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
