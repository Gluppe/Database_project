<?php

/**
 * Class RESTConstants class for application constants.
 */
class RESTConstants
{
    // HTTP method names
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    // HTTP status codes
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;

    const ENDPOINT_PUBLIC = 'public';
    const ENDPOINT_CUSTOMER = 'customer';
    const ENDPOINT_SHIPPER = 'shipper';
    const ENDPOINT_PLANNER = 'planner';
    const ENDPOINT_CUSTOMERREP = 'customer-rep';
    const ENDPOINT_STOREKEEPER = 'storekeeper';

    const ENDPOINT_ORDER = 'order';
    const ENDPOINT_ORDERS = 'orders';
    const ENDPOINT_PRODUCTION_PLAN = "production-plan";
    const ENDPOINT_SKITYPES = "ski-types";
    const ENDPOINT_SKI = "ski";
    const ENDPOINT_SKIS = "skis";
    const ENDPOINT_SHIPMENT = "shipment";
}
