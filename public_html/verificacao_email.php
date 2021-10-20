<?php
session_start();

include_once './conexao.php';

$id = $_POST['id'];
$email = $_POST['email'];

$query_data =  "SELECT COUNT(*)
    FROM events 
    WHERE id = $id 
    AND email = '$email'";

$smtl_cat = $conn->prepare($query_data);
//$smtl_cat->bindParam(':title', $_POST['title']);
$smtl_cat->execute();
//$smtl_cat->fetchAll();
$count = $smtl_cat->fetchAll();

if (($count[0][0] == 0)){
    $retorna = ['sit' => false, 'msg' => 'Email do agendamento cadastrado está incorreto'];

}else {
    $retorna = (['sit' => true, 'msg' => 'Email do agendamento cadastrado está correto.']);
}
echo json_encode($retorna);
exit;