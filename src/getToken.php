<?php

require_once("tokenLib.php");

// Sending token
sendCookie($secret_key);
header('Content-Type: application/json');
if (isset($_GET['redirect']) && $_GET['redirect'] == "true") {
	header('Location: /');
}else {
	echo json_encode(["message"=> "Token set successfully."]);
}
?>
