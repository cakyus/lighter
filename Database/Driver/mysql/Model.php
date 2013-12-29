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

class Model {

	private $schema;
	private $queryResult;

	public function __construct() {
		$this->schema = new Table;
	}

	public function getSchema() {
		return $this->schema;
	}

	public function put() {

		$db = new Connection;
		$sql = 'INSERT INTO `'.$this->schema->name.'`';
		$columnNames = array();
		$columnValues = array();

		$props = $this->getProperties();
		foreach ($props as $name => $value) {
			if (is_object($value)) { continue; }
			if (is_null($value)) { continue; }
			foreach ($this->schema->columns as $column) {
				if ($column['name'] == $name) {
					$columnNames[] = $name;
					if (in_array($column['type'], array(
						'TEXT', 'CHAR', 'VARCHAR'
						))) {
						$columnValues[] = $db->escape($value);
					} else {
						$columnValues[] = $value;
					}
					break;
				}
			}
		}

		$sql .= ' ('.implode(', ',$columnNames).')';
		$sql .= ' VALUES ('.implode(', ',$columnValues).')';

		$db->exec($sql);
	}

	public function set() {

	}

	public function del() {

	}

	public function get() {

		$db = new Connection;
		$sql = 'SELECT * FROM `'.$this->schema->name.'` WHERE ';

		// get primary key
		foreach ($this->schema->indexes as $index) {
			if ($index['type'] != 'PRIMARY') { continue; }
			foreach ($index['columns'] as $columnName) {
				$sql .= '`'.$columnName.'` = ';
				foreach ($this->schema->columns as $column) {
					if ($column['name'] != $columnName) { continue; }
					if (in_array($column['type'], array(
						'TEXT', 'CHAR', 'VARCHAR'
						))) {
						$sql .= $db->escape($this->$columnName);
					} else {
						$sql .= $this->$columnName;
					}
					break;
				}
			}
			break;
		}

		try {
			$recordset = $db->query($sql);
		} catch (\Exception $e) {
			throw $e;
		}

		if (isset($recordset[0])) {
			$record = $recordset[0];
		} else {
			return FALSE;
		}

		$class  = get_class($this);
		$object = new $class;

		foreach ($record as $name => $value) {
			$object->$name = $value;
		}

		return $object;
	}

	/**
	 * Get public properties as an array
	 **/

	private function getProperties() {

		$ref = new \ReflectionClass($this);
		$data = array();

		$props = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
		foreach ($props as $prop) {
			$name = $prop->getName();
			$value = $this->$name;
			$data[$name] = $value;
		}

		return $data;
	}

	public function save() {
		if ($this->get()) {
			return $this->set();
		} else {
			return $this->put();
		}
	}

	public function fetch() {
		
		$db = new Connection;

		if (is_null($this->queryResult)) {
			if ($queryResult = $db->query(
				'SELECT * FROM `'.$this->schema->name.'`'
				)) {
				$this->queryResult = $queryResult;
			}
		}

		if ($result = current($this->queryResult)) {
			next($this->queryResult);

			foreach ($result as $name => $value) {
				$this->$name = $value;
			}

			return $this;
		}

		$this->queryResult = NULL;
		return FALSE;
	}
}
