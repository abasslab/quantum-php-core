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
 * @since 2.5.0
 */

namespace Quantum;

use Quantum\Tracer\ErrorHandler;
use Quantum\Di\Di;

/**
 * Class App
 * @package Quantum
 */
class App
{

    /**
     * Starts the app
     * @throws \Quantum\Exceptions\CsrfException
     * @throws \Quantum\Exceptions\RouteException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\MiddlewareException
     * @throws \Quantum\Exceptions\ControllerException
     * @throws \Quantum\Exceptions\ModuleLoaderException
     * @throws \Quantum\Exceptions\LoaderException
     * @throws \Quantum\Exceptions\LangException
     * @throws \Quantum\Exceptions\EnvException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public static function start()
    {
        self::loadCoreFunctions();

        Di::loadDefinitions();

        ErrorHandler::setup();

        Bootstrap::run();
    }

    /**
     * Loads the core functions
     */
    public static function loadCoreFunctions()
    {
        foreach (glob(HELPERS_DIR . DS . 'functions' . DS . '*.php') as $filename) {
            require_once $filename;
        }
    }

}