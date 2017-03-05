<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
// spl_autoload_register(function ($classname) {
//     require ("../ap/api/" . $classname . ".php");
// });

//Config
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app =  new \Slim\App(["settings" => $config]);

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$container = $app->getContainer();

$container["jwt"] = function ($container) {
    return new StdClass;
};

$app->add(new \Slim\Middleware\JwtAuthentication([
	"path" => ["/api", "/admin"],
    "passthrough" => ["/api/login", "/admin/ping", "/api/user"],
    "algorithm" => "HS256",
    "secret" => getenv("JWT_SECRET"),
    "callback" => function ($request, $response, $arguments) use ($container) {
        $container["jwt"] = $arguments["decoded"];
    },
    "rules" => [
        new \Slim\Middleware\JwtAuthentication\RequestMethodRule([
            "passthrough" => ["OPTIONS", "GET"]
        ])
    ],
    "error" => function ($request, $response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

require_once('../app/api/ads.php');
require_once('../app/api/user.php');
require_once('../app/api/search.php');
require_once('../app/api/upload.php');
require_once('../app/api/booking.php');
require_once('../app/api/message.php');
require_once('../app/api/notification.php');

$app->run();
