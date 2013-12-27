<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 **/

namespace Lighter\Database;

class Connection {

	private $handle;
	private static $instance;

    public function __construct() {
		$this->handle = self::getInstance();
    }

    public static function getInstance() {

		if (is_null(self::$instance) == FALSE) {
			return  self::$instance;
		}

    	$config = new \Lighter\Config;
    	self::$instance = self::open($config->database);

    	return self::$instance;
	}

	public static function open($url) {

    	if (!$items = parse_url($url)) {
			throw new \Exception("Invalid configuration");
		}

		if (isset($items['host']) == FALSE) {
			$items['host'] = 'localhost';
		}

		if (isset($items['port']) == FALSE) {
			$items['port'] = 3306;
		}

		if (isset($items['user']) == FALSE) {
			$items['user'] = 'root';
		}

		if (isset($items['pass']) == FALSE) {
			$items['pass'] = '';
		}

		if (isset($items['path']) == FALSE) {
			throw new \Exception("Invalid configuration");
		} else {
			$items['path'] = substr($items['path'], 1);
		}

		if (!$handle = mysql_connect(
			$items['host'], $items['user'], $items['pass']
			)) {
			throw new \Exception("Cant connect to database");
		}

		if (!mysql_select_db($items['path'], $handle)) {
			throw new \Exception("Cant open database");
		}

		return $handle;
	}

	public function close() {
		return mysql_close($this->handle);
	}

	public function query($sql) {

        $result = array();

        if ($query = mysql_query($sql, $this->handle)) {
			while ($rows = mysql_fetch_assoc($query)) {
				$result[] = $rows;
			}
		} else {
			throw new \Exception(
				'mysql: '.mysql_error($this->handle)
				."\n".preg_replace("/\s+/", " ", $sql)
				);
        }

        return $result;
	}

	public function exec($sql) {
		if ($query = mysql_query($sql, $this->handle)) {
			return $query;
		} else {
			throw new \Exception(
				"mysql: ".mysql_error($this->handle)
				."\n".preg_replace("/\s+/", " ", $sql)
				);
		}
		return false;
	}

	public function escape($string) {
        return '"'.mysql_real_escape_string($string, $this->handle).'"';
	}

	public function lastInsertId() {
		return mysql_insert_id($this->handle);
	}

	// transaction
	public function beginTransaction() {}
	public function commit() {}
	public function rollback() {}

}
