<?php

require('../vendor/autoload.php');
use ColorThief\ColorThief;

$app = new Silex\Application();
$app['debug'] = true;

$app->after(function (Request $request, Response $response) {
  $appl = $request->attributes->get('app');

  if ($request->get('jsonp_callback') !== null 
      && $request->getMethod() === 'GET') 
  {
     /** 
      * Configured via ConfigServiceProvider (https://github.com/igorw/ConfigServiceProvider)
      * Example:
      *      
      * $config['jsonp_able_route_patterns'] = array(
      *     '/{query}', 
      *     '/teaser/{amount}'
      * );
      */
      $jsonpAbleRoutePatterns = $appl['jsonp_able_route_patterns'];
      $actualRouteKey = str_replace('GET_', '', $request->attributes->get('_route'));
      $routes = $appl['routes']->all();

      foreach ($routes as $routeKey => $route) {
          $routeKey = str_replace('GET_', '', $routeKey);
          if ($routeKey === $actualRouteKey 
              && !in_array($route->getPattern(), $jsonpAbleRoutePatterns)) 
          {
              return;
          }
      }
      $response->headers->set('Content-Type', 'application/javascript');
      $response->setContent($request->get('jsonp_callback') . '(' . $response->getContent() . ');');
  }
});

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');

  $limit = $_GET['limit'];
  $url = $_GET['url'];

  if(!isSet($limit)){
    $limit = 9;
  }

  $palette = ColorThief::getPalette($url, $limit);
  return $app->json($palette);
});

$app->run();

?>