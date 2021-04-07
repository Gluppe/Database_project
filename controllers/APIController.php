<?php
require_once 'RESTConstants.php';
require_once 'models/OrdersModel.php';

class APIController
{
    public function isValidEndpoint(array $uri): bool {
        if (strtolower($uri[0]) == RESTConstants::ENDPOINT_ORDERS) {
            return true;
        }
        return false;
    }

    public function isValidMethod(array $uri, string $requestMethod): bool {
        switch ($uri[0]) {
            case RESTConstants::ENDPOINT_ORDERS:
                // The only method implemented is for getting individual car resources
                return $requestMethod == RESTConstants::METHOD_GET;
        }
        return false;
    }

    public function isValidPayload(array $uri, string $requestMethod, array $payload): bool {
        // No payloads to test for GET methods
        if ($requestMethod == RESTConstants::METHOD_GET)  {
            return true;
        }
        return false;
    }

    /** handleRequest
     * @param array $uri
     * @param string $requestMethod
     * @param array $queries
     * @param array $payload
     * @return array
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

    protected function handleOrdersRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        if ($requestMethod == RESTConstants::METHOD_GET) {
            $model = new OrdersModel();
            return $model->getOrders();
        }
        return array();
    }

    protected function handleOrderRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new OrdersModel();
                return $model->getOrder();
            case RESTConstants::METHOD_POST:
                $model = new OrdersModel();
                if($model->updateOrder()) {
                    print("Order Updated");
                    return array();
                } else {
                    print("Order update failed");
                    return array();
                }

            case RESTConstants::METHOD_PUT:
                $model = new OrdersModel();
                return $model->addOrder();
        }


        return array();
    }

    protected function handleSkisRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        return array();
    }

    protected function handleSkiRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        return array();
    }

    protected function handleShipmentRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        return array();
    }

    protected function handleProductionPlanRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        return array();
    }
}
