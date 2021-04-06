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

    const ENDPOINT_ORDERS = 'Orders';
    const ENDPOINT_ORDER = 'Order';
    const ENDPOINT_SKIS = 'Skis';
    const ENDPOINT_SKI = 'Ski';
    const ENDPOINT_SHIPMENT = 'Shipment';
    const ENDPOINT_PRODUCTIONPLAN = 'Production-Plan';
}
