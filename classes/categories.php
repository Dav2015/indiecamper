<?php

namespace INDIECAMPER;

class Categories {

    public function __construct($db) {
        $this->db = $db;
    }

    function issetCategory($name) {
        $query = Query::isCategory($name);
        $result = $this->db->query($query)->fetch();
        return $result["result"];
    }

}
