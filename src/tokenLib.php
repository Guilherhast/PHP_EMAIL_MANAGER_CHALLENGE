<?php

require_once("./jwtConfig.php");

use Firebase\JWT\JWT as JWT;
use Firebase\JWT\Key as Key;

$secret_key = $_ENV['JWT_SECRET'];

// Auxiliary functions
// Findout the domain name
function getDomain() {
	$hostname = $_SERVER['HTTP_HOST'];
	$domain = "http://$hostname";
	return $domain;
}

// Token manipulation functions
// Decode given token
function decodeToken($token, $secret_key, $jwt_algo = 'HS256') {
	return JWT::decode($token, new Key($secret_key, $jwt_algo));
}

// Create a new token
function createNewToken($jwt_secret, $lifeSpan = 3600, $jwt_algo='HS256' ) {
	// Jwt data
	$issuer = getDomain();
	$audience = $issuer;

	// Payload data
	$userEmail = isset($_GET['email']) ? $_GET['email'] : "";

	// Generating token
	$token_payload = array(
		'iss' => $issuer,
		'aud' => $audience,
		'iat' => time(),
		'exp' => time() + $lifeSpan,
		'data' => (object)array(
			"email" => $userEmail
		),
	);

	return JWT::encode($token_payload, $jwt_secret, $jwt_algo);
}

// Cookie functions
// Check if the user is logged
function isLogged(){
	if ( !isset($_COOKIE['auth_token'])) return false;

	$token = $_COOKIE['auth_token'];
	$secret_key = $_ENV['JWT_SECRET'];
	$decoded = decodeToken($token, $secret_key);

	return $decoded->exp > time() && $decoded->data->email !="";
}

// Set a new cookie in the user's browser
function sendCookie($jwt_secret, $lifeSpan = 3600, $jwt_algo='HS256'){
	setcookie('auth_token', createNewToken($jwt_secret, $lifeSpan, $jwt_algo), [
		'expires'=> time() + $lifeSpan,
		'path'=> '/',
		//'secure'=> true,
		'httponly'=> true,
		'samesite'=> 'Strict',
	]);
}


?>
