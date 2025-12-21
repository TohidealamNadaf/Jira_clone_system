<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

$app = app();
$router = $app->getRouter();

// Test basic routes
$router->get('/test', 'TestController@test');
$router->group(['prefix' => '/api/v1'], function($router) {
    $router->get('/hello', 'ApiController@hello');
    $router->group(['prefix' => '/test'], function($router) {
        $router->get('/nested', 'ApiController@nested');
    });
});

// Check routes
$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

echo json_encode($routes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
