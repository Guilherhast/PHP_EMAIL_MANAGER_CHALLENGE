<?php

require_once("Controllers/Controler.php");
require_once("tokenLib.php");
require_once("Database/Connection.php");
require_once("Services/Service.php");

// Class definition
class EmailController extends Controller {
	private $service;

	function __construct($guards, $service) {
		$this->service = $service;
		parent::__construct($guards);
	}

	function r_getOne($id) {
		return $this->service->getOne($id);
	}

	function r_getAll() {
		if ( isset($_GET['limit']) ){
			return (
				!isset($_GET['offset']) ?
				$this->service->getAll($_GET['limit']) :
				$this->service->getAll($_GET['limit'], $_GET['offset'])
			);
		}
		return $this->service->getAll();
	}

	function r_post() {
		return $this->service->create(
				(array)json_decode($_POST['data'])
		);
	}

	function r_update() {
		$id = $_GET['id'];
		array(
			"message" => "This should update the email with id: $id.",
		);
	}
	function r_delete() {
		$id = $_GET['id'];
		return array(
			"message" => "This should delete the email with id: $id.",
		);
	}
}

// Setting up guards
$guards = array(
	"Login required" => function () {
		return isLogged();
	}
);
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

// Handling the requests
$emailController = new EmailController($guards, $emailService);
$emailController->handleRequest();
