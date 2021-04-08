<?php
require_once 'RESTConstants.php';
require_once 'models/OrdersModel.php';
require_once 'models/SkisModel.php';

class APIController
{
    /** isValidEndpoint checks if the endpoint is valid
     * @param array $uri this contains the path in an array
     * @return bool returns true if the endpoint is valid, and false otherwise
     */
    public function isValidEndpoint(array $uri): bool {
        switch(strtolower($uri[0])) {
            case RESTConstants::ENDPOINT_ORDER:
            case RESTConstants::ENDPOINT_ORDERS:
            case RESTConstants:: ENDPOINT_SKIS:
            case RESTConstants:: ENDPOINT_SKI:
            case RESTConstants:: ENDPOINT_SHIPMENT:
            case RESTConstants:: ENDPOINT_PRODUCTIONPLAN:
                return true;
            default:
                return false;
        }
    }

    /** isValidMethod checks if the request method is valid
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @return bool returns true if the request method is valid, and false otherwise.
     */
    public function isValidMethod(array $uri, string $requestMethod): bool {
        switch ($uri[0]) {
            case RESTConstants::ENDPOINT_ORDERS:
                // The only method implemented is for getting individual car resources
                return $requestMethod == RESTConstants::METHOD_GET;
            case RESTConstants::ENDPOINT_ORDER:
                return match ($requestMethod) {
                    RESTConstants::METHOD_POST, RESTConstants::METHOD_PUT, RESTConstants::METHOD_GET, RESTConstants::METHOD_DELETE => true,
                    default => false,
                };
            case RESTConstants::ENDPOINT_SKIS:
                return $requestMethod == RESTConstants::METHOD_GET;
            case RESTConstants::ENDPOINT_SHIPMENT:
            case RESTConstants::ENDPOINT_SKI:
                return match ($requestMethod) {
                    RESTConstants::METHOD_POST, RESTConstants::METHOD_PUT, RESTConstants::METHOD_GET => true,
                    default => false,
                };
            case RESTConstants::ENDPOINT_PRODUCTIONPLAN:
                return match ($requestMethod) {
                    RESTConstants::METHOD_PUT, RESTConstants::METHOD_GET => true,
                    default => false,
                };
        }
        return false;
    }

    /** isValidPayload checks if the payload is valid
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $payload contains the payload
     * @return bool returns true if the payload is valid, and false otherwise.
     */
    public function isValidPayload(array $uri, string $requestMethod, array $payload): bool {
        // No payloads to test for GET methods
        if ($requestMethod == RESTConstants::METHOD_GET)  {
            return true;
        }
        return false;
    }

    /** handleRequest checks what endpoint is used, and uses the correct function for each endpoint
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    public function handleRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        $endpointUri = $uri[0];
        switch ($endpointUri) {
            case RESTConstants::ENDPOINT_ORDERS:
                return $this->handleOrdersRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_ORDER:
                return $this->handleOrderRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_SKIS:
                return $this->handleSkisRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_SKI:
                return $this->handleSkiRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_SHIPMENT:
                return $this->handleShipmentRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_PRODUCTIONPLAN:
                return $this->handleProductionPlanRequest($uri, $requestMethod, $queries, $payload);
        }
        return array();
    }

    /** handleOrdersRequest handles what happens when the Orders endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleOrdersRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        if ($requestMethod == RESTConstants::METHOD_GET) {
            $model = new OrdersModel();
            return $model->getOrders();
        }
        return array();
    }
    /** handleOrderRequest handles what happens when the Order endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleOrderRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new OrdersModel();
                return $model->getOrder();
            case RESTConstants::METHOD_POST:
                $model = new OrdersModel();
                $updated = $model->updateOrder($payload);
                $res = array();
                $res[] = $updated;
                return $res;
            case RESTConstants::METHOD_PUT:
                $model = new OrdersModel();
                $res = array();
                $res[] = $model->addOrder($payload);
                return $res;
            case RESTConstants::METHOD_DELETE:
                $model = new OrdersModel();
                return $model->deleteOrder();
        }

        return array();
    }

    /** handleSkisRequest handles what happens when the Skis endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleSkisRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        if ($requestMethod == RESTConstants::METHOD_GET) {
            $model = new SkisModel();
            return $model->getSkis();
        }
        return array();
    }

    /** handleSkiRequest handles what happens when the Ski endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleSkiRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new SkisModel();
                return $model->getSki();
            case RESTConstants::METHOD_POST:
                $model = new SkisModel();
                $updated = $model->updateSki($payload);
                $res = array();
                $res[] = $updated;
                return $res;
            case RESTConstants::METHOD_PUT:
                $model = new SkisModel();
                return $model->addSki($payload);
        }

        return array();
    }

    /** handleShipmentRequest handles what happens when the Shipment endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleShipmentRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        return array();
    }

    /** handleProductionPlanRequest handles what happens when the Production-Plan endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleProductionPlanRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        return array();
    }
}
