<?php


namespace system\views;


use system\Controller;

class Layout extends View
{
    const MAIN_LAYOUT_FILENAME = 'index';
    const MENU_FILENAME        = 'mainmenu';

    protected $content;

    protected $title;

    protected $data;

    public function __construct(Controller $controller = null)
    {
        parent::__construct($controller);

        $this->data = [];
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function renderPage()
    {
        $mainmenu = $this->render(static::MENU_FILENAME, $this->getData());

        $res = $this->render(
            static::MAIN_LAYOUT_FILENAME,
            $this->getData(),
            [
                'mainmenu' => $mainmenu,
                'title'    => $this->getTitle(),
                'content'  => $this->getContent(),
            ]);

        return $res;
    }
}
