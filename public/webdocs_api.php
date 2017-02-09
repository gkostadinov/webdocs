<?php

require_once 'abstract_api.php';

class WebDocsAPI extends API
{
    public function __construct($request, $origin) {
        parent::__construct($request);

        // Add authentication, model initialization, etc here
    }

    protected function document() {
        switch($this->verb) {
            case "v":
                break;
            case "e":
                break;
            case "":
                if($this->method == "POST") {
                    if(empty($this->args)) {
                        return array("status"=> "success", "")
                    }
                } else {
                    return array("status" => "fail", "reason" => "Only POST is allowed");
                }
                break;
        }
    }

    /*
     * Example of an Endpoint
     */
     protected function example() {
        switch ($this->verb) {
            case "get":
                if ($this->method == 'GET') {
                    return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                } 
                else {
                    return "Only accepts GET requests";
                }
                break;
            case "post":
                if ($this->method == 'POST') {
                    return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                } 
                else {
                    return "Only accepts POST requests";
                }
                break;
            case "delete":
                if ($this->method == 'PUT') {
                    return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                } 
                else {
                    return "Only accepts PUT requests";
                }
                break;
            case "put":
                if ($this->method == 'DELETE') {
                    return array("status" => "success", "endpoint" => $this->endpoint, "verb" => $this->verb, "args" => $this->args, "request" => $this->request);
                } 
                else {
                    return "Only accepts DELETE requests";
                }
                break;
            default:
                break;
        }
        
     }
 }

?>