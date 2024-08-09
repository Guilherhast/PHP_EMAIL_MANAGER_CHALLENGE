<?php

require_once("tokenLib.php");

header('Content-Type: application/json');
echo json_encode(["logged" => isLogged()]);

?>
