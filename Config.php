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

/**
 * Configuration
 * =============
 * The main configuration file holds all required key.
 * Values which are exists in custom configuration file
 *   SHOULD NOT be used unless the key is exists
 *   in main configuration file.
 * Custom configuration file can be used to store application data.
 *
 * Files
 * -----
 * APPLICATION_PATH.'/Data/config.php'
 *     This file. The main configuration file.
 * APPLICATION_DATA.'/.lighter'
 *     Custom configuration file.
 **/

namespace Lighter;

class Config {

	private static $data;

	public function __construct() {
		self::getInstance();
	}

	public static function getInstance() {
		if (is_null(self::$data)) {
			self::$data = include(APPLICATION_PATH.'/Data/config.php');
			if (	isset($_SERVER['HOME'])
				&&	is_file($_SERVER['HOME'].'/.lighter')
				) {

				$data = include($_SERVER['HOME'].'/.lighter');
				foreach (self::$data as $name => $value) {
					if (isset($data[$name])) {
						self::$data[$name] = $data[$name];
					}
				}
			}
		}
	}

	public function __get($name) {
		if (isset(self::$data[$name])) {
			return self::$data[$name];
		}
		return null;
	}
}
