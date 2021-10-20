<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/mail/Exception.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/mail/SMTP.php';
include_once './conexao.php';

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
// echo json_encode($_POST); exit;

//Converter a data e hora do formato brasileiro para o formato do Banco de Dados
$data_start_conv = new \DateTime(str_replace('/', '-', $_POST['start']));

$data_end_conv = new \DateTime(str_replace('/', '-', $_POST['end']));

$data_hoje = new \DateTime(str_replace('/', '-', date('d/m/Y H:i:s')));

$start_str = $data_start_conv->format('Y-m-d') . ' ' . $data_start_conv->format('H:i:s');
$end_str = $data_end_conv->format('Y-m-d') . ' ' . $data_end_conv->format('H:i:s');

$categoria_id = $_POST['title'];
$id = intval($_POST['id']);
if ($data_end_conv > $data_start_conv && ($data_start_conv > $data_hoje && $data_end_conv > $data_hoje)) {
    $start_str = $data_start_conv->format('Y-m-d') . ' ' . $data_start_conv->format('H:i:s');
    $end_str = $data_end_conv->format('Y-m-d') . ' ' . $data_end_conv->format('H:i:s');
    
    $query_data =  "SELECT COUNT(*) 
    FROM events 
    WHERE 
    id <> $id
    AND
    categoria_id = $categoria_id
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
    //$smtl_cat->bindParam(':title', $_POST['title']);
    $smtl_cat->execute();
    //$smtl_cat->fetchAll();
    $count = $smtl_cat->fetchAll();
    //echo json_encode(($count[0][0]== 0));
    

    if (($count[0][0] == 0)) {

        $query_event = "UPDATE events SET color=:color, start=:start, end=:end, categoria_id=:title WHERE id=:id";

        $update_event = $conn->prepare($query_event);
        $update_event->bindParam(':color', $_POST['color']);
        $update_event->bindParam(':start', $start_str);
        $update_event->bindParam(':end', $end_str);
        $update_event->bindParam(':title', $_POST['title']);
        $update_event->bindParam(':id', $_POST['id']);
        $exec_update = $update_event->execute();
        
        $query_events = "SELECT categoria FROM categoria WHERE id_categoria = :title";
        $query_events2 = "SELECT title,email FROM events WHERE id=:id";
        
        $resultado_events = $conn->prepare($query_events);
        $resultado_events->bindParam(':title', $_POST['title']);
        $resultado_events->execute();
        
        $resultado_events2 = $conn->prepare($query_events2);
        $resultado_events2->bindParam(':id', $_POST['id']);
        $resultado_events2->execute();
        
        $categoria = $resultado_events->fetchAll( PDO::FETCH_ASSOC);
        $info = $resultado_events2->fetchAll( PDO::FETCH_ASSOC);
       
        
        $cat = implode(",",$categoria[0]);
        $nome = $info[0]['title'];
        $email = $info[0]['email'];
        //echo json_encode([$nome,$email]);exit;
        
        if ($exec_update) {
            //mandar email
            $Mailer = new PHPMailer();
            
            //Define que será usado SMTP
            $Mailer->IsSMTP();
            //Enviar e-mail em HTML
	        $Mailer->isHTML(true);
	
	        //Aceitar carasteres especiais
            $Mailer->Charset = 'UTF-8';
            
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
	        //$Mailer->AddAddress('labest@uefs.br');
	        $Mailer->AddAddress('xstrikeesdraspb@gmail.com');
	        
            
            $Mailer->Subject ='=?UTF-8?B?'.base64_encode("Confirmação da edição de agendamento").'?=';

            $conteudo = "Você recebeu a confirmação da edição do laboratório para o equipamento:$cat<br>
            início do agendamento: $start_str<br>
            Final do agendamento: $end_str<br>
            Nome:$nome";
            $Mailer->Body = $conteudo;


            if($Mailer->Send()){
                //echo "E-mail enviado com sucesso";
                $retorna = ['sit' => true, 'msg' => '<div class="alert alert-success" role="alert">Email enviado com sucesso!</div>'];
                session_start();
                $_SESSION['msg'] = '<div class="alert alert-success" role="alert">AGENDAMENTO DO EQUIPAMENTO EDITADO COM SUCESSO!</div>';
            }else{
                //echo !extension_loaded('openssl')?"Not Available":"Available";
                echo json_encode($Mailer->ErrorInfo);
                exit;
            }
        } else {
            $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Evento não foi editado com sucesso!</div>'];
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

header('Content-Type: application/json');
echo json_encode($retorna);exit;
