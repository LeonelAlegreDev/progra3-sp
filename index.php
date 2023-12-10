<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Middleware\RoutingMiddleware;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/ClienteController.php';
require_once './controllers/ReservaController.php';
require_once './controllers/UsuarioController.php';
require_once './middlewares/AuthLevelOne.php';
require_once './middlewares/AuthLevelTwo.php';
require_once './middlewares/AccessLogging.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

$app->setBasePath('/Progra3-SP/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Index
$app->get('[/]', function (Request $request, Response $response) {    
  $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
  
  $response->getBody()->write($payload);
  return $response->withHeader('Content-Type', 'application/json');
});

// Clientes
$app->group('/clientes', function (RouteCollectorProxy $group) {
  // Consulta
  $group->post('/{id}', \ClienteController::class . ':TraerUno')->add(new AuthLevelTwo());
  
  // POST Cargar Cliente
  $group->post('[/]', \ClienteController::class . ':CargarUno')->add(new AuthLevelOne());

  // PUT Modificar Cliente
  $group->put('[/]', \ClienteController::class . ':ModificarUno');

  // POST Eliminar Cliente
  $group->delete('/{id}', \ClienteController::class . ':BorrarUno')->add(new AuthLevelOne());

});

// Reservas
$app->group('/reservas', function (RouteCollectorProxy $group) {
  // POST
  $group->post('[/]', \ReservaController::class . ':CargarUno');

  // GET All
  $group->get('[/]', \ReservaController::class . ':TraerTodos');

  // GET By ID Cliente
  $group->get('/cliente/{id}', \ReservaController::class . ':TraerReservasPorCliente');

  // GET Importes
  $group->get('/importes/', \ReservaController::class . ':TraerImportes');

  // Cancelar Reserva
  $group->post('/cancelar/{id}', \ReservaController::class . ':BorrarUno');

  // Ajustar Reserva
  $group->post('/ajustar/{id}', \ReservaController::class . ':ModificarUno');
})->add(new AuthLevelTwo());

// Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {  
  // POST Alta Usuarios
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
});

// Auth
$app->group('/auth', function (RouteCollectorProxy $group) {  
  // POST
  $group->post('/login', \UsuarioController::class . ':Login');
})->add(new AccessLogging.php());
$app->run();
