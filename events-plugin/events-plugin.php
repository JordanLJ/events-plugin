<?php

/*
Plugin Name: Events Plugin
Plugin URI: http://yourwebsite.com
Description: Adds custom post type 'Events' to the WordPress admin dashboard with details like title, description, date, and location, and displays upcoming events in a list format using shortcode and sidebar widget.
Version: 1.0
Author: Your Name
Author URI: http://yourwebsite.com/
License: GPL2
*/


function events_post_type() {
    $labels = array(
        'name' => __('Events'),
        'singular_name' => __('Event'),
        'add_new' => __('Add New'),
        'add_new_item' => __('Add New Event'),
        'edit_item' => __('Edit Event'),
        'new_item' => __('New Event'),
        'view_item' => __('View Event'),
        'search_items' => __('Search Events'),
        'not_found' => __('No Events found'),
        'not_found_in_trash' => __('No Events found in Trash')
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'events'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields')
    );
    register_post_type('events', $args);
}

add_action('init', 'events_post_type');



function events_widget() {
    $args = array(
        'post_type' => 'events',
        'posts_per_page' => 5,
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'event_date',
                'value' => date('Ymd'),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            $date = get_field('event_date');
            $location = get_field('event_location');
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> - ' . $date . ' - ' . $location . '</li>';
        }
        echo '</ul>';
    }
    wp_reset_postdata();
}

function events_widget_init() {
    register_sidebar_widget('Events', 'events_widget');
}

add_action('widgets_init', 'events_widget_init');


function events_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => '5',
    ), $atts);
    $output = '';
    $args = array(
        'post_type' => 'events',
        'posts_per_page' => $atts['count'],
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'event_date',
                'value' => date('Ymd'),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $output .= '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            $date = get_field('event_date');
            $location = get_field('event_location');
            $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> - ' . $date . ' - ' . $location . '</li>';
        }
        $output .= '</ul>';
    }
    wp_reset_postdata();
    return $output;
}

add_shortcode('events', 'events_shortcode');



?>