<?php

namespace controllers;

use helpers\cart\CartHelper;
use models\Delivery;
use models\Product;
use models\Session;
use models\SessionOrder;
use system\Controller;
use system\Response;

/**
 * Class CartController
 * @package controllers
 */
class CartController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $connection   = $this->getApp()->getConnection();
        $productModel = new Product($connection);
        $sessionModel = new Session($connection);

        $list = $productModel->getCartProductList($sessionModel->getSessionCart());

        if (!count($list)) {
            $requestedUrl = $this->getRequest()->getPrevUrl();
            $response     = $this->getResponse();
            $response->redirect($requestedUrl ?? '/');
        }

        $jsonCart = $sessionModel->getSessionCartJson();

        $deliveryModel = new Delivery($connection);
        $deliveryData  = $deliveryModel->fetchAll();

        return $this->render(
            'cart',
            [
                'productList'  => $list,
                'currentCart'  => $jsonCart,
                'deliveryData' => $deliveryData,
            ]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionClear()
    {
        $connection   = $this->getApp()->getConnection();
        $sessionModel = new Session($connection);

        $sessionModel
            ->getSessionCart()
            ->clearItems();

        $response = $this->getResponse();
        $response->setResponseType(Response::RESPONSE_JSON);

        return $response->buildJsonAnswer();

    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionProcess()
    {
        $connection   = $this->getApp()->getConnection();
        $sessionModel = new Session($connection);
        $sessionOrder = new SessionOrder($connection);
        $request      = $this->getRequest();
        $sessionCart  = $sessionModel->getSessionCart();

        $response = $this->getResponse();
        $response->setResponseType(Response::RESPONSE_JSON);

        CartHelper::setPostData($request);

        $deliveryModel = new Delivery($connection);

        $tax = $deliveryModel->loadDeliveryData($request);

        if (!isset($tax)) {

            $result = $response->buildJsonAnswer(
                Product::INVALID_CART_CONTENT_CODE,
                Product::INVALID_CART_CONTENT_MESSAGE,
                $sessionCart
            );

            return $result;
        }

        $result = $response->buildJsonAnswer(
            Product::NO_ERROR_CODE,
            Product::NO_ERROR_MESSAGE,
            $sessionCart
        );

        if (!$sessionOrder->saveCart($sessionCart, $tax)) {
            $result[Response::JSON_MESSAGE_FIELD] = $sessionOrder->getErrorMessage();
            $result[Response::JSON_CODE_FIELD]    = $sessionOrder->getErrorCode();
        }

        return $result;
    }

    public function actionHistory()
    {
        $connection = $this->getApp()->getConnection();

        $sessionModel = new Session($connection);

        $jsonCart = $sessionModel->getSessionCartJson();

        return $this->render(
            'history',
            [
                'currentSession' => $sessionModel->getCurrentSession(),
                'orderList'      => $sessionModel->getSessionOrders(),
                'currentCart'    => $jsonCart,
            ]);

    }
}
