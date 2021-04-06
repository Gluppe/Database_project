<?php
require_once 'RESTConstants.php';
require_once 'db/CarModel.php';

class APIController
{
    public function isValidEndpoint(array $uri): bool
    {
        if ($uri[0] == RESTConstants::ENDPOINT_CARS) {
            if (count($uri) == 1) {
                // A request for the collection of used cars
                return true;
            } elseif (count($uri) == 2) {
                // The car id must be a number
                return ctype_digit($uri[1]);
            }
        }
        return false;
    }

    public function isValidMethod(array $uri, string $requestMethod): bool {
        switch ($uri[0]) {
            case RESTConstants::ENDPOINT_CARS:
                // The only method implemented is for getting individual car resources
                return count($uri) == 2 && $requestMethod == RESTConstants::METHOD_GET;
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
            case RESTConstants::ENDPOINT_CARS:
                return $this->handleCarRequest($uri, $requestMethod, $queries, $payload);
                break;
        }
        return array();
    }

    protected function handleCarRequest(array $uri, string $requestMethod, array $queries, array $payload): array
    {
        if (count($uri) == 1) {
            $model = new CarModel();
            return $model->getCollection();
        } elseif (count($uri) == 2) {
            $model = new CarModel();
            return $model->getResource(intval($uri[1]));
        }
        return array();
    }
}
