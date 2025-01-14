<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.6.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ControllerException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Middleware\MiddlewareManager;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Di\Di;

/**
 * Class MvcManager
 * @package Quantum\Mvc
 */
class MvcManager
{

    /**
     * Handles the request
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     * @throws \Quantum\Exceptions\ControllerException
     * @throws \Quantum\Exceptions\CsrfException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\MiddlewareException
     * @throws \Quantum\Exceptions\SessionException
     * @throws \ReflectionException
     */
    public static function handle(Request $request, Response $response)
    {
        if ($request->getMethod() != 'OPTIONS') {

            if (current_middlewares()) {
                list($request, $response) = (new MiddlewareManager())->applyMiddlewares($request, $response);
            }

            $routeArgs = route_args();
            $callback = route_callback();

            if ($callback) {
                call_user_func_array($callback, self::getArgs($callback, $routeArgs));
            } else {
                $controller = self::getController();
                $action = self::getAction($controller);

                if ($controller->csrfVerification) {
                    Csrf::checkToken($request, session());
                }

                if (method_exists($controller, '__before')) {
                    call_user_func_array([$controller, '__before'], self::getArgs([$controller, '__before'], $routeArgs));
                }

                call_user_func_array([$controller, $action], self::getArgs([$controller, $action], $routeArgs));

                if (method_exists($controller, '__after')) {
                    call_user_func_array([$controller, '__after'], self::getArgs([$controller, '__after'], $routeArgs));
                }
            }
        }
    }

    /**
     * Get Controller
     * @return \Quantum\Mvc\QtController
     * @throws \Quantum\Exceptions\ControllerException
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private static function getController(): QtController
    {
        $fs = Di::get(FileSystem::class);

        $controllerPath = modules_dir() . DS . current_module() . DS . 'Controllers' . DS . current_controller() . '.php';

        if (!$fs->exists($controllerPath)) {
            throw ControllerException::controllerNotFound(current_controller());
        }

        require_once $controllerPath;

        $controllerClass = '\\Modules\\' . current_module() . '\\Controllers\\' . current_controller();

        if (!class_exists($controllerClass, false)) {
            throw ControllerException::controllerNotDefined(current_controller());
        }

        return new $controllerClass();
    }

    /**
     * Get Action
     * @param \Quantum\Mvc\QtController $controller
     * @return string|null
     * @throws \Quantum\Exceptions\ControllerException
     */
    private static function getAction(QtController $controller): ?string
    {
        $action = current_action();

        if ($action && !method_exists($controller, $action)) {
            throw ControllerException::actionNotDefined($action);
        }

        return $action;
    }

    /**
     * Get arguments
     * @param callable $callable
     * @param array $routeArgs
     * @return array
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private static function getArgs(callable $callable, array $routeArgs): array
    {
        return Di::autowire($callable, $routeArgs);
    }

}
