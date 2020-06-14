<?php
namespace system;

/**
 * Class Request
 * @package helpers
 */
class Request
{

    /**
     * Raw query body
     */
    private $rawBody;

    public $methodParam = '_method';

    /**
     * @return string
     */
    public function getMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @return false|string|null
     */
    public function getRawBody()
    {
        if (!isset($this->rawBody)) {
            $this->rawBody = file_get_contents('php://input');
        }

        return $this->rawBody;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @return mixed
     */
    public function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * @return mixed
     */
    public function get()
    {
            return $_GET;
    }

    /**
     * @return mixed
     */
    public function post()
    {
            return $_POST;
    }

    /**
     * @return mixed|string
     */
    public function getPrevUrl()
    {
        return $_SESSION['url'] ?? '/';
    }

}
