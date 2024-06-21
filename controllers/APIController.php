<?php
namespace Controllers;

use Model\CitaServicio;
use Model\Cita;
use Model\Servicio;
class APIController{
    public static function index(){
        $servicios = Servicio::all();
        echo json_encode($servicios, JSON_UNESCAPED_UNICODE);
    }

    public static function guardar(){
        try{
            // Almacena la cita y devuelve el ID
            $cita = new Cita($_POST);
            $resultado = $cita->guardar();

            $id = $resultado["id"];
            
            // Almacena la Cita y los servicios

            $idServicios = explode(",", $_POST["servicios"]);

            foreach($idServicios as $idServicio){
                $args = [
                    "citasId" => $id,
                    "serviciosId" => $idServicio
                ];
                $citaServicio = new CitaServicio($args);
                $citaServicio->guardar();
            }
            // Retornamos una respuesta
            echo json_encode(["resultado" => $resultado]);

        } catch (\Throwable $th){
            echo $th;
        }
        
        // Comprobar con POSTMAN
        
      /*   $cita = new Cita($_POST);
        $respuesta = [
            "cita" => $cita
        ];
        echo json_encode($respuesta);  */
    }

    public static function eliminar(){
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $id = $_POST["id"];
            $cita = Cita::find($id);
            $cita->eliminar();
            header("location: " . $_SERVER["HTTP_REFERER"]);
        }
    }
}