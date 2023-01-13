<?php

date_default_timezone_set('America/Mexico_City');

require_once ("config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ("../libs/email/src/Exception.php");
require_once ("../libs/email/src/PHPMailer.php");
require_once ("../libs/email/src/SMTP.php");

class clsCorreo extends clsConexion{

    public function __construct($linkMysql=NULL){

        global $CONFIG;

        parent::__construct();

        $this->connect($linkMysql);

        $this->Host = $CONFIG->mail->Host;
        $this->Port = $CONFIG->mail->Port;
        $this->user = base64_decode($CONFIG->mail->user);
        $this->password = base64_decode($CONFIG->mail->password);
        $this->pagos = $CONFIG->mail->pagos;
        $this->from = $CONFIG->mail->from;
        $this->addBcc = $CONFIG->mail->addBcc;
        $this->addCC = $CONFIG->mail->addCC;

    }

    #******************************#
 
    #******************************#
    public function enviaCorreoSolicitanteNuevo($contenido,$datEmail){

        /* variables del correo*/
        $Host=$this->Host;
        $User=$this->user;
        $Pass=$this->password;
        $Puerto=$this->Port;

        $remitente=$datEmail['from'];
        $nomRemitente=$datEmail['fromName'];
        $correoUsuario=$datEmail['addAddress'];
        $asunto=$datEmail['subject'];
        $copia= $this->addBcc;

        /**/

        $mail = new PHPMailer(true);

            $mail->SMTPDebug = 0;                                      
            $mail->isSMTP(); 

            $mail->Debugoutput = 'html';
            $mail->Host =$Host ;                   
            $mail->SMTPAuth   = true;                                  
            $mail->Username =$User ;                     
            $mail->Password =$Pass ;                              
            $mail->SMTPSecure = 'tls';
            $mail->Port =$Puerto ;

            $mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
                ));

            $mail->setFrom($remitente, $nomRemitente);
            $mail->Subject = $asunto;
            $mail->addAddress($correoUsuario);    
            $mail->addBCC($copia);    

            $mail->CharSet = "utf-8";
            $mail->Encoding = "quoted-printable";

            $mail->AltBody = $contenido;
            $mail->MsgHTML($contenido);

            $valEmail=$mail->send();

            #send the message, check for errors
            if (!$valEmail) {
                $resp = $mail->ErrorInfo;
            } else {
                $resp = $valEmail;
            }

            return $resp;

    }

    #******************************#

   






}


?>
