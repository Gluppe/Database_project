<?php
require_once 'RESTConstants.php';
require_once 'models/OrdersModel.php';
require_once 'models/SkisModel.php';
require_once 'models/ShipmentsModel.php';
require_once 'models/ProductionPlanModel.php';
require_once 'models/AuthorisationModel.php';
require_once 'controllers/APIException.php';

class APIController
{
    /** handleRequest checks what endpoint is used, and uses the correct function for each endpoint
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     * @throws Throwable
     */
    public function handleRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        $endpointUri = $uri[1];
        switch ($endpointUri) {
            case RESTConstants::ENDPOINT_ORDERS:
                if($uri[0] == RESTConstants::ENDPOINT_CUSTOMER && empty($queries['customer_id'])) {
                    print("A customer_id query is needed to see your orders");
                    return array();
                } else if(empty($uri[2])) {
                    return $this->handleOrdersRequest($uri, $requestMethod, $queries, $payload);
                } else {
                    return $this->handleOrderRequest($uri, $requestMethod, $queries, $payload);
                }
            case RESTConstants::ENDPOINT_SKIS:
                return $this->handleSkisRequest($uri, $requestMethod, $queries, $payload);
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
     * @throws Throwable
     */
    protected function handleOrdersRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new OrdersModel();
                return $model->getOrder(array(), $queries);
            case RESTConstants::METHOD_POST:
                $model = new OrdersModel();
                $model->addOrder($payload, $queries);
                return array(true);
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
                return $model->getOrder($uri, $queries);
            case RESTConstants::METHOD_PATCH:
                $model = new OrdersModel();
                $success = $model->updateOrder($uri, $payload);
                if($success) {
                    print("the order was successfully updated\n");
                    if($uri[0] == "shipper" || $uri[0] == "storekeeper") {
                        $model = new TransitionHistoryModel();
                        $model->addTransitionHistory($uri[2], $payload['state']);
                    }
                    return array(true);
                } else {
                    print("Something went wrong, the order was not updated\n");
                    return array(false);
                }
            case RESTConstants::METHOD_DELETE:
                $model = new OrdersModel();
                $success = $model->cancelOrder($uri[2]);
                if($success) {
                    print("the order was successfully deleted\n");
                    return array(true);
                } else {
                    print("Something went wrong, the order was not deleted\n");
                    return array(false);
                }
        }

        return array();
    }


    /** handleSkisRequest handles what happens when the Ski endpoint is used
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $queries contains the queries used
     * @param array $payload contains the payload
     * @return array returns an array of the information gotten from the database
     * @throws Throwable
     */
    protected function handleSkisRequest(array $uri, string $requestMethod, array $queries, array $payload): array {
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new SkisModel();
                if(empty($uri[2])) {
                    return $model->getSkis();
                } else if((int)$uri[2] != 0){
                    return $model->getSki($uri[2]);
                } else {
                    return array();
                }

            case RESTConstants::METHOD_POST:
                $model = new SkisModel();
                $success = $model->addSki($payload);
                if ($success) {
                    print("Ski successfully added\n");
                    return array(true);
                } else {
                    print("Something went wrong, ski not added\n");
                    return array(false);
                }
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
                $model = new ShipmentsModel();
                return $model->getShipment($queries);
            case RESTConstants::METHOD_POST:
                //TODO: updateShipment may not be necessary
                if(!empty($uri[2])) {
                    $model = new ShipmentsModel();
                    return $model->updateShipment($payload);
                } else {
                    print("Shipment number must be specifed");
                }
                return array(true);
        }
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
        switch($requestMethod) {
            case RESTConstants::METHOD_GET:
                $model = new ProductionPlanModel();
                return $model->getProductionPlan($uri[2]);
            case RESTConstants::METHOD_POST:
                $model = new ProductionPlanModel();
                return array($model->addProductionPlan($payload));
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
            } else {
                $model = new SkisModel();
                return $model->getSkiTypes();
            }
        } else {
            return array(false);
        }
    }

    /**
     * Verifies that the request contains a valid authorisation token.
     * @param string $token the authorisation token to be verified
     * @param string $endpoint the endpoint which has been accessed
     * @throws APIException with the code set to HTTP_FORBIDDEN if the token is not valid
     */
    public function authorise(string $token, string $endpoint)
    {
        if (!(new AuthorisationModel())->isValid($token, $endpoint)) {
            throw new APIException(RESTConstants::HTTP_FORBIDDEN, $endpoint);
        }
    }
}



