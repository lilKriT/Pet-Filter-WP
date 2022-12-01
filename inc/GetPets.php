<?php

class GetPets
{
    function __construct()
    {
        global $wpdb;
        $tableName = $wpdb->prefix . "pets";
        $petQuery = $wpdb->prepare("SELECT * FROM $tableName WHERE species = %s LIMIT 100", array($_GET['species']));
        $this->pets = $wpdb->get_results($petQuery);
    }
}
