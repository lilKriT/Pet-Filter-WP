<?php

/*
    Plugin Name: Pets Filter SQL
    Version: 1.0
    Author: lilKriT
    Author URI: https://lilkrit.dev
*/

if (!defined("ABSPATH")) exit;

require_once plugin_dir_path(__FILE__) . 'inc/generatePet.php';

class PetFilter
{
    function __construct()
    {
        global $wpdb;

        $this->charset = $wpdb->get_charset_collate();
        $this->tableName = $wpdb->prefix . "pets";

        add_action('activate_pets-sql/pet-filter.php', array($this, 'onActivate'));
        // add_action('admin_head', array($this, 'onAdminRefresh'));
        add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
        add_filter('template_include', array($this, 'loadTemplate'), 99);

        // For adding pets
        add_action("admin_post_createpet", array($this, "createPet"));
        add_action("admin_post_nopriv_createpet", array($this, "createPet"));
    }

    function createPet()
    {
        if (current_user_can('administrator')) {
            $pet = generatePet();
            $pet['petname'] = sanitize_text_field($_POST['incomingpetname']);
            global $wpdb;
            $wpdb->insert($this->tableName, $pet);
            wp_redirect(site_url("/pet-adoption"));
        } else {
            wp_redirect(site_url());
        }
    }

    function loadAssets()
    {
        if (is_page('pet-adoption')) {
            wp_enqueue_style('petFilterStyle', plugin_dir_url(__FILE__) . 'pet-filter.css');
        }
    }

    function loadTemplate($template)
    {
        if (is_page('pet-adoption')) {
            return plugin_dir_path(__FILE__) . 'inc/template-pets.php';
        }
        return $template;
    }

    function onActivate()
    {
        require_once(ABSPATH . "wp-admin/includes/upgrade.php");
        // This is a useful function. If the table already exists, it will not destroy it
        dbDelta("CREATE TABLE $this->tableName (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            birthyear smallint(5) NOT NULL DEFAULT 0,
            petweight smallint(5) NOT NULL DEFAULT 0,
            favfood varchar(60) NOT NULL DEFAULT '',
            favhobby varchar(60) NOT NULL DEFAULT '',
            favcolor varchar(60) NOT NULL DEFAULT '',
            petname varchar(60) NOT NULL DEFAULT '',
            species varchar(60) NOT NULL DEFAULT '',
            PRIMARY KEY  (id)
        ) $this->charset;");
    }

    function onAdminRefresh()
    {
        global $wpdb;
        $wpdb->insert($this->tableName, generatePet());
        // $this->populateFast();
    }

    function populateFast()
    {
        $query = "INSERT INTO $this->tableName (`species`, `birthyear`, `petweight`, `favfood`, `favhobby`, `favcolor`, `petname`) VALUES ";
        $numberofpets = 100000;
        for ($i = 0; $i < $numberofpets; $i++) {
            $pet = generatePet();
            $query .= "('{$pet['species']}', {$pet['birthyear']}, {$pet['petweight']}, '{$pet['favfood']}', '{$pet['favhobby']}', '{$pet['favcolor']}', '{$pet['petname']}')";
            if ($i != $numberofpets - 1) {
                $query .= ", ";
            }
        }
        /*
    Never use query directly like this without using $wpdb->prepare in the
    real world. I'm only using it this way here because the values I'm 
    inserting are coming fromy my innocent pet generator function so I
    know they are not malicious, and I simply want this example script
    to execute as quickly as possible and not use too much memory.
    */
        global $wpdb;
        $wpdb->query($query);
    }
}

$petFilter = new PetFilter();
