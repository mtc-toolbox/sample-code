<?php

namespace helpers;

use helpers\cart\CartHelper;
use models\Product;
use models\Rate;
use models\Session;
use system\Application;
use system\Request;
use system\Response;

/**
 * Class VoteHelper
 * @package helpers
 */
class VoteHelper
{
    const DATA_CART_FIELD  = 'cart';
    const CART_ID_FIELD    = 'cartId';
    const PRODUCT_ID_FIELD = 'id';
    const VOTE_FIELD       = 'vote';

    const DEFAULT_JSON_ERROR_CODE   = 404;
    const DEFAULT_JSON_SUCCESS_CODE = 0;

    const DEFAULT_JSON_ERROR_MESSAGE   = 'Invalid vote';
    const DEFAULT_JSON_SUCCESS_MESSAGE = 'OK';

    const RATED_PRODUCT_ID_FIELD = 'Id';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var CartHelper|null
     */
    protected $cart;

    /**
     * @var |null
     */
    protected $productId;

    /**
     * @var int
     */
    protected $vote;

    /**
     * VoteHelper constructor.
     *
     * @param Application $app
     * @param Request     $request
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->request = $app->getRequest();

        $this->response = $app->getResponse();

        $data = $this->request->post();

        $this->cart = CartHelper::getInstance(null);

        $this->productId = $data[static::PRODUCT_ID_FIELD] ?? null;

        $this->vote = $data[static::VOTE_FIELD] ?? 0;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function vote()
    {

        if (isset($this->productId)) {

            $productData = $this->getProductData();

            if (isset($productData)) {
                $rate = new Rate(
                    $this
                        ->getApp()
                        ->getConnection()
                );

                $currentCart    = $this->getCart();
                $currentSession = $currentCart->getCartId() ?? null;

                $wasVoteCount = $this
                    ->getApp()
                    ->getConnection()
                    ->sqlFetchColumn(
                        'SELECT COUNT(*) FROM `Rate` WHERE `Session` = :session AND `Product` = :product  ',
                        [
                            'session' => $currentSession,
                            'product' => $this->productId,
                        ]
                    );

                if ($wasVoteCount) {
                    return $this->buildErrorJSon();
                }

                $records = $rate->insert([
                    'Session' => $currentSession,
                    'Product' => $this->productId ?? null,
                    'Rate'    => $this->vote,
                ]);

                if ($records) {
                    return $this->buildSuccessJson();
                }
            }
        }

        return $this->buildErrorJSon();
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * @return CartHelper|null
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Application $app
     *
     * @return $this
     */
    public function setApp(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * @param $cart
     *
     * @return $this
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    protected function getProductData()
    {
        $product     = new Product($this->getApp()->getConnection());
        $productData = $product
            ->select('Id', 'AverageRate', 'RateCount')
            ->where('Id', $this->productId)
            ->fetch();

        return $productData;
    }

    /**
     * @param int $code
     *
     * @return array
     * @throws \Exception
     */
    protected function buildSuccessJson(int $code = self::DEFAULT_JSON_SUCCESS_CODE, $message = self::DEFAULT_JSON_SUCCESS_MESSAGE)
    {
        $productData = $this->getProductData();

        $response = $this
            ->response
            ->buildJsonAnswer($code, $message, $productData);

        return $response;
    }

    /**
     * @param int    $code
     * @param string $message
     *
     * @return array
     */
    protected function buildErrorJson(int $code = self::DEFAULT_JSON_ERROR_CODE, $message = self::DEFAULT_JSON_ERROR_MESSAGE)
    {
        $response = $this
            ->response
            ->buildJsonAnswer($code, $message, []);

        return $response;
    }

}
