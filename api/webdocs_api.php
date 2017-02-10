<?php

require_once 'abstract_api.php';
require_once '../config.php';

class WebDocsAPI extends API
{
    public function __construct($request, $origin) {
        parent::__construct($request);
    }

    protected $VIEW_ID_SALT = "6jMcPqfx";
    protected $EDIT_ID_SALT = "NkJovCdr";
    protected function document() {
        global $DB_HOST;
        global $DB_NAME;
        global $DB_USER;
        global $DB_PASSWORD;
        try {
            $conn = new PDO('mysql:host=' . $DB_HOST . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
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
                case "e":
                    if($this->method == "GET") {
                        $stmt = $conn->prepare("SELECT document_id, view FROM document_links WHERE edit=?");
                        $stmt->execute(array($this->args[0]));

                        $linksResults = $stmt->fetch(PDO::FETCH_ASSOC);

                        $stmt = $conn->prepare("SELECT title, text_content FROM document_content WHERE id=?");
                        $stmt->execute(array($linksResults["document_id"]));

                        $result = $stmt->fetch(PDO::FETCH_ASSOC);

                        return array("status" => "success", "view_id" => $linksResults["view"], "title" => $result["title"], "content" => $result["text_content"]);
                    }
                    return array("status" => "fail", "reason" => "Only GET is allowed");
                case "update":
                    if($this->method == "POST") {
                        $fileContent = json_decode($this->file, true);
                        $stmt = $conn->prepare("SELECT document_id FROM document_links WHERE edit=?");
                        $stmt->execute(array($this->args[0]));

                        $docid = $stmt->fetch(PDO::FETCH_ASSOC)["document_id"];

                        $stmt = $conn->prepare("UPDATE document_content SET text_content=? WHERE id=?");
                        $stmt->execute(array($fileContent["content"], $docid));
                        return array("status" => "success");
                    }
                    return array("status" => "fail", "reason" => "Only POST is allowed");
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
 }

?>
