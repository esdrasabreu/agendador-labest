<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/mail/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/SMTP.php';
include_once './conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($id)) {
    $query_events2 = "SELECT * FROM events e INNER JOIN categoria c ON e.categoria_id = c.id_categoria WHERE id=:id";
    $resultado_events2 = $conn->prepare($query_events2);
    $resultado_events2->bindParam('id', $id);
    
    $query_event = "DELETE FROM events WHERE id=:id";
    $delete_event = $conn->prepare($query_event);
    $delete_event->bindParam("id", $id);
    
    //$query_events = "SELECT categoria FROM categoria WHERE id_categoria = :title";
    
    
    //$resultado_events = $conn->prepare($query_events);
    //$resultado_events->bindParam(':title', $_POST['title']);
    //$resultado_events->execute();
    
    
    $resultado_events2->execute();
    
    //$categoria = $resultado_events->fetchAll( PDO::FETCH_ASSOC);
    $info = $resultado_events2->fetchAll( PDO::FETCH_ASSOC);
    
    
    $cat = $info[0]['categoria'];
    $nome = $info[0]['title'];
    
    $email = $info[0]['email'];
    $start = $info[0]['start'];
    $end = $info[0]['end'];
    //echo json_encode([$cat,$nome,$email]);exit;
    
    if($delete_event->execute()){
        //echo json_encode([$cat,$nome,$email]);exit;
        //mandar email
            $Mailer = new PHPMailer();
            
            //Define que será usado SMTP
            $Mailer->IsSMTP();
            //Enviar e-mail em HTML
	        $Mailer->isHTML(true);
	
	        //Aceitar carasteres especiais
            $Mailer->Charset = 'UTF-8';
            //$Mailer->Charset ='ISO-8859-1';
            
            //Configurações
            $Mailer->SMTPAuth = true;
            $Mailer->SMTPSecure = "tls";
            //$Mailer->SMTPDebug = 2;
            
            $Mailer->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            
            //nome do servidor
            $Mailer->Host = "smtp.gmail.com";
            //Porta de saida de e-mail 
            $Mailer->Port = 587;
            
            //$Mailer -> SMTPDebug = SMTP::DEBUG_SERVER;
            
            //Dados do e-mail de saida - autenticação
            $Mailer->Username = "uefslabest@gmail.com";
            $Mailer->Password = "uefslabest123";
            $Mailer->setFrom($Mailer->Username, "LABEST");

            //Destinatario 
            $Mailer->AddAddress($email);
	        $Mailer->AddAddress('labest@uefs.br');
	        $Mailer->AddAddress('xstrikeesdraspb@gmail.com');
	        
            
            $Mailer->Subject ='=?UTF-8?B?'.base64_encode("CANCELAMENTO DE AGENDAMENTO LABEST").'?=';
            //$nome= $_POST['nome'];
            $conteudo = "O AGENDAMENTO FOI CANCELADO!<br>
            Equipamento: $cat <br>
            Inicio do agendamento: $start<br>
            Final do agendamento: $end<br>
            Nome: $nome ";
            $Mailer->Body = $conteudo;
            if($Mailer->Send()){
                //echo "E-mail enviado com sucesso";
                $retorna = ['sit' => true, 'msg' => '<div class="alert alert-success" role="alert">EMAIL ENVIADO COM SUCESSO!</div>'];
                $_SESSION['msg'] = '<div class="alert alert-success" role="alert">AGENDAMENTO CADASTRADO COM SUCESSO!</div>';
            }else{
                //echo !extension_loaded('openssl')?"Not Available":"Available";
                echo json_encode($Mailer->ErrorInfo);
                exit;
            }
        
        $_SESSION['msg'] = '<div class="alert alert-success" role="alert">O evento foi apagado com sucesso!</div>';
        header("Location: index.php");
    }else{
        $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: O evento não foi apagado com sucesso!</div>';
        header("Location: index.php");
    }
} else {
    $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: O evento não foi apagado com sucesso!</div>';
    header("Location: index.php");
}
