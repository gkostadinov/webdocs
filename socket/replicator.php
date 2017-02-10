#!/usr/bin/env php
<?php

require_once('./websockets.php');

class WebDocsServer extends WebSocketServer {
    protected $maxBufferSize = 1048576; //1MB
    protected $webdocsusers = array();
    protected $rooms = array();

    protected function process ($user, $message) {
        $decodedMsg = json_decode($message, true);
        if(!array_key_exists("document_id" , $decodedMsg)) {
            return;
        }
        $docid = $decodedMsg["document_id"];

        if($this->webdocsusers[$user->id] == "-1") {
            if(!array_key_exists($docid, $this->rooms)) {
                $this->rooms[$docid] = array();
            }
            array_push($this->rooms[$docid], $user);
            $this->webdocsusers[$user->id] = $docid;
        }

        if(!array_key_exists("delta", $decodedMsg)) {
            return;
        }

        $room = $this->rooms[$this->webdocsusers[$user->id]];

        $newMsg = array("document_id" => $docid, "delta" => $decodedMsg["delta"]);

        foreach( $room as $u) {
            if ($u != $user) {
                $this->send($u, json_encode($newMsg));
            }
        }

        if(array_key_exists("content", $decodedMsg)) {
            $content = json_encode(array("content" => $decodedMsg["content"]));
            $curl = curl_init('http://localhost/webdocs/api/document/update/' . $docid);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($content)
            ));

            curl_exec($curl);
        }
    }

    protected function connected ($user) {
        $this->webdocsusers[$user->id] = "-1";
    }

    protected function closed ($user) {
    $roomId = $this->webdocsusers[$user->id];
    $found = array_search($user, $this->rooms[$roomId]);
    unset($this->rooms[$roomId][$found]);
    if(empty($this->rooms[$roomId])) {
        unset($this->rooms[$roomId]);
    }
    unset($this->webdocsusers[$user->id]);
    }
}

$echo = new WebDocsServer("0.0.0.0","8000");

try {
  $echo->run();
}
catch (Exception $e) {
  $echo->stdout($e->getMessage());
}
