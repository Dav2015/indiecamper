<?php

use Slim\Http\Request;
use Slim\Http\Response;

require '../classes/worldgps.php';
require '../helper/valitation.php';
require '../classes/route.php';
require '../classes/categories.php';
require '../classes/query.php';


// Routes
$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");
    // Render index view
    return $this->renderer->render($response, 'documentation.html');
});


$app->get('/route[/{category}]', function (Request $request, Response $response, array $args) {

    $category = new INDIECAMPER\Categories($this->db);

    //check if exist category and if category exist in database
    $catName = (isset($args["category"]) ? $args["category"] : NULL);
    $catResult = ($catName != NULL ? $category->issetCategory($catName) : 1);

    $inputs = array("points");
    $isRequestOK = checkEssentialInputs($request->getParams(), $inputs);
    //if something is missing send a error bad request
    if (!$isRequestOK || !$catResult) {
        $newResponse = $response->withStatus(400);
        return $newResponse;
    }

    $gps = new INDIECAMPER\WorldGPS($this->db, $catName);
    $route = new INDIECAMPER\Route($gps);

    $points = $request->getParam("points");
    $points = explode(";", $points);
    if (count($points) < 2) {
        $newResponse = $response->withStatus(400);
        return $newResponse;
    }
    //create route
    $routePlaces = $route->createRoute($points);

    $newResponse = $response->withJson($routePlaces, 200, JSON_PRETTY_PRINT);
    return $newResponse;
});

$app->get('/near[/{category}]', function (Request $request, Response $response, array $args) {

    $category = new INDIECAMPER\Categories($this->db);
    $catName = (isset($args["category"]) ? $args["category"] : NULL);
    $catResult = ($catName != NULL ? $category->issetCategory($catName) : 1);

    $inputs = array("point");
    $isRequestOK = checkEssentialInputs($request->getParams(), $inputs);

    if (!$isRequestOK || !$catResult) {
        $newResponse = $response->withStatus(400);
        return $newResponse;
    }

    $point = $request->getParam("point");

    $latlong = explode(",", $point);
    $lat = doubleval($latlong[0]);
    $long = doubleval($latlong[1]);

    $gps = new INDIECAMPER\WorldGPS($this->db, $catName);
    $nearPlaces = $gps->searchNear($lat, $long);

    $newResponse = $response->withJson($nearPlaces, 200, JSON_PRETTY_PRINT);
    return $newResponse;
});

