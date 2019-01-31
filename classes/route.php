<?php

namespace INDIECAMPER;

class Route {

    //this class create the highlight route
    public function __construct($gps) {
        //uses the global positioning system that handle the main calculations
        $this->gps = $gps;
    }

    //given two or more locations
    // prepare the start and end locations to make a search
    public function createRoute($locations) {
        $routes = [];
        $numlocs = count($locations);

        for ($l = 0; $l < $numlocs - 1; $l++) {
            //prepare start location
            $startloc = $locations[$l];
            $startlatlong = explode(",", $startloc);
            $startlat = doubleval($startlatlong[0]);
            $startlong = doubleval($startlatlong[1]);

            //prepare end location
            $endloc = $locations[$l + 1];
            $endlatlong = explode(",", $endloc);
            $endlat = doubleval($endlatlong[0]);
            $endlong = doubleval($endlatlong[1]);

            //get all highlights between two points
            $newRoute = $this->seachdb($startlat, $startlong, $endlat, $endlong);

            //merge last locations whit news, deleting repeated ones
            $routes = array_replace($routes, $newRoute);
        }
        return array_values($routes);
    }

    //return a array of places from given start to end location
    private function seachdb($startlat, $startlong, $endlat, $endlong) {
        //get distance between two gps locations
        $distance = $this->gps->distanceInKmBetweenEarthCoordinates($startlat, $startlong, $endlat, $endlong);

        //get bearing angle
        $bearing = $this->gps->bearing($startlat, $startlong, $endlat, $endlong);

        //how much parallel distance do you are willing to go? 40KMs?
        $parallelSearch = 40;
        $placesContainer = [];

        //from two locations make a search of places
        for ($dSearch = 1; $dSearch <= $distance; $dSearch = $dSearch + $parallelSearch * 0.5) {
            //in direction to end location, increment distance
            $midLocation = $this->gps->intermediatePoints($startlat, $startlong, $dSearch, $bearing);
            //search for near places whit in parallelSearch radius
            $places = $this->gps->searchPlaces($midLocation[0], $midLocation[1], $parallelSearch);
            //merge finded places near the line from the two points deleting repeated ones
            $placesContainer = array_replace($placesContainer, $places);
        }

        return $placesContainer;
    }
}
