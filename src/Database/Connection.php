<?php

require_once(__DIR__ . "/" . "../jwtConfig.php");


function getDefault($a, $b){//TODO: change to arbitrary number of params
	if ( !$a || $a == "") return $b;
	return $a;
}

class MysqlPDO extends PDO {
	function __construct(
		$username = null,
		$password = null,
		$host = null,
		$port = null,
		$database = null,
		$socket = null,
	) {
		$host_str = "host=" . ($host ? $host : "localhost");
		$port_str = $port ? "port=$port" : "";
		$database_str = $database ? "dbname=$database" : "";
		$socket_str = $socket ? "unix_socket=$socket" : "";

		$dsn = "mysql:$host_str;$port_str;$database_str;$socket_str";

		parent::__construct($dsn, $username, $password);
	}
// mysql:1;port=1;dbname=1;
	static function fromEnv(
		$username = null,
		$password = null,
		$host = null,
		$port = null,
		$database = null,
		$socket = null,
	) {
		return new MysqlPDO(
			getDefault($username, getDefault($_ENV['DATABASEUSER'], "root")),
			getDefault($password, getDefault($_ENV['DATABASEPASSWORD'],null)),
			getDefault($host, getDefault($_ENV['DATABASEHOST'],"localhost")),
			getDefault($port, getDefault($_ENV['DATABASEPORT'],3306)),
			getDefault($database, getDefault($_ENV['DATABASEDBNAME'],null)),
			getDefault($socket, getDefault($_ENV['DATABASESOCKET'],null))
		);
	}
}
