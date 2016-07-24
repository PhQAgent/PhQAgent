<?php
namespace httpd;

class ResponseHeader{

    private $status;
    private $content;
    private $type;

    private function getStatus(){
        $status = $this->status;
        if(isset(static::$STATUS_MAP[$status])){
            return "HTTP/1.1 {$status} {static::$STATUS[$status]}\r\n";
        }else{
            return "HTTP/1.1 500 {static::$STATUS_MAP[500]}\r\n";
        }
    }

    private function getContentType(){
        return "Content-Type: {$this->type}\r\n";
    }

    private function getContentLenth(){
        $lenth = strlen($this->content);
        return "Content-Length: {$lenth}\r\n";
    }

    private function getServerName(){
        return "Server: {AodHTTPD::NAME}\r\n";
    }
    
    private function getContent(){
        return $this->content;
    }
    
    public function __construct($STATUS = '500', $CONTENT = '', $TYPE = 'text/html'){
        $this->status = $STATUS;
        $this->content = $CONTENT;
        $this->type = $TYPE;
    }

    public function __tostring(){
        return(
            $this->getStatus().
            $this->getContentLenth().
            $this->getContentType().
            $this->getServerName().
            '\r\n'.
            $this->getContent()
        );

    }

    static $STATUS_MAP = [
	    100 => "Continue",
	    101 => "Switching Protocols",
	    200 => "OK",
	    201 => "Created",
	    202 => "Accepted",
	    203 => "Non-Authoritative Information",
	    204 => "No Content",
	    205 => "Reset Content",
	    206 => "Partial Content",
	    300 => "Multiple Choices",
	    301 => "Moved Permanently",
	    302 => "Found",
	    303 => "See Other",
	    304 => "Not Modified",
	    305 => "Use Proxy",
	    307 => "Temporary Redirect",
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
	    500 => "Internal Server Error",
	    501 => "Not Implemented",
	    502 => "Bad Gateway",
	    503 => "Service Unavailable",
	    504 => "Gateway Timeout",
	    505 => "HTTP Version Not Supported",
    ];

}