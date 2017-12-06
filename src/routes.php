<?php

use Slim\Http\Request;
use Slim\Http\Response;
//require_once(__DIR__."/../src/classes/wooapiconnect.php");
use Purpose\Classes\WooapiConnect;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
// Routes
//
//$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
//    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
//
//    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
//});

$app->get('/',function (Request $request, Response $response, array $args){

    $woo = new wooapiconnect("http://princesspurpose.co.za", [
    'wp_api' => true,
    'version' => 'wc/v2',
    ]);



    try{
        $body = $woo->theClient->get('orders',['per_page'=>100,'status'=>'processing']);
//        $body= $woo->get('orders',["per_page"=>100,"status"=>"completed"]);
//        $lastRequest=$woo->http->getRequest();
//        $lastResponse = $woo->http->getResponse();
    //    print_r($body);
//        var_dump($body);
            $converted = $woo->buildResults($body);
    }catch ( HttpClientException $e){

        $e->getMessage(); // Error message.
        $e->getRequest(); // Last request data.
        $e->getResponse(); // Last response data.
        echo "Could not get any data";
        echo $e;
    }
    exit;
//var_dump($converted);
   return $this->renderer->render($response,'index.phtml',["body"=>$converted]);
});
