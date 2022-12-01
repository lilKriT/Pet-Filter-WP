<?php

get_header(); ?>

<div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/ocean.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title">Pet Adoption</h1>
        <div class="page-banner__intro">
            <p>Providing forever homes one search at a time.</p>
        </div>
    </div>
</div>

<div class="container container--narrow page-section">

    <p>This page took <strong><?php echo timer_stop(); ?></strong> seconds to prepare. Found <strong>x</strong> results (showing the first x).</p>

    <?php
    global $wpdb;
    $tableName = $wpdb->prefix . "pets";
    $petQuery = $wpdb->prepare("SELECT * FROM $tableName LIMIT 100", array());
    $pets = $wpdb->get_results($petQuery);
    // var_dump($pets);
    ?>

    <table class="pet-adoption-table">
        <tr>
            <th>Name</th>
            <th>Species</th>
            <th>Weight</th>
            <th>Birth Year</th>
            <th>Hobby</th>
            <th>Favorite Color</th>
            <th>Favorite Food</th>
        </tr>
        <tr>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        </tr>
    </table>

</div>

<?php get_footer(); ?>