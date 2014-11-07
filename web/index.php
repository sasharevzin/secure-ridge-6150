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
  $palette = ColorThief::getPalette('http://lokeshdhakar.com/projects/color-thief/img/photo1.jpg');
  return 'Hello '.$palette;
});

$app->run();

?>
