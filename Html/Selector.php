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

namespace Lighter\Html;

class Selector {

    private $html;

    public function find($pattern, $index=null) {

		if (preg_match_all(
			"/<".$pattern.".+<\/".$pattern.">/U"
			,$this->html,$match) == false) {
			return false;
		}

		$match = $match[0];

        if (is_null($index) == false) {

			if ($index > count($match)) {
				var_dump($match); die();
				throw new \Exception("x");
				return false;
			}

            $element = new Selector;
            $element->loadHTML($match[$index]);

            return $element;
        }

        $elements = array();
        for ($i = 0; $i < count($match); $i++){
            $element = new Selector;
            $element->loadHTML($match[$i]);
            $elements[] = $element;
        }

        return $elements;
    }

    public function loadHTML($source) {
        $this->html = $source;
    }

    public function getTextContent() {
        return preg_replace("/<[^>]+>/", '', $this->html);
    }

    public function __get($name) {

        if (preg_match("/<[^\s>]+([^>]+)?>/",$this->html,$match)) {
            if (isset($match[1]) == false) {
                return '';
            }
            $attributesText = $match[1];
        } else {
            return '';
        }

        if (preg_match("/$name=(.+)/",$attributesText,$match)) {
            if (isset($match[1]) == false) {
                return '';
            }
            $attributeText = $match[1];
        } else {
            return '';
        }

        if (substr($attributeText,0,1) == '"') {
            if (preg_match("/\"([^\"]+)/",$attributeText,$match)) {
                $value = $match[1];
            } else {
                return '';
            }
        } elseif (substr($attributeText,0,1) == "'") {
            if (preg_match("/'([^\']+)/",$attributeText,$match)) {
                $value = $match[1];
            } else {
                return '';
            }
        } else {
            if (preg_match("/([^\s]+)/",$attributeText,$match)) {
                $value = $match[1];
            } else {
                return '';
            }
        }

        return $value;
    }

    public function __toString() {
        return $this->html;
    }
}
