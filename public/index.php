<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;

require __DIR__ . '/../vendor/autoload.php';

$dbconfig = require(__DIR__ . '/../config/db.php');

$mysql = new mysqli($dbconfig['server'], $dbconfig['user'], $dbconfig['password']);
$db = $mysql->select_db($dbconfig['dbname']);
if(!$db){
$sql = 'CREATE SCHEMA IF NOT EXISTS ' . $dbconfig['dbname'];
$res = $mysql->query($sql);
if(!$res){
    error_log('Error: ' . $mysql->error);
}else{
    $db = $mysql->select_db($dbconfig['dbname']);
}
$sql = "CREATE TABLE IF NOT EXISTS products (`id` int AUTO_INCREMENT PRIMARY KEY,`slug` varchar(2000), `name` varchar(2000), `quantity` int, `variants` varchar(2000));";
$res = $mysql->query($sql);
if(!$res){
    error_log('Error: ' . $mysql->error);
}
}



/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

// Add Routing Middleware
$app->addRoutingMiddleware();

/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);



// Define app routes


$app->get('/products/list/[{page}]', function (Request $request, Response $response, $args) use($mysql){
    $page = $args['page'] ?? 0;
    $res = $mysql->query("SELECT * FROM products LIMIT $page,10");
    if(!$res) error_log('Error: ' . $mysql->error);
    $res = $res->fetch_all(MYSQLI_ASSOC);
    if(count($res)<1){
        $res = ['message'=>'No elements to show.'];
    }
    $response->getBody()->write(json_encode($res));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
});

$app->get('/products/get/{id}', function (Request $request, Response $response, $args) use($mysql) {
    $id = $args['id'];
    $res = $mysql->query("SELECT * FROM products WHERE ID = $id");
    if(!$res) error_log('Error: ' . $mysql->error);
    $res = $res->fetch_all(MYSQLI_ASSOC);
    if(count($res)<1){
        $res = ['message'=>'No elements to show.'];
    }
    $response->getBody()->write(json_encode($res));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
});


$app->get('/products/slug/{slug}', function (Request $request, Response $response, $args) use($mysql){
    $slug = $args['slug'];
    $sql = 'SELECT * FROM products WHERE slug = "' . $slug . '"';
    $res = $mysql->query($sql);
    if(!$res) error_log('Error: ' . $mysql->error);
    $res = $res->fetch_all(MYSQLI_ASSOC);
    if(count($res)<1){
        $res = ['message'=>'No elements to show.'];
    }
    $response->getBody()->write(json_encode($res));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
});

$app->get('/products/like/{id}', function (Request $request, Response $response, $args) use($mysql) {
    $id = $args['id'];

    $res = $mysql->query("SELECT name FROM products WHERE id = $id");
    if(!$res) error_log('Error: ' . $mysql->error);
    $getName=$res->fetch_array(MYSQLI_ASSOC)['name'];


    $sql2 ='SELECT * FROM products WHERE name = "' . $getName . '"' ;
    $res2 = $mysql->query($sql2);
    if(!$res2) error_log('Error: ' . $mysql->error);
    $res = $res->fetch_all(MYSQLI_ASSOC);
    if(count($res)<1){
        $res = ['message'=>'No elements to show.'];
    }
    $response->getBody()->write(json_encode($res));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
});

$app->get('/products/search/{param}/{value}', function (Request $request, Response $response, $args) use($mysql){
    $param = $args['param'];
    $value = $args['value'];
    $value = '"'.$value.'"';
    $res = $mysql->query("SELECT * FROM products WHERE $param = $value");
    if(!$res) error_log('Error: ' . $mysql->error);
    $res = $res->fetch_all(MYSQLI_ASSOC);
    if(count($res)<1){
        $res = ['message'=>'No elements to show.'];
    }
    $response->getBody()->write(json_encode($res));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
});

$app->post('/products/post/', function (Request $request, Response $response, $args) use($mysql){
    $params = (array)$request->getParsedBody();
    $res = $mysql->query($params['sql']);
    if(!$res) error_log('Error: ' . $mysql->error);
    $res = $res->fetch_all(MYSQLI_ASSOC);
    if(count($res)<1){
        $res = ['message'=>'No elements to show.'];
    }
    $response->getBody()->write(json_encode($res));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withStatus(201);
});

// Run app
$app->run();