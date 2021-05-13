<?php
require_once "models/SkisModel.php";

class PayloadValidation
{
    /** isValidPayload checks if the payload is valid
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $payload contains the payload in an array
     * @return bool returns true if the payload is valid, and false otherwise.
     */
    public function isValidPayload(array $uri, string $requestMethod, array $payload): bool
    {
        return match ($requestMethod) {
            RESTConstants::METHOD_PUT, RESTConstants::METHOD_DELETE, RESTConstants::METHOD_GET => true,
            RESTConstants::METHOD_POST => $this->isValidPostPayload($uri, $payload),
            RESTConstants::METHOD_PATCH => $this->isValidPatchPayload($uri, $payload),
            default => false,
        };
    }

    /** Checks if the Payload is valid when method POST is used is valid
     * @param array $uri contains the path in an array
     * @param array $payload contains the payload in an array
     * @return bool returns true if the payload is valid, and false otherwise
     */
    public function isValidPostPayload(array $uri, array $payload): bool
    {
        if(empty($payload)) {
            return false;
        }
        return match ($uri[0]) {
            RESTConstants::ENDPOINT_CUSTOMER => $this->isValidCustomerPayload($uri, $payload),
            RESTConstants::ENDPOINT_STOREKEEPER => $this->isValidStorekeeperPayload($uri, $payload),
            RESTConstants::ENDPOINT_PLANNER => $this->isValidPlannerPayload($uri, $payload),
            default => false,
        };
    }

    public function isValidPatchPayload(array $uri, array $payload): bool
    {
        if(empty($payload)) {
            return false;
        }
        return match ($uri[0]) {
            RESTConstants::ENDPOINT_SHIPPER, RESTConstants::ENDPOINT_CUSTOMERREP, RESTConstants::ENDPOINT_STOREKEEPER => $this->isValidUpdateOrderPayload($uri, $payload),
            default => false,
        };
    }

    /** Checks if the Payload is valid when the customer endpoint is used
     * @param array $uri contains the path in an array
     * @param array $payload contains the payload in an array
     * @return bool returns true if the payload is valid, and false otherwise
     */
    public function isValidCustomerPayload(array $uri, array $payload): bool {
        switch ($uri[1]) {
            case RESTConstants::ENDPOINT_ORDERS:
                if(empty($payload['skis'])) {
                    return false;
                }
                foreach ($payload['skis'] as $ski_type_id => $quantity) {
                    $model = new SkisModel();
                    if (!$model->skiTypeExist($ski_type_id) || !empty($payload['ski_type_id'])) {
                        return false;
                    }
                    if (!is_int($quantity)) {
                        return false;
                    }
                }
                return true;
        }
        return false;
    }

    /** Checks if the Payload is valid when the storekeeper endpoint is used
     * @param array $uri contains the path in an array
     * @param array $payload contains the payload in an array
     * @return bool returns true if the payload is valid, and false otherwise
     */
    public function isValidStorekeeperPayload(array $uri, array $payload): bool {
        switch ($uri[1]) {
            case RESTConstants::ENDPOINT_SKIS:
                if (!empty($payload['ski_type_id'])) {
                    $ski_type_id = $payload['ski_type_id'];

                    if ((int)$ski_type_id == 0 && is_int((int)$ski_type_id)) {
                        print("\"ski_type_id\" must be a valid number\n");
                        return false;
                    }
                    if ((new SkisModel())->skiTypeExist($ski_type_id)) {
                        return true;
                    } else {
                        print("Ski type with id " . $ski_type_id . " does not exist\n");
                        return false;
                    }
                }
                print("Body must contain a valid \"ski-type-id\" field\n");
                return false;
            default:
                return false;
        }
    }

    /** Checks if the Payload is valid when the planner endpoint is used
     * @param array $uri contains the path in an array
     * @param array $payload contains the payload in an array
     * @return bool returns true if the payload is valid, and false otherwise
     */
    public function isValidPlannerPayload(array $uri, array $payload): bool {
        switch ($uri[1]) {
            case RESTConstants::ENDPOINT_PRODUCTION_PLAN:
                if (empty($payload['month']) || empty($payload['skis'])) {
                    return false;
                }
                foreach ($payload['skis'] as $ski_type_id => $quantity) {
                    $model = new SkisModel();
                    if (!$model->skiTypeExist($ski_type_id)) {
                        return false;
                    }
                    if (!is_int($quantity)) {
                        return false;
                    }
                }
                return true;
        }
    }

    public function isValidUpdateOrderPayload(array $uri, array $payload): bool
    {
        switch($uri[1]) {
            case RESTConstants::ENDPOINT_ORDERS:
                $state = strtolower($payload['state']);
                if(!empty($state) && is_string($state)) {
                    $endpoint = $uri[0];
                    if( ($state == "new" && $endpoint == RESTConstants::ENDPOINT_CUSTOMERREP) ||
                        ($state == "open" && $endpoint == RESTConstants::ENDPOINT_CUSTOMERREP) ||
                        ($state == "skis-available" && $endpoint == RESTConstants::ENDPOINT_CUSTOMERREP) ||
                        ($state == "ready-for-shipping" && $endpoint == RESTConstants::ENDPOINT_STOREKEEPER) ||
                        ($state == "shipped" && $endpoint == RESTConstants::ENDPOINT_SHIPPER)) {
                        return true;
                    }
                }
                return false;
            default:
                return false;
        }
    }

}

