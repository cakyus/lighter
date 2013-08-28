<?php

namespace Lighter\Console;

class Application {
	
	public function start() {
	
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
}
