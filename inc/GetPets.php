<?php

class GetPets
{
    function __construct()
    {
        global $wpdb;
        $tableName = $wpdb->prefix . "pets";
        // $petQuery = $wpdb->prepare("SELECT * FROM $tableName WHERE species = %s LIMIT 100", array($_GET['species']));

        $this->args = $this->getArgs();
        // You can feed args directly into prepare instead of placeholders!
        $this->placeholders = $this->createPlaceholders();

        $query = "SELECT * FROM $tableName ";
        $query .= $this->createWhereText();
        $query .= " LIMIT 100";

        $countQuery = "SELECT COUNT(*) FROM $tableName ";
        $countQuery .= $this->createWhereText();

        // You can use args instead of placeholders
        $this->count = $wpdb->get_var($wpdb->prepare($countQuery, $this->placeholders));
        $this->pets = $wpdb->get_results($wpdb->prepare($query, $this->placeholders));
    }

    function getArgs()
    {
        $temp = array(
            "favcolor" => sanitize_text_field($_GET["favcolor"]),
            "species" => sanitize_text_field($_GET["species"]),
            "minyear" => sanitize_text_field($_GET["minyear"]),
            "maxyear" => sanitize_text_field($_GET["maxyear"]),
            "minweight" => sanitize_text_field($_GET["minweight"]),
            "maxweight" => sanitize_text_field($_GET["maxweight"]),
            "favhobby" => sanitize_text_field($_GET["favhobby"]),
            "favfood" => sanitize_text_field($_GET["favfood"]),
        );

        // This is a simple filter - if $x is true, it will be added to the array
        return array_filter($temp, function ($x) {
            return $x;
        });
    }

    function createPlaceholders()
    {
        // Take an associative array and return a regular array
        return array_map(function ($x) {
            return $x;
        }, $this->args);
    }

    function specificQuery($index)
    {
        switch ($index) {
            case "minweight":
                return "petweight >= %d";
            case "maxweight":
                return "petweight <= %d";
            case "minyear":
                return "birthyear >= %d";
            case "maxwyear":
                return "birthyear <= %d";
            default:
                return $index . " = %s";
        }
    }

    function createWhereText()
    {
        $whereQuery = "";

        // If there's no arguments
        if (count($this->args)) {
            $whereQuery = "WHERE ";
        }

        $currentPosition = 0;
        foreach ($this->args as $index => $item) {
            $whereQuery .= $this->specificQuery($index);

            if ($currentPosition != count($this->args) - 1) {
                $whereQuery .= " AND ";
            }
            $currentPosition++;
        }

        return $whereQuery;
    }
}
