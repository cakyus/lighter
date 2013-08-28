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


namespace Lighter\Web;

class Application {
	
	public function start() {
		
		// register autoload
		spl_autoload_register(array(__CLASS__, 'load'));
		
		// defaults
		$controller = 'Index';
		$function = 'index';
		$arguments = array();
		
		if (isset($_REQUEST['p'])) {
			
			$items = explode('/', $_REQUEST['p']);

			if (count($items) > 1) {
				$controller = ucfirst($items[0]);
				$function = $items[1];
			} elseif (count($items) == 0) {
				// controler and function is not specified
				// use defaults
			} elseif (count($items) == 1) {
				// function is not specified
				$controller = ucfirst($items[0]);
			} else {
				// bad request
				throw new \Exception('Bad request');
			}		
		}
		
		$controller = '\\Controller\\'.$controller;
		
		$object = new $controller;
		$object->$function();
	}
	
	public function load($class) {
		
		$file = str_replace('\\', '/', $class);
		$items = explode('/', $file);
		
		if (in_array($items[0], array('Controller','Model','View')) == false) {
			return false;
		}
		
		if (is_readable(APPLICATION_PATH.'/'.$file.'.php') == false) {
			throw new \Exception('Unable to read file. '.$file);
		}
		
		return require_once(APPLICATION_PATH.'/'.$file.'.php');
	}
}
