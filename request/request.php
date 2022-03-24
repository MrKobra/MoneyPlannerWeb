<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/inc/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/request/OperationRequest.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/request/CategoryRequest.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/request/TypeRequest.php";

$data = get_post_request();
$response = array("error" => 1);

if(array_key_exists("entity", $data)) {
    $request = null;
    $action = (array_key_exists("action", $data)) ? $data["action"] : "";
    switch ($data["entity"]) {
        case "operation":
            $request = new OperationRequest($action);
            $response = $request->perform_request($data);
            break;
        case "category":
            $request = new CategoryRequest($action);
            $response = $request->perform_request($data);
            break;
        case "type":
            $request = new TypeRequest($action);
            $response = $request->perform_request($data);
            break;
    }
}

echo json_encode($response);
