<?php

require_once(__DIR__ . "/../vendor/autoload.php");
require_once("../Services/emails.service.php");

// In orther to be more generic you could receive --primaryKey=key

class GenericRetriever {
	// Constants
	private $options = array(
		"--id" => "setId",
		"--raw" => "setUseRaw",
		"--manny" => "setReturnManny",
		"--limit" => "setLimit",
		"--offset" => "setOffset",
		"--save" => "setSave",
		"--all" => "setAll",
		"--quiet" => "setQuiet",
		"--primaryKey" => "setPrimaryKey",
	);

	private $service; // Service to retrieve data
	private $parser; // Parser do deal with emails
	private $tableColName; // The column of interest
	private $separator; // The separator used before print

	// Methods
	private $getterFunction = "getOne"; // Function used to retrieve
	private $filterFunction = "noFilter"; // Function used to filter after retrieve
	private $echoFunction = "echo"; // Function used to write data

	// Variables
	private $getAll = false;
	private $window = 10;

	private $id = 1; // Id of the returned element ( when only one is nedded )

	private $maxKey = 500; // How manny will be returned

	private $limit = null; // How manny will be returned
	private $offset = null; // First element

	private $saveCol = null; // Column where to save data


	// Constructor
	function __construct($service, $parser, $tableColName, $separator = "\n") {
		$this->service = $service;
		$this->parser = $parser;
		$this->tableColName = $tableColName;
		$this->separator = $separator;
	}

	// Set functions

	// Setting constants
	function setId($value) {
		$this->id = $value;
	}

	function setLimit($value) {
		$this->limit = $value;
	}

	function setOffset($value) {
		$this->offset = $value;
	}

	function setSave($value) {
		$this->saveCol = $value;
	}

	// Setting methods
	function setUseRaw() {
		$this->filterFunction = "rawFilter";
	}

	function setReturnManny() {
		$this->getterFunction = "getManny";
	}

	function setQuiet() {
		$this->echoFunction = "silent";
	}

	function setAll() {
		$this->getAll = true;
		$this->setReturnManny();
		if($this->offset == null ) $this->setOffset(0);
	}
	// Echo functions
	private function echo($str) {
		echo $str;
		echo $this->separator;
	}

	private function silent($str) {
		return $str;
	}

	// Getter functions
	private function getOne() {
		// Using the service to retrieve data
		// Returning only the required column
		// as array for compatibility with manny
		return array(
			$this->service->getOne($this->id)
		);
	}

	private function getManny() {
		return	array_map(
			function ($el) {
				// Casting $el to array
				return (array) $el;
			},
			$this->service->getAll($this->limit, $this->offset)
		);
	}

	private function updateOne(){

	}

	// Filter functions
	private function noFilter($str) {
		return $str;
	}

	private function rawFilter($str) {
		$this->parser->setText($str);
		return $this->parser->getMessageBody('text');
	}


	// Row manipulation functions
	// Return an array with only the fields
	// present in the $keys arg
	private function cropRow($row, $keys){
		$croped_row = array();
		foreach($keys as $key){
			$croped_row[$key] = $row[$key];
		}
		return $croped_row;
	}

	// Use cropRow to crop all rows
	private function cropManyRows($all, $keys){
		// Return a mapped array
		return array_map(
			// Create a function that receives the
			// same args as $this->cropRow
			function ($row) use ($keys){
				// Send these args to cropRow
				return $this->cropRow($row, $keys);
			},
			// Pass each row of $all to that function
			$all
		);
	}

	// Entry action Methods
	private function actionPrint($data){
		$col = ($this->saveCol == null? $this->tableColName: $this->saveCol);
		// Using the filter to get only required data
		$filtered =
			$this->{$this->filterFunction}($data[$col]);
		// Echoing depending on what the user want
		$this->{$this->echoFunction}($filtered);
	}

	private function actionPrintAll($collection){
		foreach($collection as $str){
			$this->actionPrint($str);
		}
	}

	private function actionSave($data){
		// Get the keys of the object
		$primaryKey = "id"; // Fixme it is a magic word
		$origin = $this->tableColName;
		$destin = $this->saveCol;

		// Getting the uniq identifier using the primary key
		$identifier = $data[$primaryKey];

		// Filter data and save in the destin column
		$proccessed = $this->{$this->filterFunction}($data[$origin]);
		$data[$destin] =  $proccessed;

		// Cleaning the array for optimisation
		unset($data[$primaryKey]);
		//unset($data[$origin]);

		print_r($data);
		// Updating using the service
		$this->service->update($identifier, $data);
		return $data;
	}

	private function actionSaveAll($collection){
		return array_map( function ($el)
		{
			return $this->actionSave($el);
		},
			$collection
		);
	}

	// Run functions
	function config($args) {
		foreach ($args as $arg) {
			$splited = explode('=', $arg);
			// Choosing the propper setter function based on the arg
			$setter = $this->options[$splited[0]];
			// Using the setter function
			// Passing the value as it's arg if it has
			if (isset($splited[1])) $this->{$setter}($splited[1]);
			// Calling without args
			else $this->{$setter}();
		}
	}

	private function execAll(){
		do{
			$this->offset += $this->window;
			$this->execRange();
		}while($this->offset < $this->maxKey);
	}

	private function execRange(){
		$entries = $this->{$this->getterFunction}();


		// Fixme: "id" is a magic word ( change for $this->primaryKey )
		// Retrieving origin, id, destin
		$cropped = $this->cropManyRows($entries, [$this->tableColName, "id", $this->saveCol]);

		if ($this->saveCol != null) {
			// Add the new field to the data
			$after = $this->actionSaveAll($cropped);
			$cropped = $after;
		}
		$this->actionPrintAll($cropped);

		// Retrieving only the required columns from all rows
		//$cropped = $this->cropManyRows($entries, [$this->tableColName]);

	}

	function exec() {
		global $argv;
		$userArgs = $argv;
		array_shift($userArgs);
		$this->config($userArgs);

		if ( $this->getAll ){
			$this->execAll();
		}else{
			$this->execRange();
		}
	}
}

$er = new GenericRetriever(
	$emailService,
	new eXorus\PhpMimeMailParser\Parser(),
	"email",
	"\n\n---------------\n\n"
);

print_r($er->exec());
