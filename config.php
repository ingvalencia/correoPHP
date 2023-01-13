<?php

# information de errores del sistema.

ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '../../logs_pagosDgire/bitacora_user/error_log_system/errors.log');
    
global $CONFIG;

$CONFIG = (object)array (
	
	'mysql' => (object)array(
								
	)
    ,
	'sybase' => (object)array(
								
	),
    'patronato' => (object)array(

								
    )
	,
	"mail" => (object)array(

								 'Host' => "132.248.38.11"
								,'Port' => 25
								,'user' => "c2lzdGVtYXM="
								,'password' => "WjE4NmhNQ2U="
								,'pagos' => "pagosdgire@dgire.unam.mx"	#Correo del adminsitrador de caja
								,'from' => "sistemas@dgire.unam.mx"		#Correo del administrador de pagos pagos@dgire.unam.mx
								,'addBcc' => "gvalenci@dgire.unam.mx"	#Correo copia
								,'addCC' => "rejinderiog7@gmail.com"	#Correo copia
	)
	,
    "rutas" => (object)array(
								
    )


	
);


?>
