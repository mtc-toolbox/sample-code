<?php

namespace controllers;

use helpers\cart\CartHelper;
use helpers\cart\Item;
use helpers\VoteHelper;
use models\Product;
use models\Rate;
use models\Session;
use system\Controller;
use system\Response;

class SiteController extends Controller
{
    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function actionVote()
    {
        $app = $this->getApp();

        $request = $this->getRequest();

        $response = $this->getResponse();


        $response->setResponseType(Response::RESPONSE_JSON);

        $result  = $response->buildJsonAnswer();

        if ($request->isPost()) {
            $vh = new VoteHelper($app, $request);
            $result = $vh->vote();
        }

        return $result;
    }

    public function actionCart()
    {

        $request = $this->getRequest();

        $response = $this->getResponse();

        $response->setResponseType(Response::RESPONSE_JSON);

        $result  = $this->getResponse()->buildJsonAnswer();

        if ($request->isPost()) {

            $app = $this->getApp();

            $connection = $app->getConnection();

            $sessionModel = new Session($connection);

            CartHelper::setPostData($request);

            $result['data'] = $sessionModel->getSessionCart();
        }

        return $result;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $connection = $this->getApp()->getConnection();

        $productModel = new Product($connection);

        $sessionModel = new Session($connection);

        $list = $productModel->getProductList($sessionModel->getSessionCart());

        $jsonCart = $sessionModel->getSessionCartJson();

        return $this->render(
            'products',
            [
                'productList' => $list,
                'currentCart' => $jsonCart,
            ]);
    }
}
