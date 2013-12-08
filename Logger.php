<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General public static License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General public static License for more details.
 *
 * You should have received a copy of the GNU General public static License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 **/

namespace Lighter;

class Logger {

	public static function debug() {
		self::write('DEBUG',   debug_backtrace(), func_get_args());
	}

	public static function error() {
		self::write('ERROR',   debug_backtrace(), func_get_args());
	}

	public static function info() {
		self::write('INFO',    debug_backtrace(), func_get_args());
	}

	public static function warning() {
		self::write('WARNING', debug_backtrace(), func_get_args());
	}

	private static function write($type, $debug, $args) {

		$info = '';
		$message = implode(' ', $args);

		if (isset($debug[1])){
			if (isset($debug[1]['class'])){
				$info .= $debug[1]['class'];
			}
			if (isset($debug[1]['function'])){
				$info .= ' '.$debug[1]['function'];
			}
			$message = $info.' '.$message;
		}

		echo date('H:i:s')." $type $message\n";
	}
}
