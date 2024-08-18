<?php

class Service {
	private $db;
	private $table;
	private $model;
	private $primaryKey;

	function __construct($database, $tableName, $model, $primaryKey = 'id') {
		$this->db = $database;
		$this->table = $tableName;
		$this->model = $model;
		$this->primaryKey = $primaryKey;
	}

	function getPrimaryKey(){
		return $this->primaryKey;
	}

	private function prefix_key($key) {
		return ":$key";
	}

	private function keyInCols($key) {
		return in_array($key, array_keys($this->model));
	}

	private function cleanObj($obj) {
		$clean = $obj;
		foreach ($clean as $key => $_) {
			if (!$this->keyInCols($key)) unset($clean[$key]);
		}
		return $clean;
	}

	private function bind_used_values($dict, $keys, $stmt) {
		foreach ($keys as $key) {
			$stmt->bindValue(":$key", $dict[$key], $this->model[$key]);
		}
	}

	// Query generators
	private function genQueryCreate($dict) {
		$keys = array_keys($dict);
		return array(
			"cols" => implode(', ', array_map(function ($el) {
			return "`$el`";
		}, $keys)),
			"holder" => implode(', ', array_map(function ($el) {
				return $this->prefix_key($el);
			}, $keys)),
		);
	}

	private function genQueryUpdate($dict) {
		$keys = array_keys($this->cleanObj($dict));
		return implode(", ", array_map(function ($k) {
			return "`$k` = :$k";
		}, $keys));
	}

	// Crud functions
	function before_return($obj) {
		return $obj;
	}

	function getAll($limit = null, $offset = null) {
		$query = "SELECT * FROM $this->table";
		if ($limit != null) {
			$query .= " LIMIT :limit";
			if ($offset != null) {
				$query .= " OFFSET :offset ";
			}
		}
		$stmt = $this->db->prepare($query);
		if ($limit != null) {
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
			if ($offset != null) {
				$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
			}
		}
		$stmt->execute();
		return $this->before_return($stmt->fetchAll(PDO::FETCH_CLASS));
	}

	function getOne($key) {
		$stmt = $this->db->prepare("SELECT * FROM $this->table WHERE $this->primaryKey=:key");
		$stmt->bindValue(':key', $key, $this->model[$this->primaryKey]);
		$stmt->execute();
		return $this->before_return($stmt->fetch(PDO::FETCH_ASSOC));
	}

	function pre_create($obj) {
		return $this->cleanObj($obj);
	}

	function create($obj) {
		$obj = $this->pre_create($obj);
		$info = $this->genQueryCreate($obj);

		// Building and preparing the query
		$model = $info['cols'];
		$values = $info['holder'];

		$query = "INSERT INTO $this->table ($model) VALUES ($values)";
		$stmt = $this->db->prepare($query);
		$this->bind_used_values($obj, array_keys($obj), $stmt);
		// Running and returning the query
		$stmt->execute();
		return $this->before_return(array(
			"id" => $this->db->lastInsertId()
		));
	}

	function pre_update($obj) {
		$clean = (array)$this->cleanObj($obj);
		// Disable changing primary key
		if(isset($clean[$this->primaryKey])){
			unset($clean[$this->primaryKey]);
		}
		return $clean;
	}

	function update($key, $obj) {
		$obj = $this->pre_update($obj);
		$asgn_list = $this->genQueryUpdate($obj);
		// Building and executing the query
		$query = "UPDATE $this->table SET $asgn_list WHERE $this->primaryKey=:$this->primaryKey";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(":$this->primaryKey", $key, $this->model[$this->primaryKey]);
		$this->bind_used_values($obj, array_keys($obj), $stmt);
		return $this->before_return(array(
			'success'=>$stmt->execute()
		));
	}

	function delete($key) {
		$stmt = $this->db->prepare("DELETE FROM $this->table WHERE $this->primaryKey=:$this->primaryKey");
		$stmt->bindValue(":$this->primaryKey", $key, $this->model[$this->primaryKey]);
		return $this->before_return(array(
			'success'=>$stmt->execute()
		));
	}
}
