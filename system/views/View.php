<?php

namespace system\views;

use Jenssegers\Blade\Blade;
use system\Controller;
use system\Application;

class View
{
    /**
     * @var Blade
     */
    protected $blade;

    /**
     * @var array
     */
    protected $nestedViews = [];

    /**
     * @var Controller
     */
    protected $controller;

    public function __construct(Controller $controller = null)
    {
        $this->controller = $controller;
        $this->blade = new Blade(Application::FULL_VIEW_PATH, Application::FULL_CACHE_PATH);
    }

    /**
     * @return Blade
     */
    public function getBlade(): Blade
    {
        return $this->blade;
    }

    /**
     * @param Blade $blade
     *
     * @return $this
     */
    public function setBlade(Blade $blade)
    {
        $this->blade = $blade;

        return $this;
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     *
     * @return $this
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;

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
        $res = $this
            ->getBlade()
            ->render($view, $data, $mergeData);
        return $res;
    }

}
