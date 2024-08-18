<?php
class ContentParser{
	// Get the separator from the input string
	static function getSeparator($sub) {
		$start = "/------WebKitFormBoundary.*/";
		$matches = null;
		preg_match($start, $sub, $matches);
		return $matches[0];
	}

	// Get all different data fields
	static function getDataFields($sep, $data) {
		$all = preg_split("/$sep(--)?/", $data);
		array_pop($all);
		array_shift($all);
		return  $all;
	}

	// Extract name of data field
	static function getDataName($field) {
		$start = 'Content-Disposition: form-data; name="([^"]*)"';
		$matches = null;
		preg_match("/$start/", $field, $matches);
		return $matches[1];
	}

	// Extract content of data field
	static function getDataContent($field) {
		$start = 'Content-Disposition: form-data; name="([^"]*)"';
		$all = preg_split("/$start/", $field);
		return trim($all[1]);
	}

	// Build a key par ( 'dataFieldName' => dataFieldContent )
	static function buildKeyPair($field) {
		return array(
			ContentParser::getDataName($field) => ContentParser::getDataContent($field)
		);
	}

	// Extract the data from all different data fields
	static function getAllData($rawData) {
		$data = str_replace("\r", '', $rawData);
		$separator = ContentParser::getSeparator($data);
		$fields = ContentParser::getDataFields($separator, $data);
		$all = array();
		foreach($fields as $field){
			$all[ContentParser::getDataName($field)] = ContentParser::getDataContent($field);
		}
		return $all;
	}
}
