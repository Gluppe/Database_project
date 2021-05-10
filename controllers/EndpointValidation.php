<?php

class EndpointValidation {
    /** isValidEndpoint checks if the endpoint is valid
     * @param array $uri this contains the path in an array
     * @return bool returns true if the endpoint is valid, and false otherwise
     */
    public function isValidEndpoint(array $uri): bool
    {
        switch (strtolower($uri[0])) {
            case RESTConstants::ENDPOINT_CUSTOMER:
                return $this->validCustomerEndpoint($uri[1]);
            case RESTConstants::ENDPOINT_CUSTOMERREP:
                return $this->validCustomerRepEndpoint($uri[1]);
            case RESTConstants::ENDPOINT_PLANNER:
                return $this->validPlannerEndpoint($uri[1]);
            case RESTConstants::ENDPOINT_PUBLIC:
                return $this->validPublicEndpoint($uri[1]);
            case RESTConstants::ENDPOINT_SHIPPER:
                return $this->validShipperEndpoint($uri[1]);
            case RESTConstants::ENDPOINT_STOREKEEPER:
                return $this->validStorekeeperEndpoint($uri[1]);
            default:
                return false;
        }
    }

    public function validCustomerEndpoint(string $endpoint): bool
    {
        return match ($endpoint) {
            RESTConstants::ENDPOINT_ORDERS, RESTConstants::ENDPOINT_PRODUCTION_PLAN => true,
            default => false,
        };
    }

    public function validCustomerRepEndpoint(string $endpoint): bool
    {
        return match ($endpoint) {
            RESTConstants::ENDPOINT_ORDERS => true,
            default => false,
        };
    }

    public function validPlannerEndpoint(string $endpoint): bool
    {
        return match ($endpoint) {
            RESTConstants::ENDPOINT_PRODUCTION_PLAN => true,
            default => false,
        };
    }

    public function validPublicEndpoint(string $endpoint): bool
    {
        return match ($endpoint) {
            RESTConstants::ENDPOINT_SKITYPES => true,
            default => false,
        };
    }

    public function validShipperEndpoint(string $endpoint): bool
    {
        return match ($endpoint) {
            RESTConstants::ENDPOINT_ORDERS, RESTConstants::ENDPOINT_SHIPMENT => true,
            default => false,
        };
    }

    public function validStorekeeperEndpoint(string $endpoint): bool
    {
        return match ($endpoint) {
            RESTConstants::ENDPOINT_ORDERS, RESTConstants::ENDPOINT_SKIS => true,
            default => false,
        };
    }
}