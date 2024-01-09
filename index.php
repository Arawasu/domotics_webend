<?php

/**
 * This class is the main brains of the whole application.
 * ALL requests are routed through this file to ensure that every request can be easily dispatched,
 * necessary classes can be pre-loaded, and constants/global functions can be defined.
 *
 * This class is the foundation for any MVC framework. As a matter of fact, this whole application is a
 * badly, hastily written) mini-MVC framework.
 *
 * Also relies on dependency injection heavily, with a twist.
 *
 * Class MainController
 */
class MainController {
    /**
     * @var Database
     */
    private $db;
    /**
     * @var
     */
    private $controller;
    /**
     * @var
     */
    private $method;
    /**
     * @var
     */
    private $param;

    /**
     * MainController constructor.
     */
    public function __construct() {
        $this->showErrors();
        $this->setGeneralConstants();
        $this->setSocketConstants();
        $this->autoloadDependencies();
        $this->db = new Database(true);

        $this->getRequest();
        $this->dispatch();
    }

    /**
     * 'Dispatch' an incoming request and inject the database attribute into any loaded controller.
     */
    private function dispatch() {
        $controller = new $this->controller($this->db);
        $action = $this->method;
        $controller->$action($this->param);
    }

    /**
     * Get a request based on the URL that is called,
     * splice the request into a controller and a method,
     * check if they actually exist, and save them in attributes if they do.
     * If they don't, just redirect to the login page.
     */
    private function getRequest() {
        $parts = explode('/', $_SERVER["REQUEST_URI"]);
        $parts = array_slice($parts, 1);
        $this->controller = ucfirst(strtolower($parts[0]));

        if (empty($parts[1])){
            header('Location: ' . ROOT_URL . 'admin/login/');
            exit;
        }

        $this->method = strtolower($parts[1]);

        if(!method_exists($this->controller, $this->method)){
            header('Location: ' . ROOT_URL . 'admin/login/');
            exit;
        }

        $this->param = array_slice($parts, 2);
    }

    /**
     * Autoload dependenceis from the controllers, helpers, and  models folders.
     * SPL autoloading is magic.
     * See: http://php.net/manual/en/function.spl-autoload-register.php
     */
    private function autoloadDependencies() {
        spl_autoload_register(function ($class) {
            $sources = array("controllers/$class.php", "helpers/$class.php", "models/$class.php");
            foreach ($sources as $source) {
                $source = APP . $source;
                if (file_exists($source)) {
                    require_once $source;
                }
            }
        });
    }

    /**
     * Hardcode error handling for ease of use. This way, the php.ini file doesn't need to be tweaked on every environment
     * the website is developed in.
     */
    private function showErrors() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    /**
     * Set general constants that are used throughout the application.
     */
    private function setGeneralConstants() {
        define("DS", DIRECTORY_SEPARATOR);
        define("ROOT", getcwd() . DS);
        define("ROOT_URL", "http://domotica.local/");
        define("APP", ROOT . "app" . DS);
    }

    /**
     * NOTE: this function isn't used anywhere, useful for reference/future use.
     *
     * Made by ca at php dot spamtrak dot org,
     * http://php.net/manual/en/function.socket-last-error.php
     */

    private function setSocketConstants(){
        define('ENOTSOCK',      88);    /* Socket operation on non-socket */
        define('EDESTADDRREQ',  89);    /* Destination address required */
        define('EMSGSIZE',      90);    /* Message too long */
        define('EPROTOTYPE',    91);    /* Protocol wrong type for socket */
        define('ENOPROTOOPT',   92);    /* Protocol not available */
        define('EPROTONOSUPPORT', 93);  /* Protocol not supported */
        define('ESOCKTNOSUPPORT', 94);  /* Socket type not supported */
        define('EOPNOTSUPP',    95);    /* Operation not supported on transport endpoint */
        define('EPFNOSUPPORT',  96);    /* Protocol family not supported */
        define('EAFNOSUPPORT',  97);    /* Address family not supported by protocol */
        define('EADDRINUSE',    98);    /* Address already in use */
        define('EADDRNOTAVAIL', 99);    /* Cannot assign requested address */
        define('ENETDOWN',      100);   /* Network is down */
        define('ENETUNREACH',   101);   /* Network is unreachable */
        define('ENETRESET',     102);   /* Network dropped connection because of reset */
        define('ECONNABORTED',  103);   /* Software caused connection abort */
        define('ECONNRESET',    104);   /* Connection reset by peer */
        define('ENOBUFS',       105);   /* No buffer space available */
        define('EISCONN',       106);   /* Transport endpoint is already connected */
        define('ENOTCONN',      107);   /* Transport endpoint is not connected */
        define('ESHUTDOWN',     108);   /* Cannot send after transport endpoint shutdown */
        define('ETOOMANYREFS',  109);   /* Too many references: cannot splice */
        define('ETIMEDOUT',     110);   /* Connection timed out */
        define('ECONNREFUSED',  111);   /* Connection refused */
        define('EHOSTDOWN',     112);   /* Host is down */
        define('EHOSTUNREACH',  113);   /* No route to host */
        define('EALREADY',      114);   /* Operation already in progress */
        define('EINPROGRESS',   115);   /* Operation now in progress */
        define('EREMOTEIO',     121);   /* Remote I/O error */
        define('ECANCELED',     125);   /* Operation Canceled */
    }
}

/**
 * Global function. Basically var_dump within pre tags so indentation shows up properly.
 * Will also immediately die/exit after printing. Extremely useful function and required in any php project.
 *
 * Shoutout to my mentor & previous boss Joost van Veen for the name.
 * https://joostvanveen.com/a-3/dump-helper-alternative-var_dump
 * @param $var
 */
function dump_exit($var) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die;
}

/**
 * Same as dump_exit, just without the die.
 * @param $var
 */
function dump($var) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}


/**
 * Generates a readable stack trace
 *
 * Made by jurchiks101 at gmail dot com
 * http://php.net/manual/en/function.debug-backtrace.php#112238
 * @return string
 */
function generateCallTrace()
{
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++)
    {
        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }

    return "\t" . implode("\n\t", $result);
}

$index = new MainController();
