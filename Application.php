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

class Application {

    public function start() {

        spl_autoload_register(array($this, 'autoload'));
        set_exception_handler(array($this, 'exceptionHandler'));
        set_error_handler(array($this, 'errorHandler'));

        // defaults
        $controller = 'index';
        $function = 'index';
        $arguments = array();

        if (PHP_SAPI == 'cli') {
			if ($_SERVER['argc'] == 1) {
				// no arguments => Index/index
			} elseif ($_SERVER['argc'] == 2) {
				// 1 argument => Index/<argument1>
				$function = $_SERVER['argv'][1];
			} elseif ($_SERVER['argc'] == 3) {
				// 2 argument => <argument1>/<argument2>
				$controller = $_SERVER['argv'][1];
				$function = $_SERVER['argv'][2];
			} else {
				// > 2 argument => <argument1>/<argument2> <argument3>
				$controller = $_SERVER['argv'][1];
				$function = $_SERVER['argv'][2];
				$arguments = array_slice($_SERVER['argv'], 3);
			}
		} elseif (is_null(key($_GET)) == false) {

			// take the first key in QueryString
			// eg. /index.php?/<controller>/<function>/<argument1>/...

			$items = explode('/', key($_GET));
			array_shift($items);

			if (count($items) > 1) {
				$controller = array_shift($items);
				$function = array_shift($items);
				$arguments = $items;
			} elseif (count($items) == 0) {
				// controler and function is not specified
				// use defaults
			} elseif (count($items) == 1) {
				// function is not specified
				$controller = $items[0];
			}
		}

        // class and function name validation
        if (    preg_match("/^[a-z][a-z0-9]+$/", $controller)
            &&  preg_match("/^[a-z][a-z0-9]+$/", $function)
            ) {
            // do nothing
        } else {
            throw new \Exception("bad request");
        }

        $controller = ucfirst($controller);
        $file = APPLICATION_PATH.DIRECTORY_SEPARATOR
            .'Controller'.DIRECTORY_SEPARATOR
            .$controller.'.php'
            ;

        if (is_file($file) == false) {
            throw new \Exception("file not found $file");
        }

        $controller = '\\Controller\\'.$controller;

        $object = new $controller;

        call_user_func_array(array($object, $function), $arguments);
    }

    public function exceptionHandler($exception) {
        echo "\n".$exception->getMessage()
            ."\nin ".$exception->getFile()
            ." on line ".$exception->getLine()
            ."\n\n".$exception->getTraceAsString()
            ."\n\n";
        exit(1);
    }

    public function errorHandler($number, $message, $file, $line, $content) {
        echo "\n$message"
            ."\nin $file on line $line"
            ."\n\n";
        debug_print_backtrace();
        exit(1);
    }

    public function autoload($class) {

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
