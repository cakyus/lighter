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

namespace Lighter\System\Directory;

class Temp {
	
	private $path;
	
	public function __construct() {
		$this->path = sys_get_temp_dir();
		$this->path .= '\\'.uniqid('php');
		\Lighter\Console::debug($this->path);
		mkdir($this->path);
	}
	
	public function addDir($dir) {
		
	}
	
	public function addFile($file) {
		
	}
	
	public function getPath() {
		
	}
}
