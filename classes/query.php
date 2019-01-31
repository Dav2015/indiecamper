<?php

namespace INDIECAMPER;

class Query {

    private static function mainSearchQuery($lat, $long) {
        $query = "SELECT * FROM (SELECT *, 6371 * acos( cos( radians($lat) )
        *cos( radians( latitude ) ) * cos( radians( `longitude` ) - radians($long) ) +
        sin( radians($lat) ) * sin( radians( `latitude` ) ) ) AS distance FROM place) AS near";
        return $query;
    }

    public static function searchPlaces($lat, $long, $parallelSearch, $category) {
        $parallelWhere = " WHERE near.distance < $parallelSearch";
        $optionwhere = (isset($category) ? " AND near.category='$category'" : "");
        $createdQuery = Query::mainSearchQuery($lat, $long) . $parallelWhere . $optionwhere;
        return $createdQuery;
    }

    public static function searchNear($lat, $long, $category) {
        $optionwhere = (isset($category) ? " WHERE near.category='$category' " : "");
        $createdQuery = Query::mainSearchQuery($lat, $long) . $optionwhere . " ORDER BY distance LIMIT 1";
        return $createdQuery;
    }

    public static function isCategory($name) {
        $query = "SELECT EXISTS (SELECT 1 FROM category WHERE name = '$name') as result";
        return $query;
    }

}
