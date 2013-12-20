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

namespace Lighter\Http;

class Request {

    public $status;
    public $statusText;
    public $responseText;
    private $responseHeaderText;

    private $handle;

    private $method;
    protected $url;

    private $responseHeaders;
    private $bodyLength;

    private $resolveTimeout;
    private $connectTimeout;
    private $sendTimeout;
    private $receiveTimeout;

    public function __construct() {
        $this->responseHeaders = array();
    }

    public function open($method, $url) {

        $this->method = $method;
        $this->url = str_replace(array(' '), array('%20'), $url);

        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_URL, $this->url);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HEADER, 1);
		curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
    }

    public function send($body=null) {

        \Lighter\Logger::debug($this->method, $this->url);

        // timeouts
        if (is_null($this->resolveTimeout) == false) {
            curl_setopt($this->handle, CURLOPT_TIMEOUT, $this->resolveTimeout);
        }
        if (is_null($this->connectTimeout) == false) {
            curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        }
        if (is_null($this->sendTimeout) == false) {
            curl_setopt($this->handle, CURLOPT_TIMEOUT, $this->sendTimeout);
        }
        if (is_null($this->receiveTimeout) == false) {
            curl_setopt($this->handle, CURLOPT_TIMEOUT, $this->connectTimeout);
        }

        if (curl_errno($this->handle)) {
            $error = curl_error($this->handle);
            curl_close($this->handle);
            throw new \Exception($error);
        }

        $content = curl_exec($this->handle);
        if (curl_errno($this->handle)) {
            $error = curl_error($this->handle);
            throw new \Exception($error);
        }

        $headerLength = curl_getinfo($this->handle, CURLINFO_HEADER_SIZE);
        $bodyLength = curl_getinfo($this->handle, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $this->status = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

        $contentLength = $headerLength + $bodyLength;

        $this->responseText = substr($content, $headerLength);
        $this->responseHeaderText = substr($content, 0, $headerLength - 4);
    }

    public function getResponseHeader($name) {
        if (preg_match("/^$name: (.+)$/mi"
            ,$this->responseHeaderText,$match)) {
            return $match[1];
        }
        return false;
    }

    public function getAllResponseHeaders() {
        return $this->responseHeaderText;
    }

    public function setProxy($url) {

        $items = parse_url($url);

        foreach (array('host', 'port', 'user', 'pass') as $name) {
            if (isset($items[$name]) == false) {
                throw new \Exception("$name is not specified");
            }
            break;
        }

        curl_setopt($this->handle, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($this->handle, CURLOPT_PROXYPORT, $items['port']);
        curl_setopt($this->handle, CURLOPT_PROXY, $items['host']);
        curl_setopt($this->handle, CURLOPT_PROXYUSERPWD
			, $items['user'].':'.$items['port']
            );
    }

    public function setRequestHeader($header) {
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $header);
    }

    public function setResolveTimeout($seconds) {
        $this->resolveTimeout = $seconds;
    }

    public function setConnectTimout($seconds) {
        $this->connectTimeout = $seconds;
    }

    public function setSendTimeout($seconds) {
        $this->sendTimeout = $seconds;
    }

    public function setReceiveTimeout($seconds) {
        $this->receiveTimeout = $seconds;
    }

    public function __destruct() {
        if ($this->handle) {
            curl_close($this->handle);
        }
    }

}
