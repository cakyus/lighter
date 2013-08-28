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

class Loader {
	
	/**
	 * Regiter autoload callback
	 * 
	 * @return void
	 **/
	
	public static function register() {
		spl_autoload_register(array(__CLASS__, 'load'));		
	}
	
	/**
	 * Autoload callback
	 * 
	 * @return boolean
	 **/
	
	public static function load($class) {
		
		if (class_exists($class)) {
			return false;
		}
		
		// handle current namespace
		$namespaces = explode('\\', $class);
		if ($namespaces[0] != __NAMESPACE__) {
			return false;
		}
		
		// get path of class
		unset($namespaces[0]);
		$file = __DIR__.'/'.implode('/', $namespaces).'.php';
		
		if (!is_readable($file)) {
			throw new \Exception('Unable to read file. '.$file);
		}
		
		require_once($file);
		return true;
	}
}
