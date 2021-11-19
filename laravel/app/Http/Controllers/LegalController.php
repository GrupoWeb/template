<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\Legal\juridicos;
use App\Models\Legal\expjuridico;
use App\Models\Legal\evjuridico;
use App\Models\Legal\jevento;
use Illuminate\Support\Facades\DB;


class LegalController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function getLegal(){
        $legal = expjuridico::get();
        return response()->json($legal);
    }

    public function getEventByExp(Request $request): \Illuminate\Http\JsonResponse
    {

        try {
            DB::beginTransaction();
                $expediente = evjuridico::selectRaw('DATE_FORMAT(evjuridico.fecha,"%d-%m-%Y") as Fecha, jevento.evento, evjuridico.observaciones, juridicos.nombre')
                    ->join('jevento','evjuridico.id_jevento','=','jevento.id_jevento')
                    ->join('juridicos','evjuridico.id_juridicos','=','juridicos.id_juridicos')
                    ->where(['evjuridico.expediente' => $request->code])
                    ->get();
            DB::commit();

            return response()->json($expediente, Response::HTTP_OK);

        }catch (\Throwable $err){
            DB::rollBack();
            return response()->json(false, Response::HTTP_BAD_REQUEST);
        }

    }

    public function getEvent(){
        $event = jevento::select('id_jevento','evento')->get();

        return response()->json($event, Response::HTTP_OK);
    }

    public function setEvent(Request $request){
        try {

            $fdia = date('j');
            $fmes = date('m');
            $fanio = date('Y');
            $fecha = date('Y-m-d');


            $setEvent = new evjuridico;

            $setEvent->expediente = $request->expediente;
            $setEvent->fdia = $fdia;
            $setEvent->fmes = $fmes;
            $setEvent->fanio = $fanio;
            $setEvent->fecha = $fecha;
            $setEvent->id_jevento = $request->evento;
            $setEvent->id_regional = $request->regional;
            $setEvent->observaciones = $request->observacion;
            $setEvent->usuario = $request->usuario;
            $setEvent->ncomercial = $request->comercial;
            $setEvent->id_juridicos = $request->juridico;
            $setEvent->save();

            /*
             * expediente
             * fdia, fmes, fanio, fecha (date php)
             * observaciones
             * id evento
             * ncomercial = usuario logiado
             * id_juridico = usuario juridico logeado
             * usuario = usuario juridico logeado
             * */
        }catch (\Throwable $err){

        }
    }

    protected function getRegisterByUser()
    {
//         $user_id = $this->getUserRegister();
//         $user_id = $user_id[0]['id_legal'];
        $data = expjuridico::selectRaw('expediente as Expediente,ncomercial as Procedencia,DATE_FORMAT(fecha,"%d-%m-%Y") as Fecha,observaciones as Descripción,id_jevento,id_texpj')->get();
//         ->where(['id_juridicos' => $user_id])->get();

        return response()->json($data, Response::HTTP_OK);
    }

    protected function getUserRegister()
    {
        $data = userLegal::select('id_legal')->where(['user_id' =>  Auth::user()->id])->get();
        return $data;
    }
}
