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

namespace Lighter;

class Console {
	
	public static function debug() {
		
		$arguments = func_get_args();
		$debug = debug_backtrace();
		$debugInfo = '';
		$fileLog = APPLICATION_PATH.'\data\\'.$_SERVER['REQUEST_TIME'].'.log';
		
		if (isset($debug[1])){
			if (isset($debug[1]['class'])){
				$debugInfo .= ' '.$debug[1]['class'];
			}
			if (isset($debug[1]['function'])){
				$debugInfo .= ' '.$debug[1]['function'];
			}
			array_unshift($arguments, $debugInfo);
		}
		
		$info =  date('His').' INFO '.implode(' ', $arguments);
		echo $info."\n";
	}
	
	public static function error() {
		
	}
	
	public static function info() {
		
	}
	
	public static function warn() {
		
	}
	
	public static function log() {
		
	}
}
