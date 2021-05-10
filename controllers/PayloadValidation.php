<?php

class PayloadValidation {
    /** isValidPayload checks if the payload is valid
     * @param array $uri contains the path in an array
     * @param string $requestMethod contains the request method used
     * @param array $payload contains the payload
     * @return bool returns true if the payload is valid, and false otherwise.
     */
    public function isValidPayload(array $uri, string $requestMethod, array $payload): bool {
        return match ($requestMethod) {
            RESTConstants::METHOD_PUT, RESTConstants::METHOD_DELETE, RESTConstants::METHOD_GET => true,
            RESTConstants::METHOD_POST => $this->isValidPostPayload($uri, $payload),
            default => false,
        };
    }

    public function isValidPostPayload(array $uri, array $payload): bool{
        switch ($uri[0]) {
            case RESTConstants::ENDPOINT_CUSTOMER:
                switch($uri[1]) {
                    case RESTConstants::ENDPOINT_ORDERS:
                        foreach ($payload['skis'] as $ski_type_id => $quantity) {
                            $model = new SkisModel();
                            if(!$model->skiTypeExist($ski_type_id) && !empty($payload['ski_type_id'])) {
                                return false;
                            }
                            if(!is_int($quantity)) {
                                return false;
                            }
                        }
                        return true;
                }
                return false;
            case RESTConstants::ENDPOINT_STOREKEEPER:
                switch($uri[1]) {
                    case RESTConstants::ENDPOINT_SKIS:
                        if(empty(($payload['ski_type_id']) || (int)$payload['ski_type_id'] == 0) && is_int((int)$payload['ski_type_id'])) {
                            return false;
                        } else {
                            return true;
                        }
                }
                return false;
            case RESTConstants::ENDPOINT_PLANNER:
                switch($uri[1]) {
                    case RESTConstants::ENDPOINT_PRODUCTION_PLAN:
                        if(empty($payload['month']) || empty($payload['skis'])) {
                            return false;
                        }
                        foreach ($payload['skis'] as $ski_type_id => $quantity) {
                            $model = new SkisModel();
                            if(!$model->skiTypeExist($ski_type_id)) {
                                return false;
                            }
                            if(!is_int($quantity)) {
                                return false;
                            }
                        }
                        return true;
                }
                return false;
            default:
                return false;
        }
    }
}