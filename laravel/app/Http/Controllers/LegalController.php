<?php

namespace App\Http\Controllers;

use App\Models\Legal\departamentos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\Legal\contratos;
use App\Models\Legal\expjuridico;
use App\Models\Legal\evjuridico;
use App\Models\Legal\jevento;
use Illuminate\Support\Facades\DB;
use App\Models\legalHasUser;
use App\Models\Legal\juridicos;



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

    public function getUserRole(){
        $role = Auth::user();

        return $role;
    }

    public function getEventByExp(Request $request): \Illuminate\Http\JsonResponse
    {

        try {
            DB::beginTransaction();
                $expediente = evjuridico::selectRaw('DATE_FORMAT(evjuridico.fecha,"%d-%m-%Y") as Fecha, jevento.evento, evjuridico.observaciones, juridicos.nombre')
                    ->join('jevento','evjuridico.id_jevento','=','jevento.id_jevento')
                    ->join('juridicos','evjuridico.id_juridicos','=','juridicos.id_juridicos')
                    ->orderBy('evjuridico.fecha', 'desc')
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
        $event = jevento::select('id_jevento','evento')->where(['tipo' => 2])->get();

        return response()->json($event, Response::HTTP_OK);
    }

    public function getUserLegal(){
        $user = juridicos::selectRaw('id_juridicos as code, nombre as user_name')->get();

        return response()->json($user, Response::HTTP_OK);
    }

    public function setEvent(Request $request){

        try {

            DB::beginTransaction();

            $fdia = date('j');
            $fmes = date('m');
            $fanio = date('Y');
            $fecha = date('Y-m-d');

            $user_id = $this->getUserRegister();
            $user_id = $user_id[0]['id_juridicos'];




            $setEvent = new evjuridico;

            $setEvent->expediente = $request->expediente;
            $setEvent->fdia = $fdia;
            $setEvent->fmes = $fmes;
            $setEvent->fanio = $fanio;
            $setEvent->fecha = $fecha;
            $setEvent->id_jevento = $request->evento;
            $setEvent->id_regional = 1;
            $setEvent->observaciones = $request->observaciones;
            $setEvent->usuario = $user_id;
            $setEvent->ncomercial = $request->comercial;
            $setEvent->id_juridicos = $user_id;
            $setEvent->save();

            if ($request->flag === true) {
                $update = $this->updatedHistoryDocument($fdia, $fmes, $fanio, $fecha, $request->evento, $request->observaciones, $request->user_id, $request->expediente);
            }else if( $request->flag === false){
                $update = $this->DeleteHistoryDocument($fdia, $fmes, $fanio, $fecha, $request->evento, $request->observaciones, $user_id, $request->expediente);
                $delete = $this->UpdatedContratoDocument($request->expediente);
            }else if($request->flag === null) {
                $update = $this->updatedHistoryDocument($fdia, $fmes, $fanio, $fecha, $request->evento, $request->observaciones, $user_id, $request->expediente);
            }


            DB::commit();
            return response()->json($update, Response::HTTP_ACCEPTED);

        }catch (\Throwable $err){
            DB::rollBack();
            return response()->json($err, Response::HTTP_BAD_REQUEST);
        }
    }

    public function updatedHistoryDocument($day , $month, $year, $date, $event, $obs, $user, $code){
        Try {

            DB::beginTransaction();

            $update = expjuridico::where(['expediente' => $code])->update([
                'fdia'          =>  $day,
                'fmes'          =>  $month,
                'fanio'         =>  $year,
                'fecha'         =>  $date,
                'id_jevento'    =>  $event,
                'observaciones'  =>  $obs,
                'usuario'       =>  $user,
                'id_juridicos'  =>  $user
            ]);

            DB::commit();

            return response()->json($update , Response::HTTP_ACCEPTED);

        }catch (\Throwable $err){
            DB::rollBack();
            return response($err, Response::HTTP_BAD_REQUEST);

        }
    }


    public function UpdatedContratoDocument($code){
        Try {

            DB::beginTransaction();

            $update = contratos::where(['registro' => $code])->update([
                'status'        =>  '-50',
                'centinela'     =>  1,
                'caprobacion'   =>  $code
            ]);

            DB::commit();

            return response()->json($update , Response::HTTP_ACCEPTED);

        }catch (\Throwable $err){
            DB::rollBack();
            return response($err, Response::HTTP_BAD_REQUEST);

        }
    }

    public function DeleteHistoryDocument($day , $month, $year, $date, $event, $obs, $user, $code){
        Try {

            DB::beginTransaction();

            $update = expjuridico::where(['expediente' => $code])->update([
                'fdia'          =>  $day,
                'fmes'          =>  $month,
                'fanio'         =>  $year,
                'fecha'         =>  $date,
                'id_jevento'    =>  $event,
                'observaciones'  =>  $obs,
                'usuario'       =>  $user,
                'id_juridicos'  =>  $user,
                'status'        =>  '-50',
                'fechasalida'   =>  $date
            ]);

            DB::commit();

            return response()->json($update , Response::HTTP_ACCEPTED);

        }catch (\Throwable $err){
            DB::rollBack();
            return response($err, Response::HTTP_BAD_REQUEST);

        }
    }

    protected  function getInfoByCode(Request $request){
        $info = contratos::join('departamentos','departamentos.id_deptos','=','contratos.id_deptos')
        ->join('regional','regional.id_regional','=','contratos.id_regional')
        ->join('usuarios','contratos.usuario','=','usuarios.id')
        ->selectRaw('contratos.registro, contratos.nempresa, contratos.rlegal, contratos.ncomercial, contratos.direccion, contratos.zona, contratos.colonia, contratos.nit, contratos.municipio, contratos.telefono1, contratos.fax, contratos.email, concat(contratos.fdia, "/",
        contratos.fmes, "/", contratos.fanio) as fecha, usuarios.usuario, departamentos.nombre_deptos, regional.nombre_r, contratos.f63a, contratos.patente')
        ->where(['contratos.registro' => $request->code])
        ->get();

        return $info;
    }


    protected function getRegisterByUser()
    {
         $user_id = $this->getUserRegister();
         $user_id = $user_id[0]['id_juridicos'];
         $role_user = Auth::user()->menuroles;
         $roles = explode(',', $role_user);
         if(in_array('admin_legal', $roles)){
             $data = expjuridico::selectRaw('expediente as Expediente,ncomercial as Procedencia,DATE_FORMAT(fecha,"%d-%m-%Y") as Fecha,observaciones as Descripción,id_jevento,id_texpj')
                 ->orderBy('fecha')->get();
         }else{
             $data = expjuridico::selectRaw('expediente as Expediente,ncomercial as Procedencia,DATE_FORMAT(fecha,"%d-%m-%Y") as Fecha,observaciones as Descripción,id_jevento,id_texpj')
                 ->where(['id_juridicos' => $user_id, 'status' => 1])->orderBy('fecha')->get();
         }


         return response()->json($data, Response::HTTP_OK);
    }

    protected function getUserRegister()
    {
        $data = legalHasUser::select('id_juridicos')->where(['user_id' =>  Auth::user()->id])->get();
        return $data;
    }
}
