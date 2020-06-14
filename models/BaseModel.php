<?php

namespace models;

use Greg\Orm\Model;

/**
 * Class BaseModel
 * @package models
 */
class BaseModel extends Model
{
    const NO_ERROR_CODE    = 0;
    const NO_ERROR_MESSAGE = 'Ok';

    const SERVER_ERROR_CODE    = 2;
    const SERVER_ERROR_MESSAGE = 'Database access error. Please, try later.';


    const ERROR_MESSAGES = [
        self::SERVER_ERROR_CODE => self::SERVER_ERROR_MESSAGE,
        self::NO_ERROR_CODE     => self::NO_ERROR_MESSAGE,
    ];


    /**
     * @var int
     */
    protected $errorCode = 0;

    /**
     * @var string
     */
    protected $errorMessage = '';

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode(int $errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @param $errorMessage
     *
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @param int    $code
     * @param string $message
     *
     * @return BaseModel
     */
    public function setError(int $code = 0, string $message = '')
    {
        return $this
            ->setErrorCode($code)
            ->setErrorMessage($message);
    }

    /**
     * @return BaseModel
     */
    public function clearError()
    {
        return $this->setError();
    }

    /**
     * @param int $code
     *
     * @return string
     */
    public function getErrorMessageByCode(int $code = 0)
    {
        $result = '';
        if (!isset(self::ERROR_MESSAGES[$code])) {
            if (method_exists(get_parent_class($this), 'getErrorMessageByCode')) {
                $result = parent::getErrorMessageByCode($code);
            }
        } else {
            $result = self::ERROR_MESSAGES[$code];
        };

        return $result;
    }

    /**
     * @param int $code
     *
     * @return BaseModel
     */
    public function setErrorByCode(int $code = 0)
    {
        $message = $this->getErrorMessageByCode($code);
        return $this->setError($code, $message);
    }
}
