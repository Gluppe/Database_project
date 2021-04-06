<?php
require_once 'RESTConstants.php';
require_once 'models/OrdersModel.php';

class APIController
{
    public function isValidEndpoint(array $uri): bool
    {
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

    public function isValidPayload(array $uri, string $requestMethod, array $payload): bool
    {
        // No payloads to test for GET methods
        if ($requestMethod == RESTConstants::METHOD_GET)  {
            return true;
        }
        return false;
    }

    public function handleRequest(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        $endpointUri = $uri[0];
        switch ($endpointUri) {
            case RESTConstants::ENDPOINT_ORDERS:
                return $this->handleOrdersRequest($uri, $requestMethod, $queries, $payload);
                break;
        }
        return array();
    }

    protected function handleOrdersRequest(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        if ($requestMethod == RESTConstants::METHOD_GET) {
            $model = new OrdersModel();
            return $model->getOrders();
        }
        return array();
    }
}
