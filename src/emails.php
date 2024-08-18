<?php

require_once("Controllers/Controler.php");
require_once("tokenLib.php");
require_once("Services/emails.service.php");
require_once("Libraries/contentParser.php");

// Class definition
class EmailController extends Controller {
	private $service;
	private $extractor;

	function __construct($guards, $service, $extractor) {
		$this->service = $service;
		$this->extractor = $extractor;
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
		$_PUT = $this->extractor::getAllData(file_get_contents('php://input'));
		return $this->service->update($id, json_decode($_PUT['data']));
	}

	function r_delete() {
		$id = $_GET['id'];
		return $this->service->delete($id);
	}
}

// Setting up guards
$guards = array(
	"Login required" => function () {
		return isLogged();
	}
);

// Handling the requests
$emailController = new EmailController($guards, $emailService, new ContentParser());
$emailController->handleRequest();
