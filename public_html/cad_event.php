<?php
session_start();

//require 'mail/PHPMailer.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/mail/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/SMTP.php';


include_once './conexao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');


//Converter a data e hora do formato brasileiro para o formato do Banco de Dados
$data_start_conv = new \DateTime(str_replace('/', '-', $_POST['start']));

$data_end_conv = new \DateTime(str_replace('/', '-', $_POST['end']));

$data_hoje = new \DateTime(str_replace('/', '-', date('d/m/Y H:i:s')));
$title = intval($_POST['title']);
//echo json_encode($_POST['nome']);exit;

// echo json_encode(['start' => $data_start_conv, 'hoje' => $data_hoje, 'condi' => $data_start_conv > $data_hoje && $data_end_conv > $data_hoje]);exit;


if ($data_end_conv > $data_start_conv && ($data_start_conv > $data_hoje && $data_end_conv > $data_hoje )) {
    $start_str = $data_start_conv->format('Y-m-d') . ' ' . $data_start_conv->format('H:i:s');
    $end_str = $data_end_conv->format('Y-m-d') . ' ' . $data_end_conv->format('H:i:s');
    //$query_data = "SELECT COUNT (*) FROM events WHERE categoria_id = {$title} ";
    $query_data =  "SELECT COUNT(*) 
    FROM events 
    WHERE 
    categoria_id = $title
    AND
    (
        (
            STR_TO_DATE(start,'%Y-%m-%d %T') < STR_TO_DATE('$start_str','%Y-%m-%d %T') 
            AND STR_TO_DATE(start,'%Y-%m-%d %T') < STR_TO_DATE('$end_str','%Y-%m-%d %T')
            AND STR_TO_DATE(end,'%Y-%m-%d %T') > STR_TO_DATE('$end_str','%Y-%m-%d %T')
            AND STR_TO_DATE(end,'%Y-%m-%d %T') > STR_TO_DATE('$start_str','%Y-%m-%d %T')
        )
        OR
        (
            STR_TO_DATE(start,'%Y-%m-%d %T') < STR_TO_DATE('$start_str','%Y-%m-%d %T') 
            AND STR_TO_DATE(start,'%Y-%m-%d %T') < STR_TO_DATE('$end_str','%Y-%m-%d %T') 
            AND STR_TO_DATE(end,'%Y-%m-%d %T') < STR_TO_DATE('$end_str','%Y-%m-%d %T')  
            AND STR_TO_DATE(end,'%Y-%m-%d %T') > STR_TO_DATE('$start_str','%Y-%m-%d %T')  
        )
        OR
    
        (
            STR_TO_DATE(start,'%Y-%m-%d %T') > STR_TO_DATE('$start_str','%Y-%m-%d %T') 
            AND STR_TO_DATE(start,'%Y-%m-%d %T') < STR_TO_DATE('$end_str','%Y-%m-%d %T') 
            AND STR_TO_DATE(end,'%Y-%m-%d %T') > STR_TO_DATE('$end_str','%Y-%m-%d %T')
            AND STR_TO_DATE(end,'%Y-%m-%d %T') > STR_TO_DATE('$start_str','%Y-%m-%d %T')
        )
        OR
        (
            STR_TO_DATE(start,'%Y-%m-%d %T') > STR_TO_DATE('$start_str','%Y-%m-%d %T') 
            AND STR_TO_DATE(start,'%Y-%m-%d %T') < STR_TO_DATE('$end_str','%Y-%m-%d %T') 
            AND STR_TO_DATE(end,'%Y-%m-%d %T') < STR_TO_DATE('$end_str','%Y-%m-%d %T')
            AND STR_TO_DATE(end,'%Y-%m-%d %T') > STR_TO_DATE('$start_str','%Y-%m-%d %T')
        )
        OR
        (
            start = '$start_str' OR end = '$end_str'
        )
    )";

    $smtl_cat = $conn->prepare($query_data);
    $smtl_cat->execute();
    //$smtl_cat->fetchAll();
    $count = $smtl_cat->fetchAll();
    // 
    //echo json_encode(($count[0][0]== 0));
    // exit;

    if (($count[0][0]==0)) {

        $query_event = "INSERT INTO events (title, color, start, end, categoria_id, email) VALUES (:nome,:color, :start, :end, :title, :email)";

        $insert_event = $conn->prepare($query_event);
        $insert_event->bindParam(':nome', $_POST['nome']);
        $insert_event->bindParam(':color', $_POST['color']);
        $insert_event->bindParam(':start', $start_str);
        $insert_event->bindParam(':end', $end_str);
        $insert_event->bindParam(':title', $_POST['title']);
        $insert_event->bindParam(':email', $_POST['email']);
      
        $query_events = "SELECT categoria FROM categoria WHERE id_categoria = :title";
        
        $resultado_events = $conn->prepare($query_events);
        $resultado_events->bindParam(':title', $_POST['title']);
        $resultado_events->execute();
        
        $categoria = $resultado_events->fetchAll( PDO::FETCH_ASSOC);
        //$cat = $categoria[0];
        $cat = implode(",",$categoria[0]);
        //echo json_encode($cat);exit;
        //echo json_encode("não entrou");exit;
    
        if ($insert_event->execute() && $resultado_events->execute() ) {
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
            $Mailer->AddAddress($_POST['email']);
	        $Mailer->AddAddress('labest@uefs.br');
	        $Mailer->AddAddress('xstrikeesdraspb@gmail.com');
	        
            
            $Mailer->Subject ='=?UTF-8?B?'.base64_encode("CONFIRMAÇÃO AGENDAMENTO LABEST").'?=';
            $nome= $_POST['nome'];
            $conteudo = "SEU AGENDAMENTO FOI CONFIRMADO!<br>
            Equipamento: $cat <br>
            Inicio do agendamento: $start_str<br>
            Final do agendamento: $end_str<br>
            Nome: $nome";
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
            
        } else {
            $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Agendamento não foi cadastrado com sucesso!</div>'];
        }
        echo json_encode($retorna);
        exit;
    } else {
        echo json_encode(['sit' => false, 'msg' => 'Este horário esta ocupado']);
        exit;
    }
} else {
    echo json_encode(['sit' => false, 'msg' => 'Data inicial deve ser menor que a final.']);
    exit;
    }

