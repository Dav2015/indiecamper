<?php

namespace INDIECAMPER;

class WorldGPS {

    //created whit the information in https://www.movable-type.co.uk/scripts/latlong.html 

    private $earthRadiusKm = 6371;

    public function __construct($db, $catName) {
        $this->db = $db;
        //category
        $this->cat = $catName;
    }

    function toRadians($degrees) {
        return $degrees * pi() / 180;
    }

    function toDegrees($radians) {
        return $radians * 180 / pi();
    }

    //claculate distance between two locations
    //TODO need a upgrade, to take in count bearing
    function distanceInKmBetweenEarthCoordinates($startLat, $startLong, $endLat, $endLong) {
        $dLat = $this->toRadians($endLat - $startLat);
        $dLon = $this->toRadians($endLong - $startLong);

        $startLatR = $this->toRadians($startLat);
        $endLatR = $this->toRadians($endLat);

        $a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($startLatR) * cos($endLatR);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $this->earthRadiusKm * $c;
    }

    //because of the roundness of the Earth, a line between two locs is not a line, allways have some curve
    //for better result is calculated the bearing, basically is the angle of a curve between two points
    function bearing($startlat, $startlong, $endlat, $endlong) {
        $deltaLongR = $this->toRadians(abs($startlong - $endlong));
        $startlatR = $this->toRadians($startlat);
        $endlatR = $this->toRadians($endlat);

        $X = cos($endlatR) * sin($deltaLongR);
        $Y = cos($startlatR) * sin($endlatR) - sin($startlatR) * cos($endlatR) * cos($deltaLongR);

        $bearing = atan2($X, $Y);
        return $bearing;
    }

    //given a start location and a "bearing angle line", what is my actual position passed $actualDistance
    //for a visual interpretation go to: https://www.movable-type.co.uk/scripts/latlong.html 
    //whit the title "Destination point given distance and bearing from start point"
    function intermediatePoints($startlat, $startlong, $actualDistance, $bearing) {
        $startlatR = $this->toRadians($startlat);
        $startlongR = $this->toRadians($startlong);

        $angularDist = $actualDistance / $this->earthRadiusKm;

        //la2 =  asin(sin la1 * cos Ad  + cos la1 * sin Ad * cos θ)
        $lat = asin(sin($startlatR) * cos($angularDist) + cos($startlatR) * sin($angularDist) * cos($bearing));

        //lo2 = lo1 + atan2(sin θ * sin Ad * cos la1 , cos Ad – sin la1 * sin la2)
        $long = $startlongR + atan2(sin($bearing) * sin($angularDist) * cos($startlatR), cos($angularDist) - sin($startlatR) * sin($lat));

        $outLocation = [$this->toDegrees($lat), $this->toDegrees($long)];
        return $outLocation;
    }

    function searchPlaces($lat, $long, $parallelSearch) {
        $query = Query::searchPlaces($lat, $long, $parallelSearch, $this->cat);
        $places = $this->db->query($query)->fetchAll();

        $placesContainer = [];
        foreach ($places as $p) {
            $placesContainer[$p["placeID"]] = $p;
        }
        return $placesContainer;
    }

    function searchNear($lat, $long) {
        $query = Query::searchNear($lat, $long, $this->cat);
        $nearPlace = $this->db->query($query)->fetch();
        return $nearPlace;
    }

}
