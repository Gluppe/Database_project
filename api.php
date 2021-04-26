<?php
require_once 'RESTConstants.php';
require_once 'controllers/APIController.php';

header('Content-Type: application/json');

// Parse request parameters
$queries = array();
if (!empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $queries);
}



$uri = $_SERVER['PHP_SELF'];
$uri = ltrim($uri, "/");
$uri = explode( '/', $uri);


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
if (!$controller->isValidEndpoint($uri)) {
    // Endpoint not recognised
    error_log("Not valid endpoint");
    http_response_code(RESTConstants::HTTP_NOT_FOUND);
    return;
}
if (!$controller->isValidMethod($uri, $requestMethod)) {
    // Method not supported
    error_log("Not valid method");
    http_response_code(RESTConstants::HTTP_METHOD_NOT_ALLOWED);
    return;
}
if (!$controller->isValidPayload($uri, $requestMethod, $payload)) {
    // Payload is incorrectly formatted
    error_log("Not valid payload");
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    return;
}

try {
    $res = $controller->handleRequest($uri, $requestMethod, $queries, $payload);

    if (count($res) == 0) {
        http_response_code(RESTConstants::HTTP_NOT_FOUND);
    }
    switch($requestMethod) {
        case RESTConstants::METHOD_GET:
            http_response_code(RESTConstants::HTTP_OK);
            print(json_encode($res));
            break;
        case RESTConstants::METHOD_POST:
            http_response_code(RESTConstants::HTTP_OK);
            print("Successfully Updated");
            break;
        case RESTConstants::METHOD_PUT:
            http_response_code(RESTConstants::HTTP_OK);
            print("Successfully Added");
            break;
        case RESTConstants::METHOD_DELETE:
            http_response_code(RESTConstants::HTTP_OK);
            print("Successfully Deleted");
            break;

    }

} catch (Exception $e) {
    http_response_code(RESTConstants::HTTP_INTERNAL_SERVER_ERROR);
    return;
}
