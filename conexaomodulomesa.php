<?php
$db_name="modulomesa1122";
$mysql_user = "root";
$mysql_pass= "pctrim";
$server_name="92.113.33.100:3308";
$con = mysqli_connect($server_name, $mysql_user, $mysql_pass, $db_name);
mysqli_set_charset($con,"utf8");
if(!$con){
echo "Erro na conexao ".mysqli_connect_error();
} else 
{
$id='1204';
$keyapi="AIzaSyAnEWYhrBHFmfC50pPshcw1wNhZ7oUGHnw";
//echo "Conexão criada com sucesso!" ;
}

?>
