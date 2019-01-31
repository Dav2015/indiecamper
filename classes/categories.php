<?php

namespace INDIECAMPER;

class Categories {

    public function __construct($db) {
        $this->db = $db;
    }

    //check if a category exist in the database
    function issetCategory($name) {
        $query = Query::isCategory($name);
        $result = $this->db->query($query)->fetch();
        return $result["result"];
    }

}
