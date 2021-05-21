<?php
require_once 'RESTConstants.php';
require_once 'controllers/APIController.php';
require_once 'controllers/EndpointValidation.php';
require_once 'controllers/MethodValidation.php';
require_once 'controllers/PayloadValidation.php';

$response = array("status code" => "", "message" => "");
// Parse request parameters
$queries = array();
if (!empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $queries);
}

// Sets the response header to application/json
header('Content-Type: application/json');

// Stores the uri of the request
$uri = $_SERVER['PHP_SELF'];
$uri = ltrim($uri, "/");
$uri = explode( '/', $uri);

// Stores the request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Stores the request body
$content = file_get_contents('php://input');
if (strlen($content) > 0) {
    $payload = json_decode($content, true);
} else {
    $payload = array();
}

// Stores the authentication token
$token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : '';

$controller = new APIController();
$endpointValidation = new EndpointValidation();

// Check that the endpoint is valid
if (!$endpointValidation->isValidEndpoint($uri)) {
    // Endpoint not recognised
    error_log("Not valid endpoint");
    $response["status code"] = RESTConstants::HTTP_NOT_FOUND;
    $response["message"] = "Not a valid endpoint";
    print(json_encode($response));
    http_response_code(RESTConstants::HTTP_NOT_FOUND);
    return;
}

// Check that the request method is valid
$methodValidation = new MethodValidation();
if (!$methodValidation->isValidMethod($uri, $requestMethod)) {
    // Method not supported
    error_log("Not valid method");
    $response["status code"] = RESTConstants::HTTP_METHOD_NOT_ALLOWED;
    $response["message"] = "Not a valid method";
    print(json_encode($response));
    http_response_code(RESTConstants::HTTP_METHOD_NOT_ALLOWED);
    return;
}

if(!is_array($payload)) {
    error_log("Payload is not array");
    $payload = array();
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
}

// Check that the payload is valid
$payloadValidation = new PayloadValidation();
if (!$payloadValidation->isValidPayload($uri, $requestMethod, $payload)) {
    // Payload is incorrectly formatted
    error_log("Not valid payload");
    $response["status code"] = RESTConstants::HTTP_BAD_REQUEST;
    $response["message"] = "Not a valid payload";
    print(json_encode($response));
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    return;
}

// Checks if the authorisation token is valid
try{
    $controller->authorise($token, $uri[0]);
} catch (Exception $e) {
    $response["status code"] = RESTConstants::HTTP_BAD_REQUEST;
    $response["message"] = "A valid token is needed";
    print(json_encode($response));
    http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    return;
}

// Handles the request
try {
    $res = $controller->handleRequest($uri, $requestMethod, $queries, $payload);
    if (count($res) == 0) {
        $response["status code"] = RESTConstants::HTTP_NOT_FOUND;
        $response["message"] = "No response";
        print(json_encode($response));
        http_response_code(RESTConstants::HTTP_NOT_FOUND);
    } else if(!$res[0]) {
        $response["status code"] = RESTConstants::HTTP_BAD_REQUEST;
        $response["message"] = "Bad Request";
        print(json_encode($response));
        http_response_code(RESTConstants::HTTP_BAD_REQUEST);
    } else {
        switch ($requestMethod) {
            case RESTConstants::METHOD_GET:
                http_response_code(RESTConstants::HTTP_OK);
                print(json_encode($res));
                break;
            case RESTConstants::METHOD_PATCH:
                http_response_code(RESTConstants::HTTP_OK);
                $response["status code"] = RESTConstants::HTTP_OK;
                $response["message"] = "Successfully updated";
                print(json_encode($response));
                break;
            case RESTConstants::METHOD_DELETE:
                http_response_code(RESTConstants::HTTP_ACCEPTED);
                $response["status code"] = RESTConstants::HTTP_ACCEPTED;
                $response["message"] = "Successfully cancelled";
                print(json_encode($response));
                break;
            case RESTConstants::METHOD_POST:
                http_response_code(RESTConstants::HTTP_CREATED);
                $response["status code"] = RESTConstants::HTTP_CREATED;
                $response["message"] = "Successfully created";
                print(json_encode($response));
                break;
        }
    }
} catch (Exception $e) {
    http_response_code(RESTConstants::HTTP_INTERNAL_SERVER_ERROR);
    $response["status code"] = RESTConstants::HTTP_INTERNAL_SERVER_ERROR;
    $response["message"] = "Internal Server error has occured";
    print(json_encode($response));
    return;
}
