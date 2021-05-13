<?php

class MethodValidation {
    /** isValidMethod checks if the request method is valid
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @return bool returns true if the request method is valid, and false otherwise.
     */
    public function isValidMethod(array $uri, string $requestMethod): bool
    {
        switch ($uri[0]) {
            case RESTConstants::ENDPOINT_CUSTOMER:
                return $this->validCustomerMethod($uri, $requestMethod);
            case RESTConstants::ENDPOINT_CUSTOMERREP:
                return $this->validCustomerRepMethod($uri, $requestMethod);
            case RESTConstants::ENDPOINT_PLANNER:
                return $this->validPlannerMethod($uri, $requestMethod);
            case RESTConstants::ENDPOINT_PUBLIC:
                return $this->validPublicMethod($uri, $requestMethod);
            case RESTConstants::ENDPOINT_SHIPPER:
                return $this->validShipperMethod($uri, $requestMethod);
            case RESTConstants::ENDPOINT_STOREKEEPER:
                return $this->validStoreKeeperMethod($uri, $requestMethod);
        }
        return false;
    }

    /** validCustomerMethod checks if the method used when accessing the customer endpoint is correct
     * @param array $uri all the parts of the endpoint url in an array
     * @param string $requestMethod the method used in the request
     * @return bool returns true if the method is correct, false otherwise
     */
    public function validCustomerMethod(array $uri, string $requestMethod): bool
    {
        switch ($uri[1]) {
            case RESTConstants::ENDPOINT_PRODUCTION_PLAN:
                return match ($requestMethod) {
                    RESTConstants::METHOD_GET => true,
                    default => false,
                };
            case RESTConstants::ENDPOINT_ORDERS:
                if(empty($uri[2])) {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_POST, RESTConstants::METHOD_GET => true,
                        default => false,
                    };
                } else {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_DELETE, RESTConstants::METHOD_POST, RESTConstants::METHOD_PUT, RESTConstants::METHOD_GET => true,
                        default => false,
                    };
                }
            default:
                return false;
        }
    }
    /** validCustomerRepMethod checks if the method used when accessing the customer rep endpoint is correct
     * @param array $uri all the parts of the endpoint url in an array
     * @param string $requestMethod the method used in the request
     * @return bool returns true if the method is correct, false otherwise
     */
    public function validCustomerRepMethod(array $uri, string $requestMethod): bool
    {
        if($uri[1] == RESTConstants::ENDPOINT_ORDERS) {
            if(empty($uri[2])) {
                return match ($requestMethod) {
                    RESTConstants::METHOD_GET => true,
                    default => false,
                };
            } else {
                return match ($requestMethod) {
                    RESTConstants::METHOD_PATCH => true,
                    default => false,
                };
            }
        }
        return false;
    }
    /** validPlannerMethod checks if the method used when accessing the planner endpoint is correct
     * @param array $uri all the parts of the endpoint url in an array
     * @param string $requestMethod the method used in the request.
     * @return bool returns true if the method is correct, false otherwise
     */
    public function validPlannerMethod(array $uri, string $requestMethod): bool {
        return match ($uri[1]) {
            RESTConstants::ENDPOINT_PRODUCTION_PLAN => match ($requestMethod) {
                RESTConstants::METHOD_POST => true,
                default => false,
            },
            default => false,
        };
    }

    /** validPublicMethod checks if the method used when accessing the public endpoint is correct
     * @param array $uri all the parts of the endpoint url in an array
     * @param string $requestMethod the method used in the request.
     * @return bool returns true if the method is correct, false otherwise
     */
    public function validPublicMethod(array $uri, string $requestMethod): bool {
        return match ($uri[1]) {
            RESTConstants::ENDPOINT_SKITYPES => match ($requestMethod) {
                RESTConstants::METHOD_GET => true,
                default => false,
            },
            default => false,
        };
    }

    /** validShipperMethod checks if the method used when accessing the shipper endpoint is correct
     * @param array $uri all the parts of the endpoint url in an array
     * @param string $requestMethod the method used in the request.
     * @return bool returns true if the method is correct, false otherwise
     */
    public function validShipperMethod(array $uri, string $requestMethod): bool {
        return match ($uri[1]) {
            RESTConstants::ENDPOINT_ORDERS => match ($requestMethod) {
                RESTConstants::METHOD_PATCH, RESTConstants::METHOD_GET => true,
                default => false,
            },
            RESTConstants::ENDPOINT_SHIPMENT => match($requestMethod) {
                RESTConstants::METHOD_GET => true,
                default => false,
            },
        };
    }

    /** validStorekeeperMethod checks if the method used when accessing the storekeeper endpoint is correct
     * @param array $uri all the parts of the endpoint url in an array
     * @param string $requestMethod the method used in the request.
     * @return bool returns true if the method is correct, false otherwise
     */
    public function validStorekeeperMethod(array $uri, string $requestMethod): bool {

        switch($uri[1]) {
            case RESTConstants::ENDPOINT_SKIS:
                if(empty($uri[2])) {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_GET, RESTConstants::METHOD_POST => true,
                        default => false,
                    };
                } else {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_GET => true,
                        default => false,
                    };
                }
            case RESTConstants::ENDPOINT_ORDERS:
                if(empty($uri[2])) {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_GET, RESTConstants::METHOD_PATCH => true,
                        default => false,
                    };
                } else {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_GET => true,
                        default => false,
                    };
                }
            default:
                return false;
        }
    }

}