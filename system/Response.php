<?php


namespace system;

/**
 * Class Response
 * @package helpers
 */
class Response
{
    const RESPONSE_JSON = 'Content-Type: application/json';
    const RESPONSE_HTML = 'Content-Type: text/html';

    const INVALID_HTML_REPONSE_DATA = 'Response content must be a string';
    const INVALID_JSON_REPONSE_DATA = 'Response content must be array';
    const INVALID_RESPONSE_TYPE     = 'Unknown response type';

    const JSON_CODE_FIELD    = 'code';
    const JSON_MESSAGE_FIELD = 'message';
    const JSON_DATA_FIELD    = 'data';


    /**
     * Current response type
     * @var string
     */
    protected $responseType;

    /**
     * Response constructor.
     */
    public function __construct()
    {
        $this->responseType = static::RESPONSE_HTML;
    }

    /**
     * @return string
     */
    public function getResponseType(): string
    {
        return $this->responseType;
    }

    /**
     * @param string $responseType
     *
     * @return $this
     */
    public function setResponseType(string $responseType)
    {
        $this->responseType = $responseType;

        return $this;
    }

    /**
     * @param $content
     *
     * @return $this
     * @throws \HttpRequestException
     */
    public function run($content)
    {
        if ($this->responseType == static::RESPONSE_HTML) {
            return $this->responseHtml($content);
        }

        if ($this->responseType == static::RESPONSE_JSON) {
            return $this->responseJson($content);
        }
        throw new \HttpRequestException(static::INVALID_RESPONSE_TYPE);
    }

    /**
     * @param int    $code
     * @param string $message
     * @param mixed  $data
     *
     * @return array
     */
    public function buildJsonAnswer(int $code = 0, string $message = '', $data = [])
    {
        return [
            static::JSON_CODE_FIELD    => $code,
            static::JSON_MESSAGE_FIELD => $message,
            static::JSON_DATA_FIELD    => $data,
        ];
    }

    /**
     * @param string $url
     */
    public function redirect(string $url)
    {
        header('Location: '.$url);
        die();
    }

    /**
     * @param $content
     *
     * @return $this
     * @throws \HttpRequestException
     */
    protected function responseHtml($content)
    {
        if (!is_string($content)) {
            throw new \HttpRequestException(static::INVALID_HTML_REPONSE_DATA);
        }

        $this->setHeader(static::RESPONSE_HTML);

        echo $content;

        return $this;
    }

    /**
     * @param $content
     *
     * @return $this
     * @throws \HttpRequestException
     */
    protected function responseJson($content)
    {
        if (!is_array($content)) {
            throw new \HttpRequestException(static::INVALID_JSON_REPONSE_DATA);
        }

        $this->setHeader(static::RESPONSE_JSON);

        echo json_encode($content);

        return $this;
    }

    /**
     * @param string $header
     *
     * @return $this
     */
    protected function setHeader(string $header)
    {
        header($header);

        return $this;
    }


}
