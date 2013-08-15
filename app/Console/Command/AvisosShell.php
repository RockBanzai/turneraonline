<?php

App::uses('CakeEmail', 'Network/Email');
App::import( 'Controller/Avisos', 'AvisoAppSender' );
App::import( 'Controller/Avisos', 'EmailSender');
App::import( 'Controller/Avisos', 'SmsSender' );

class AvisosShell extends AppShell {

	var $uses = array( 'Aviso' );
    
    private $sms_sender = null;
    private $email_sender = null;

	public function verificarEnvio() {
		// Busco si existe algun email para saber si tengo que enviarlo.
		$min_inicio = intval( date( 'i' ) );
		/*if( !$this->Aviso->existePendiente( $min_inicio ) ) {
			$this->out( "No hay avisos pendientes." );
			return;
		} else {
		    $this->out( "Existen avisos pendientes" ) ;
		}*/
        $avisos = $this->Aviso->pendientes( $min_inicio );
        /*if( count( $avisos ) <= 0 ) {
            $this->out( "Listado de avisos vacío" );
            return;
        }*/
        $this->email_sender = new EmailSender();
        $this->sms_sender = new SmsSender();
        foreach( $avisos as $aviso ) {
            $this->out( 'Enviando aviso '.$aviso['Aviso']['id_aviso'] );
            $enviado = false;
            $aviso['Aviso']['metodo'] = 'sms';
            // Veo que tipo es
            if( $aviso['Aviso']['metodo'] == 'email' ) {
                $this->out( "Enviando mediante email" );
                $enviado = $this->email_sender->enviar( $aviso['Aviso']['id_aviso'] );
            } else if( $aviso['Aviso']['metodo'] == 'sms' ){
                $enviado = $this->sms_sender->enviar( $aviso['Aviso']['id_aviso'] );                
            } else {
                $this->out( 'Formato desconocido: '.$aviso['Aviso']['metodo'] );
            }
            if( $enviado ) {
                $this->Aviso->delete( $aviso['Aviso']['id_aviso'] );
            } else {
                $this->out( 'El aviso '.$aviso['Aviso']['id_aviso']." no pudo ser enviado" );
            }
       }
	}
}
?>