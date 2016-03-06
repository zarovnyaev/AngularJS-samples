<?php

class API {
    
    public $method = null;
    public $parameters = array();

    public function __construct() {
        $this->method = $this->getMethod();
        $this->parameters = $this->setParameters();
    }
    
    public function response($data = array(), $code = 200) {
        header("HTTP/1.1 " . $code . " " . $this->getHttpStatus($code));
        return json_encode($data);
    }

    public function __get($name) {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        } else {
            return null;
        }
    }
    
    /**
     * Collected parameters from $_REQUEST and stream and returns this array
     * @return array
     */
    public function setParameters() {
        
        $parameters = array();
        
        // Get from $_REQUEST
        if (count($_REQUEST) > 0) {
            $parameters = array_merge($parameters, $_REQUEST);
        }
        
        // Get from stream
        $stream = trim(file_get_contents("php://input"));
        if ($stream != "") {
            $parameters = array_merge($parameters, (array)json_decode($stream, true));
        }
        
        return $parameters;
    }

    /**
     * Execute process function _{method_type}
     * @return string
     */
    public function process() {
        $method = "_" . strtolower($this->method);
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        } else {
            return $this->response(array(), 405);
        }
    }

    /**
     * Returns current request method
     * @return string
     * @throws Exception
     */
    public function getMethod() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }   
        return $method;
    }
    
    /**
     * Returns HTTP status text of specified code
     * @param Int $code
     * @return string
     * @throws Exception
     */
    public function getHttpStatus($code) {
        $codes = array(
            // 1xx: Informational
            100 => "Continue",
            101 => "Switching Protocols",
            102 => "Processing",
            // 2xx: Success
            200 => "OK",
            201 => "Created",
            202 => "Accepted",
            203 => "Non-Authoritative Information",
            204 => "No Content",
            205 => "Reset Content",
            206 => "Partial Content",
            207 => "Multi-Status",
            208 => "Already Reported",
            226 => "IM Used",
            // 3xx: Redirection
            300 => "Multiple Choices",
            301 => "Moved Permanently",
            302 => "Found",
            303 => "See Other",
            304 => "Not Modified",
            305 => "Use Proxy",
            307 => "Temporary Redirect",
            308 => "Permanent Redirect",
            // 4xx: Client Error
            400 => "Bad Request",
            401 => "Unauthorized",
            402 => "Payment Required",
            403 => "Forbidden",
            404 => "Not Found",
            405 => "Method Not Allowed",
            406 => "Not Acceptable",
            407 => "Proxy Authentication Required",
            408 => "Request Timeout",
            409 => "Conflict",
            410 => "Gone",
            411 => "Length Required",
            412 => "Precondition Failed",
            413 => "Request Entity Too Large",
            414 => "Request-URI Too Long",
            415 => "Unsupported Media Type",
            416 => "Requested Range Not Satisfiable",
            417 => "Expectation Failed",
            422 => "Unprocessable Entity",
            423 => "Locked",
            424 => "Failed Dependency",
            426 => "Upgrade Required",
            428 => "Precondition Required",
            429 => "Too Many Requests",
            431 => "Request Header Fields Too Large",
            444 => "No Response",
            449 => "Retry With",
            451 => "Unavailable For Legal Reasons",
            499 => "Client Closed Request",
            // 5xx: Server Error
            500 => "Internal Server Error",
            502 => "Bad Gateway",
            503 => "Service Unavailable",
            504 => "Gateway Timeout",
            505 => "HTTP Version Not Supported",
            506 => "Variant Also Negotiates",
            507 => "Insufficient Storage",
            508 => "Loop Detected",
            509 => "Bandwidth Limit Exceeded",
            510 => "Not Extended",
            511 => "Network Authentication Required",
            598 => "Network read timeout error",
            599 => "Network connect timeout error",
        );
        
        if (!isset($codes[$code])) {
            throw new Exception("Wrong response HTTP status code");
        }
        
        return $codes[$code];
    }
    
}

