<?php

namespace INDIECAMPER;

class WorldGPS {

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

    function distanceInKmBetweenEarthCoordinates($lat1, $lon1, $lat2, $lon2) {
        $dLat = $this->toRadians($lat2 - $lat1);
        $dLon = $this->toRadians($lon2 - $lon1);

        $lat1 = $this->toRadians($lat1);
        $lat2 = $this->toRadians($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) +
                sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $this->earthRadiusKm * $c;
    }

    function bearing($startlat, $startlong, $endlat, $endlong) {
        $deltaLong = $this->toRadians(abs($startlong - $endlong));
        $startlat = $this->toRadians($startlat);
        $endlat = $this->toRadians($endlat);

        $X = cos($endlat) * sin($deltaLong);
        $Y = cos($startlat) * sin($endlat) - sin($startlat) * cos($endlat) * cos($deltaLong);

        $bearing = atan2($X, $Y);
        return $bearing;
    }

    function intermediatePoints($startlat, $startlong, $actualDistance, $bearing) {
        $startlat = $this->toRadians($startlat);
        $startlong = $this->toRadians($startlong);

        $angularDist = $actualDistance / $this->earthRadiusKm;

        //la2 =  asin(sin la1 * cos Ad  + cos la1 * sin Ad * cos θ)
        $lat = asin(sin($startlat) * cos($angularDist) + cos($startlat) * sin($angularDist) * cos($bearing));

        //lo2 = lo1 + atan2(sin θ * sin Ad * cos la1 , cos Ad – sin la1 * sin la2)
        $long = $startlong + atan2(sin($bearing) * sin($angularDist) * cos($startlat), cos($angularDist) - sin($startlat) * sin($lat));

        $out = [$this->toDegrees($lat), $this->toDegrees($long)];
        return $out;
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
