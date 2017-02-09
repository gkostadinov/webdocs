#!/usr/bin/env php
<?php

require_once('./websockets.php');

class WebDocsServer extends WebSocketServer {
  //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.
  protected $webdocsusers = array();
  protected $rooms = array();
  
  protected function process ($user, $message) {
      if($this->webdocsusers[$user->id] == "-1") {
          $decodedMsg = json_decode($message, true);
          if(array_key_exists("document_id" , $decodedMsg)) {
              $var = $decodedMsg["document_id"];
              if(!array_key_exists($var, $this->rooms)) {
                  $this->rooms[$var] = array();
              }
              array_push($this->rooms[$var], $user);
              $this->webdocsusers[$user->id] = $var;
              return;
          }
      }

      $room = $this->rooms[$this->webdocsusers[$user->id]];

      foreach( $room as $u) {
          if ($u != $user) {
              $this->send($u, $message);
          }
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
