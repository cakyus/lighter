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

class Table {

	public $name;
	public $columns;
	public $indexes;

	public function __construct() {
		$this->columns = array();
		$this->indexes = array();
	}

	public function get() {
		return TRUE;
	}

	public function put() {

		$sql = 'CREATE TABLE IF NOT EXISTS `'.$this->name.'` ';

		// columns
		$sqlColumns = array();
		foreach ($this->columns as $column) {
			$sqlColumns[] = $this->getSqlColumn($column);
		}
		// indexes
		$sqlIndexes = array();
		foreach ($this->indexes as $index) {
			$sqlIndexes[] = $this->getSqlIndex($index);
		}
		$sqlColumns[] = implode(', ', $sqlIndexes);
		$sql .= '('.implode(' ,', $sqlColumns).')';

		$db = new \Lighter\Database\Connection;
		return $db->exec($sql);
	}

	public function set() {
		$this->del();
		$this->put();
	}

	public function del() {

		$sql = 'DROP TABLE IF EXISTS `'.$this->name.'`';

		$db = new \Lighter\Database\Connection;
		return $db->exec($sql);
	}

	private function getSqlColumn($column) {

		$sql = '`'.$column['name'].'`'
			.' '.$column['type']
			;

		if (isset($column['size'])) {
			$sql .= ' ('.$column['size'].') ';
		}

		if (	isset($column['autoincrement'])
			&&	$column['autoincrement'] == TRUE
			) {
			$sql .= ' AUTO_INCREMENT';
		}

		return $sql;
	}

	private function getSqlIndex($index) {

		$sql = ' CONSTRAINT ';

		if ($index['type'] == 'PRIMARY') {
			$sql .= 'PRIMARY KEY';
		} else {
			$sql .= $index['type'];
		}

		$sqlColumns = array();
		foreach ($index['columns'] as $column) {
			$sqlColumns[] = '`'.$column.'`';
		}
		$sql .= ' ('.implode(',', $sqlColumns).')';

		return $sql;
	}
}
