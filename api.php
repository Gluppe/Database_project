<?php
require_once 'RESTConstants.php';
require_once 'controllers/APIController.php';
require_once 'controllers/EndpointValidation.php';
require_once 'controllers/MethodValidation.php';
require_once 'controllers/PayloadValidation.php';


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
$endpointValidation = new EndpointValidation();

// Check that the request is valid
if (!$endpointValidation->isValidEndpoint($uri)) {
    // Endpoint not recognised
    error_log("Not valid endpoint");
    print("Not valid endpoint");
    http_response_code(RESTConstants::HTTP_NOT_FOUND);
    return;
}
$methodValidation = new MethodValidation();
if (!$methodValidation->isValidMethod($uri, $requestMethod)) {
    // Method not supported
    error_log("Not valid method");
    print("Not a valid request method");
    http_response_code(RESTConstants::HTTP_METHOD_NOT_ALLOWED);
    return;
}
$payloadValidation = new PayloadValidation();
if(!is_array($payload)) {
    error_log("Payload is not array");
    $payload = array();
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
}
if (!$payloadValidation->isValidPayload($uri, $requestMethod, $payload)) {
    // Payload is incorrectly formatted
    error_log("Not valid payload");
    print("Not a valid payload");
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    return;
}
try{
    $controller->authorise($token, $uri[0]);
} catch (Exception $e) {
    print("A valid token is needed");
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    return;
}
try {
    $res = $controller->handleRequest($uri, $requestMethod, $queries, $payload);

    if (count($res) == 0) {
        http_response_code(RESTConstants::HTTP_NOT_FOUND);
    } else if(!$res[0]) {
        print("Bad request");
        http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    } else {
        header('Content-Type: application/json');
        switch ($requestMethod) {
            case RESTConstants::METHOD_GET:
                http_response_code(RESTConstants::HTTP_OK);
                print(json_encode($res));
                break;
            case RESTConstants::METHOD_PUT:
            case RESTConstants::METHOD_DELETE:
            case RESTConstants::METHOD_POST:
                http_response_code(RESTConstants::HTTP_OK);
                break;

        }
    }

} catch (Exception $e) {
    http_response_code(RESTConstants::HTTP_INTERNAL_SERVER_ERROR);
    return;
}
