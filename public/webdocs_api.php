<?php

require_once 'abstract_api.php';

class WebDocsAPI extends API
{
    public function __construct($request, $origin) {
        parent::__construct($request);
    }

    protected $VIEW_ID_SALT = "6jMcPqfx";
    protected $EDIT_ID_SALT = "NkJovCdr";
    protected function document() {
        try {
            $conn = new PDO('mysql:host=localhost;dbname=webdocs', "root", "");
            switch($this->verb) {
                case "v":
                    if($this->method == "GET") {
                        $stmt = $conn->prepare("SELECT document_id FROM document_links WHERE view=?");
                        $stmt->execute(array($this->args[0]));

                        $docid = $stmt->fetch(PDO::FETCH_ASSOC)["document_id"];

                        $stmt = $conn->prepare("SELECT title, text_content FROM document_content WHERE id=?");
                        $stmt->execute(array($docid));

                        $result = $stmt->fetch(PDO::FETCH_ASSOC);

                        return array("status" => "success", "title" => $result["title"], "content" => $result["text_content"]);
                    }

                    return array("status" => "fail", "reason" => "Only GET is allowed");
                    break;
                case "e":
                    break;
                case "":
                    if($this->method == "POST") {
                        if(empty($this->args)) {
                            $fileContent = json_decode($this->file, true);
                            if(!array_key_exists("title", $fileContent) || !array_key_exists("content", $fileContent)) {
                                return array("status" => "fail", "reason" => "Body not complete");
                            }

                            $stmt = $conn->prepare("INSERT INTO document_content(title, text_content) VALUES(?, ?)");
                            $stmt->execute(array($fileContent["title"], $fileContent["content"]));
                            $id = $conn->lastInsertId();

                            $view_id = $id . $this->VIEW_ID_SALT;
                            $edit_id = $id . $this->EDIT_ID_SALT;

                            $stmt = $conn->prepare("INSERT INTO document_links(document_id, view, edit) VALUES(?, ?, ?)");
                            $stmt->execute(array($id, $view_id, $edit_id));

                            return array("status" => "success", "document_id" => $id, "edit_id" => $edit_id, "view_id" => $view_id);
                        }
                    } else {
                        return array("status" => "fail", "reason" => "Only POST is allowed");
                    }
                    break;
            }
        } catch(PDOException $e) {
            return array("status" => "fail", "reason" => "Database operation failed");
        }
        return array("status" => "fail", "reason" => "Not allowed operation");
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