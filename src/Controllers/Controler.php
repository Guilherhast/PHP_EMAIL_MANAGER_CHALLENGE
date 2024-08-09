<?php
// Form functions

// Usage:
// $controller = new Controller();
// $controller->handleRequest();


// Auxiliary functions
class Controller {

	public $methods = array(
		'GET' => 'r_get',
		'POST' => 'r_post',
		'UPDATE' => 'r_update',
		'PUT' => 'r_update',
		'DELETE' => 'r_delete',
	);

	public $statusLabel = array(
		200 => "Ok",
		403 => "Forbidden",
	);

	private $guards;

	function __construct($guards = []) {
		$this->guards = $guards;
	}

	// Crud functions
	function r_get() {
		return  array_key_exists('id', $_GET) ? $this->r_getOne($_GET['id']) : $this->r_getAll();
	}


	function r_getOne($id) {
		return array(
			"message" => "This should return one element with id: $id.",
		);
	}

	function r_getAll() {
		return array(
			"message" => "This should return all elements.",
		);
	}

	function r_post() {
		array(
			"message" => "This should create a new element.",
		);
	}

	function r_update() {
		$id = $_GET['id'];
		array(
			"message" => "This should update the element with id: $id.",
		);
	}
	function r_delete() {
		$id = $_GET['id'];
		return array(
			"message" => "This should delete the element with id: $id.",
		);
	}

	// Request handlers
	function checkGuards() {
		foreach ($this->guards as $msg => $guard) {
			if (!$guard()) {
				return (object)array(
					"safe" => false,
					"msg" => $msg,
				);
			}
		}
		return (object)array("safe" => true);
	}

	function createResponse() {
		$req_type = $_SERVER['REQUEST_METHOD'];

		// Handling requests
		$check = $this->checkGuards();
		if ($check->safe == false) return $this->resp_forbidden($check->msg);
		else return $this->resp_ok($this->{$this->methods[$req_type]}());
	}

	function handleRequest() {
		$this->send_response($this->createResponse());
	}

	// Response sender
	function send_response($obj) {
		$label  = $this->statusLabel[$obj->status];
		header("HTTP/1.0 $obj->status $label");
		header('Content-Type: application/json');
		echo json_encode($obj->data);
	}

	function resp_forbidden($data) {
		return (object)array(
			"status" => 403,
			"data" =>
			json_encode(array(
				"message" => "Forbiden: $data.",
			)),
		);
	}

	function resp_ok($data) {
		return (object)array(
			"status" => 200,
			"data" => $data
		);
	}
}
