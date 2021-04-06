<?php
require_once 'RESTConstants.php';
require_once 'controllers/APIController.php';

header('Content-Type: application/json');

// Parse request parameters
$queries = array();
//parse_str($_SERVER['QUERY_STRING'], $queries);

//$uri = explode( ',', $queries['request']);
//unset($queries['request']);

$path = $_SERVER['PHP_SELF'];
$path = ltrim($path, "/");
$pathArray = explode( '/', $path);


$requestMethod = $_SERVER['REQUEST_METHOD'];

$content = file_get_contents('php://input');
if (strlen($content) > 0) {
    $payload = json_decode($content, true);
} else {
    $payload = array();
}

$token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : '';

$controller = new APIController();

// Check that the request is valid
if (!$controller->isValidEndpoint($pathArray)) {
    // Endpoint not recognised
    http_response_code(RESTConstants::HTTP_NOT_FOUND);
    return;
}
if (!$controller->isValidMethod($pathArray, $requestMethod)) {
    // Method not supported
    http_response_code(RESTConstants::HTTP_METHOD_NOT_ALLOWED);
    return;
}
if (!$controller->isValidPayload($pathArray, $requestMethod, $payload)) {
    // Payload is incorrectly formatted
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    return;
}

try {
    $res = $controller->handleRequest($pathArray, $requestMethod, $queries, $payload);
    if ($requestMethod == RESTConstants::METHOD_GET && count($res) == 0) {
        http_response_code(RESTConstants::HTTP_NOT_FOUND);
    } else {
        http_response_code(RESTConstants::HTTP_OK);
        print(json_encode($res));
    }
} catch (Exception $e) {
    http_response_code(RESTConstants::HTTP_INTERNAL_SERVER_ERROR);
    return;
}
