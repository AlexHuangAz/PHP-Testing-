<?php
namespace {
  require_once 'vendor/autoload.php';

  use GuzzleHttp\Client;
  use Raygun4php\RaygunClient;
  use Raygun4php\Transports\GuzzleAsync;

  $httpClient = new Client([
    'base_uri' => 'https://api.raygun.com',
    'headers' => ['X-ApiKey' => 'YSakFrZlkhnjSYf1NcWA']
  ]);
  $transport = new GuzzleAsync($httpClient);
  $raygunClient = new RaygunClient($transport);

  set_error_handler(function($errno, $errstr, $errfile, $errline) use ($raygunClient) {
    $raygunClient->SendError($errno, $errstr, $errfile, $errline);
  });
  set_exception_handler(function($exception) use ($raygunClient) {
    $raygunClient->SendException($exception);
  });
  register_shutdown_function(function() use ($raygunClient) {
    $lastError = error_get_last();

    if (!is_null($lastError)) {
      [$type, $message, $file, $line] = $lastError;
      $raygunClient->SendError($type, $message, $file, $line);
    }
  });
  register_shutdown_function([$transport, 'wait']);
}
