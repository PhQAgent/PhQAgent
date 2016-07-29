<?php
namespace utils;

class Curl{
    private $curlresource;
    private $url;
    private $content;

    public function __construct(){
        $this->curlresource = curl_init();
        if(substr(php_uname(), 0, 7) == "Windows"){
            curl_setopt($this->curlresource, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->curlresource, CURLOPT_SSL_VERIFYPEER, 0);
        }//Stupid Windows
		curl_setopt($this->curlresource, CURLOPT_RETURNTRANSFER, true);
        $this->returnHeader(true);
        $this->setTimeout(10);
    }

    public function setUrl($url){
        $this->url = $url;
        curl_setopt($this->curlresource, CURLOPT_URL, $url);
        return $this;
    }

    public function returnHeader($bool){
        curl_setopt($this->curlresource, CURLOPT_HEADER, ($bool == true) ? 1 : 0);
        return $this;
    }

    public function returnBody($bool){
        curl_setopt($this->curlresource, CURLOPT_NOBODY, ($bool == false) ? 1 : 0);
        return $this;
    }
    
    public function setCookie($cookies){
        $payload = '';
        foreach($cookies as $key=>$cookie){
            $payload .= "$key=$cookie; ";
        }
        curl_setopt($this->curlresource, CURLOPT_COOKIE, $payload);
        return $this;
    }

    public function setReferer($referer){
        curl_setopt($this->curlresource, CURLOPT_REFERER, $referer);
        return $this;
    }

    public function setGet($get){
        $payload = '?';
        foreach($get as $key=>$content){
            $payload .= urlencode($key).'='.urlencode($content).'&';
        }
        curl_setopt($this->curlresource, CURLOPT_URL, $this->url.$payload);
        return $this;
    }

    public function setPost($post){
        $payload = '';
        foreach($post as $key=>$content){
            $payload .= urlencode($key).'='.urlencode($content).'&';
        }
        curl_setopt($this->curlresource, CURLOPT_POSTFIELDS, $payload);
        return $this;
    }

    public function setEncPost($post){
        curl_setopt($this->curlresource, CURLOPT_POSTFIELDS, $post);
        return $this;
    }

    public function setTimeout($timeout){
	    curl_setopt($this->curlresource, CURLOPT_CONNECTTIMEOUT, $timeout);
        return $this;
    }

    public function keepCookie(){
		curl_setopt($this->curlresource, CURLOPT_COOKIEJAR, '');
		curl_setopt($this->curlresource, CURLOPT_COOKIEFILE, '');
        return $this;
    }

    public function exec(){
        $this->content = curl_exec($this->curlresource);
        return $this->content;
    }

    public function getCookie(){
        preg_match_all('/Set-Cookie: (.*);/iU', $this->content, $cookies);
        $payload = [];
        foreach($cookies[1] as $cookie){
            $key = explode('=', $cookie);
            if(isset($payload[$key[0]]) and $payload[$key[0]] !== ''){
                continue;
            }
            $payload[$key[0]] = $key[1];
        }
		return $payload;
    }

    public function isError(){
        return (curl_errno($this->curlresource)) ? true : false;
    }
}