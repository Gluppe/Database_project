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
    public function isValidEndpoint(array $uri): bool
    {
        switch (strtolower($uri[0])) {
            case RESTConstants::ENDPOINT_CUSTOMER:
                if ($this->validCustomerEndpoint($uri[1])) return true;
                return false;
            case RESTConstants::ENDPOINT_CUSTOMERREP:
                if ($this->validCustomerRepEndpoint($uri[1])) return true;
                return false;
            case RESTConstants::ENDPOINT_PLANNER:
                if ($this->validPlannerEndpoint($uri[1])) return true;
                return false;
            case RESTConstants::ENDPOINT_PUBLIC:
                if ($this->validPublicEndpoint($uri[1])) return true;
                return false;
            case RESTConstants::ENDPOINT_SHIPPER:
                if ($this->validShipperEndpoint($uri[1])) return true;
                return false;
            case RESTConstants::ENDPOINT_STOREKEEPER:
                if ($this->validStorekeeperEndpoint($uri[1])) return true;
                return false;
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
            RESTConstants::ENDPOINT_ORDERS, RESTConstants::ENDPOINT_SKI => true,
            default => false,
        };
    }

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
                    RESTConstants::METHOD_POST => true,
                    default => false,
                };
            }
        }
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
            RESTConstants::ENDPOINT_ORDERS, RESTConstants::ENDPOINT_SHIPMENT => match ($requestMethod) {
                RESTConstants::METHOD_POST, RESTConstants::METHOD_GET => true,
                default => false,
            },
            default => false,
        };
    }

    /** validStorekeeperMethod checks if the method used when accessing the storekeeper endpoint is correct
     * @param array $uri all the parts of the endpoint url in an array
     * @param string $requestMethod the method used in the request.
     * @return bool returns true if the method is correct, false otherwise
     */
    public function validStorekeeperMethod(array $uri, string $requestMethod): bool {

        switch($uri[1]) {
            case RESTConstants::ENDPOINT_SKI:

            case RESTConstants::ENDPOINT_ORDERS:
                if(empty($uri[2])) {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_GET => true,
                        default => false,
                    };
                } else {
                    return match ($requestMethod) {
                        RESTConstants::METHOD_GET, RESTConstants::METHOD_POST => true,
                        default => false,
                    };
                }
            default:
                return false;
        }
    }


    /** isValidPayload checks if the payload is valid
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $payload contains the payload
     * @return bool returns true if the payload is valid, and false otherwise.
     */
    public function isValidPayload(array $uri, string $requestMethod, array $payload): bool {
        //TODO: actually implement this.
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                return true;
            case RESTConstants::METHOD_PUT:
                return true;
            case RESTConstants::METHOD_POST:
                return true;
            case RESTConstants::METHOD_DELETE:
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
        $endpointUri = $uri[1];
        switch ($endpointUri) {
            case RESTConstants::ENDPOINT_ORDERS:
                if(empty($uri[2])) {
                    if($uri[0] == RESTConstants::ENDPOINT_CUSTOMER && empty($queries['customer_id'])) {
                        print("A customer_id query is needed to see your orders");
                        return array();
                    }
                    return $this->handleOrdersRequest($uri, $requestMethod, $queries, $payload);
                } else {
                    return $this->handleOrderRequest($uri, $requestMethod, $queries, $payload);
                }
            case RESTConstants::ENDPOINT_SKI:
                return $this->handleSkiRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_SHIPMENT:
                return $this->handleShipmentRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_PRODUCTION_PLAN:
                return $this->handleProductionPlanRequest($uri, $requestMethod, $queries, $payload);
            case RESTConstants::ENDPOINT_SKITYPES:
                return $this->handleSkiTypeRequest($uri, $requestMethod, $queries, $payload);
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
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new OrdersModel();
                return $model->getOrders();
            case RESTConstants::METHOD_POST:
                $model = new OrdersModel();
                $model->addOrder($payload);
        }
        return array();
    }

    /** handleOrderRequest handles what happens when the Order endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     * @throws Throwable
     */
    protected function handleOrderRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new OrdersModel();
                return $model->getOrder($queries);
            case RESTConstants::METHOD_POST:
                $model = new OrdersModel();
                $updated = $model->updateOrder($payload);
                $res = array();
                $res[] = $updated;
                return $res;
            case RESTConstants::METHOD_DELETE:
                $model = new OrdersModel();
                $model->deleteOrder($queries[0]);
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
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new ShipmentModel();
                return $model->getShipment($payload);
            case RESTConstants::METHOD_POST:
                $model = new ShipmentModel();
                return $model->updateShipment($payload);
        }
    }

    /** handleProductionPlanRequest handles what happens when the Production-Plan endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleProductionPlanRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new ProductionPlanModel();
                return $model->getProductionPlan($payload);
            case RESTConstants::METHOD_PUT:
                $model = new ProductionPlanModel();
                return $model->addProductionPlanModel($payload);
        }
    }

    /** handleSkiTypeRequest handles what happens when the Ski-Type endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     */
    protected function handleSkiTypeRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        if ($requestMethod == RESTConstants::METHOD_GET) {
            if(!empty($queries['model'])) {
                $model = new SkisModel();
                $query = explode( ',', $queries['model']);
                return $model->getSkiTypesByModel($query);
            } else if (!empty($queries['grip'])) {
                $model = new SkisModel();
                $query = explode( ',', $queries['grip']);
                return $model->getSkiTypesByGripSystem($query);
            }
        }
    }
}
