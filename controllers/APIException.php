<?php

/**
 * Class APIException an exception class thrown whenever the request could not be successfully handled by the API.
 */
class APIException extends Exception
{
    /**
     * @var string $instance the URI of the instance finding that the request could not be successfully handled.
     */
    protected $instance;

    /**
     * @var int the detailed error code - as specified in the RESTConstant class.
     */
    protected $detailCode;

    public function __construct($code, $instance, $detailCode = -1, $message = "", Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->instance = $instance;
        $this->detailCode = $detailCode;
    }

    public function getInstance() {
        return $this->instance;
    }

    public function getDetailCode()
    {
        return $this->detailCode;
    }
}
