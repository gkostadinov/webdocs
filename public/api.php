<?php

session_start();

require_once "../api/webdocs_api.php";

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new WebDocsAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
}
catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}

?>
