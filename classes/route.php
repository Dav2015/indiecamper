<?php

namespace INDIECAMPER;

class Route {

    public function __construct($gps) {
        $this->gps = $gps;
    }

    public function createRoute($locations) {
        $routes = [];
        $numlocs = count($locations);

        for ($l = 0; $l < $numlocs - 1; $l++) {
            $startloc = $locations[$l];
            $startlatlong = explode(",", $startloc);
            $startlat = doubleval($startlatlong[0]);
            $startlong = doubleval($startlatlong[1]);

            $endloc = $locations[$l + 1];
            $endlatlong = explode(",", $endloc);
            $endlat = doubleval($endlatlong[0]);
            $endlong = doubleval($endlatlong[1]);

            $newRoute = $this->seachdb($startlat, $startlong, $endlat, $endlong);
            $routes = array_replace($routes, $newRoute);
        }
        return array_values($routes);
    }

    private function seachdb($startlat, $startlong, $endlat, $endlong) {
        $distance = $this->gps->distanceInKmBetweenEarthCoordinates($startlat, $startlong, $endlat, $endlong);
        $bearing = $this->gps->bearing($startlat, $startlong, $endlat, $endlong);

        $parallelSearch = 40;
        $placesContainer = [];

        for ($dSearch = 1; $dSearch <= $distance; $dSearch = $dSearch + $parallelSearch * 0.5) {
            $midPoint = $this->gps->intermediatePoints($startlat, $startlong, $dSearch, $bearing);
            $places = $this->gps->searchPlaces($midPoint[0], $midPoint[1], $parallelSearch);
            $placesContainer = array_replace($placesContainer, $places);
        }

        return $placesContainer;
    }

}
