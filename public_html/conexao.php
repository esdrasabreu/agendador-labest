<?php
/* @author Esdras Abreu
 */
define('HOST', 'localhost');
define('USER', 'id15751512_agendador_labest');
define('PASS', '5za}4xQQsh|!G5Aa');
define('DBNAME', 'id15751512_agendador_db');
define('PORT', 3306);

$conn = new PDO('mysql:host=' . HOST .';port='.PORT.';dbname=' . DBNAME . ';', USER, PASS);
