<?php
session_start();

include_once ("../common/config.php");
include_once ("../common/clsSolicitantes.php");
include_once ("../common/clsParametros.php");
include_once ("../js/recaptcha/recaptchalib.php");
require_once ("../common/clsCorreo.php");


foreach($_POST as $k => $val){
	$var = "\$" . $k . "=0;";
	eval($var);
	$var = "\$ref=&$" . $k . ";";
	eval($var);
	$ref = addslashes(utf8_decode($val));
}

$solicitantes = new clsSolicitantes();
$enviacorreo = new clsCorreo();

	

if($opt == "addSolicitante"){
		
	/////Valid datos solictante
	
	$resp = $solicitantes->exist_email($correo_e);

	if(!$resp){
		$json["error"] = true;
		$json["msg"] = "Error B005: Debe de comunicarse con el administrador";
		die(json_encode($json));
	}
	
	if($resp->exist){
		$json["error"] = true;
		$json["msg"] = "Error B006: Ya existe un usuario con el correo, $correo_e";
		die(json_encode($json));
	}
	
	
	$cadena_valida = base_convert(mt_rand(0x1679616, 0x39AA3FF), 12, 36);
	
	$sol = array(
		"id_perfil" => $id_perfil
		,"nombre" => utf8_encode($nombre)
		,"ap_paterno" => utf8_encode($ap_paterno)
		,"ap_materno" => utf8_encode($ap_materno)
		,"correo_e" => $correo_e
		,"telefono" => $telefono
		,"celular" => $celular
		,"nom_ptl" => $nom_ptl
		,"ptl_ptl" => $ptl_ptl
		,"exp_unam"=> $exp_unam
		,"passwd" => $passwd
		,"cadena_valida" => $cadena_valida 
		,"vigente" => 0
	);

	$solicitantes->startTransactionMysql();
	
	if(!$solicitantes->agregar_solicitante($sol)){
		$json["error"] = true;
		$json["msg"] = "Error B007: comuniquese con el administrador - No se agrego el solicitante";
		$json["debug"] = $solicitantes->getError();
		$json["lastQuery"] = $solicitantes->getLastQuery();
		die(json_encode($json));
	}
	
	$id_solicitante = $solicitantes->getLastIDSolicitante();
	
	/*datos factura*/
	
	if($chFactura == 1){
	 //die("111");
		$dFac = array(
		"id_solicitante"=> $id_solicitante
		,"rfc" => $rfc
		,"tipo_persona" => $tipo_persona
		,"calle" => utf8_encode($calle)
		,"id_ciudad" => $id_ciudad
		,"id_municipio" => $id_municipio
		,"id_edo" => $id_edo
		,"id_cp" => $id_cp
		,"num_int" => $num_int
		,"nombre_fisc"=>utf8_encode($nombre_fisc)
		,"nombre"=>utf8_encode($fnombre)
		,"ap_paterno"=>utf8_encode($fap_paterno)
		,"ap_materno"=>utf8_encode($fap_materno)
		,"id_colonia"=>$id_colonia
		,"colonia_otra"=>utf8_encode($txtOtraCol)
		,"num_ext" => $num_ext  
		
		);
		
		$resp = $solicitantes->agregar_datos_facturacion($dFac);
	
		if(!$resp){
			$json["error"]=true;
			$json["msg"] = "Error B008: Nuevo RFC";
			$json["query"] = $solicitantes->getLastQuery();
			$json["debug"] = $solicitantes->getError();
			$solicitantes->rollbackMysql();
			die(json_encode($json));
		}
		
		
	}
	
	/*enviar cadena por correo*/
    	global $CONFIG;

        $baseUrlUsuario= $CONFIG->rutas->baseDir;
		$from= $CONFIG->mail->from;

		$url_val = $baseUrlUsuario."valid_cuenta/index.php?task=validate_account&"."email={$correo_e}&cadena_val={$cadena_valida}";
		
		$nomTotal=$nombre.' '.$ap_paterno.' '.$ap_materno;
		$nomTotal=utf8_encode($nomTotal);
	
		$correo_solicitante = $correo_e;
	
		$contenido=$nomTotal;
	
		$datEmail=array(
						'addAddress'=>$correo_solicitante
						,'from'=>$from
						,'subject'=>'Registro de usuario-Sistema de pagos DGIRE'
						,'fromName'=>'Sistemas DGIRE'
						,'addBcc'=>''
						,'addBccName'=>''
						,'addCC'=>''
						,'addCCName'=>''
						,'$contenido'=>$contenido
						
						);
			
		$resp = $enviacorreo->enviaCorreoSolicitanteNuevo($contenido,$datEmail);

		//print_r($resp);exit;
		
	if(!$resp){
		$json["error"]=true;
		$json["msg"] = "Error B009: Problema enviar correo";
		$solicitantes->rollbackMysql();
		die(json_encode($json));
	}
	
	
	
	$solicitantes->commitMysql();
	
	$json["error"] = false;
	$json["msg"] = "El usuario fue agregado";
	$json[$id_solicitante] = $id_solicitante;
	$json["correo_e"] = $correo_e;
	die(json_encode($json));
}





?>
