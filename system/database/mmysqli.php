<?php
final class mMySQLi {
	private $link;
	// private $debug;

	public function __construct($hostname, $username, $password, $database) {
		$this->link = new mysqli($hostname, $username, $password, $database);

		if ($this->link->connect_error) {
			trigger_error('Error: Could not make a database link (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
			die();
		}

		$this->link->set_charset("utf8");
		$this->link->query("SET SQL_MODE = ''");

		// $this->debug = new Log('db_log.txt');
	}

	public function query($sql) {
		$query = $this->link->query($sql);

		// $this->debug->write($sql);

		if (!$this->link->errno) {
			if ($query instanceof mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}

				$result = new stdClass();
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;
				$result->num_rows = $query->num_rows;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			trigger_error('Error: ' . $this->link->error  . '<br />Error No: ' . $this->link->errno . '<br />' . $sql);
		}
	}

	public function escape($value) {
		return $this->link->real_escape_string($value);
	}

	public function countAffected() {
		return $this->link->affected_rows;
	}

	public function getLastId() {
		return $this->link->insert_id;
	}

	public function ping() {
		return $this->link->ping();

		/*
		 * Another ping method, from http://php.net/manual/en/mysqli.ping.php
		 *
		$this->link->query('SELECT LAST_INSERT_ID()');

		if ($this->link->errno == 2006) {
			$this->link->close();
			$this->__construct(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		}
		*
		* */
	}

	public function close() {
		return $this->link->close();
	}

	public function __destruct() {
		$this->link->close();
	}
}
?>
