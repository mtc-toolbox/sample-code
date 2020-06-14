<?php

namespace system;

use system\views\Layout;
use system\views\View;

/**
 * Class Controller
 * @package system
 */
class Controller
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var View
     */
    protected $view;

    protected $layout;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->view   = new View($this);
        $this->layout = new Layout($this);

        $this->pageData = [];
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->app->getRequest();
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->app->getResponse();
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
     * @return View
     */
    public function getView(): View
    {
        return $this->view;
    }

    /**
     * @param View $view
     *
     * @return $this
     */
    public function setView(View $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return Layout
     */
    public function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * @param Layout $layout
     *
     * @return $this
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @param string $view
     * @param array  $data
     * @param array  $mergeData
     *
     * @return string
     */
    public function render(string $view, array $data = [], array $mergeData = [])
    {
        $content = $this->view->render($view, $data, $mergeData);
        $result  = $this->layout
            ->setContent($content)
            ->renderPage();

        return $result;
    }

}
