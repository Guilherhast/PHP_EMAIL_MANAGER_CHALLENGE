<?php

require_once("Controllers/Controler.php");


class EmailController extends Controller {
	function r_getOne($id) {
		return array(
			"message" => "This should return one email with id: $id.",
		);
	}

	function r_getAll() {
		return array(
			"message" => "This should return all emails.",
		);
	}

	function r_post() {
		array(
			"message" => "This should create a new email.",
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


$emailController = new EmailController();
$emailController->handleRequest();
