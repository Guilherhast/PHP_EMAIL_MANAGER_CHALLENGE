<?php

require_once(__DIR__ . "/../Database/Connection.php");
require_once(__DIR__ . "/Service.php");

$emailsTable = "successful_emails";
$emailsModel = array(
	'id' => PDO::PARAM_INT,
	'affiliate_id' => PDO::PARAM_INT,
	'envelope' => PDO::PARAM_STR,
	'from' => PDO::PARAM_STR,
	'subject' => PDO::PARAM_STR,
	'dkim' => PDO::PARAM_STR,
	'SPF' => PDO::PARAM_STR,
	'spam_score' => PDO::PARAM_STR,
	'email' => PDO::PARAM_STR,
	'raw_text' => PDO::PARAM_STR,
	'sender_ip' => PDO::PARAM_STR,
	'to' => PDO::PARAM_STR,
	'timestamp' => PDO::PARAM_INT,
);

$db = MysqlPDO::fromEnv();
$emailService = new Service($db, $emailsTable, $emailsModel);

?>
