<?php

require('../vendor/autoload.php');
use ColorThief\ColorThief;

$app = new Silex\Application();
$app['debug'] = true;

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