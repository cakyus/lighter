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

namespace Lighter\Console;

class Application {
	
	public function start() {
		
		// register autoload
		spl_autoload_register(array(__CLASS__, 'load'));
		
		// defaults
		$controller = 'Index';
		$function = 'index';
		
		include(APPLICATION_PATH.'/Controller/Index.php');
		$object = new \Controller\Index;
		
		if ($_SERVER['argc'] > 2) {
			$controller = '\\Controller\\'.ucfirst($_SERVER['argv'][1]);
			$function = $_SERVER['argv'][2];
		} elseif ($_SERVER['argc'] == 1) {
			// controler and function is not specified
			// use defaults
		} elseif ($_SERVER['argc'] == 2) {
			// function is not specified
			$controller = '\\Controller\\'.ucfirst($_SERVER['argv'][1]);
		} else {
			// bad request
			throw new \Exception('Bad request');
		}
		
		$object->$function();
	}
	
	public function load($class) {
		
		$file = str_replace('\\', '/', $class);
		$items = explode('/', $file);
		
		if (in_array($items[0], array('Controller','Model','View')) == false) {
			return false;
		}
		
		if (is_file(APPLICATION_PATH.'/'.$file.'.php') == false) {
			throw new \Exception('Unable to read file. '.$file);
		}
		
		return require_once(APPLICATION_PATH.'/'.$file.'.php');
	}
}
