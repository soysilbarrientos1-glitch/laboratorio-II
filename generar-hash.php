<?php
$contrasena_plana = "1234";
$hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);
echo $hash;
?>