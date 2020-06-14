<?php

namespace system;

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Greg\Orm\Connection\ConnectionManager;
use Greg\Orm\Connection\MysqlConnection;
use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Connection\Pdo;

/**
 * Class Application
 * @package system
 */
class Application
{
    const DEFAULT_CONTROLLER_NAME        = 'site';
    const DEFAULT_CONTROLLER_METHOD_NAME = 'index';
    const DEFAULT_CONTROLLER_MASK        = '{controller:c}?';
    const DEFAULT_CONTROLLER_METHOD_MASK = '{method:c}?';
    const CONTROLLER_CLASS_POSTFIX       = 'Controller';
    const CONTROLLER_ACTION_PREFIX       = 'action';

    const DEFAULT_SYSTEM_NAMESPACE     = 'system';
    const DEFAULT_MODEL_NAMESPACE      = 'models';
    const DEFAULT_HELPERS_NAMESPACE    = 'helpers';
    const DEFAULT_CONTROLLER_NAMESPACE = 'controllers';

    const DEFAULT_VIEW_PATH  = 'views';
    const FULL_VIEW_PATH     = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::DEFAULT_VIEW_PATH;
    const DEFAULT_CACHE_PATH = 'cache';
    const FULL_CACHE_PATH    = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::DEFAULT_CACHE_PATH;

    const DB_CONNECTION          = 'db';
    const DB_CONNECTION_DSN      = 'dsn';
    const DB_CONNECTION_LOGIN    = 'login';
    const DB_CONNECTION_PASSWORD = 'password';

    /**
     * @var RouteCollector
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var ConnectionManager
     */
    protected $connectionManager;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->router = new RouteCollector();

        $this->request = new Request();

        $this->response = new Response();

        $this->config = new Configuration();

        $this->connectionManager = new ConnectionManager();

        $this->connectionManager->register(static::DB_CONNECTION, function () {
            return new MysqlConnection(
                new Pdo(
                    $this->config->getValue(
                        static::DB_CONNECTION,
                        static::DB_CONNECTION_DSN
                    ),
                    $this->config->getValue(static::DB_CONNECTION, static::DB_CONNECTION_LOGIN),
                    $this->config->getValue(static::DB_CONNECTION, static::DB_CONNECTION_PASSWORD)
                )
            );
        });

        $this->router->any('/' . static::DEFAULT_CONTROLLER_MASK . '/' . static::DEFAULT_CONTROLLER_METHOD_MASK, function ($controller = self::DEFAULT_CONTROLLER_NAME, $method = self::DEFAULT_CONTROLLER_METHOD_NAME) {

            $controllerName = static::DEFAULT_CONTROLLER_NAMESPACE . '\\' . $this->normalizeName($controller) . static::CONTROLLER_CLASS_POSTFIX;

            $controllerObject = new $controllerName($this);

            $fullMethodName = static::CONTROLLER_ACTION_PREFIX . $this->normalizeName($method);

            return $controllerObject->$fullMethodName();
        });

    }

    public function run()
    {
        try {

            $dispatcher = new Dispatcher($this->router->getData());
            $content    = $dispatcher->dispatch($this->request->getMethod(), $this->request->getUri());

            $this->response->run($content);

        } catch (\HttpRequestException $e) {
            print_r($e);
        } catch (HttpRouteNotFoundException $e) {
            print_r($e);
        } catch (HttpMethodNotAllowedException $e) {
            print_r($e);
        }

    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function normalizeName(string $name)
    {
        return ucFirst(strtolower($name));
    }

    /**
     * @return Configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager(): ConnectionManager
    {
        return $this->connectionManager;
    }

    /**
     * @return ConnectionStrategy
     * @throws \Exception
     */
    public function getConnection()
    {
        return $this->getConnectionManager()->connection(static::DB_CONNECTION);
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
