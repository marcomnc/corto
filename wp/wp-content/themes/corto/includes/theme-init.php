<?php

/* 5 Events */
function my_post_type_event()
{
    register_post_type('event', array(
        'label' => __('Events'),
        'singular_label' => __('Event', 'corto'),
        '_builtin' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_nav_menus' => false,
        'hierarchical' => false,
        'capability_type' => 'page',
        'menu_position' => 5,
        'rewrite' => array(
            'slug' => 'events',
            'with_front' => FALSE,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields')
            )
    );
}

/* 6 Press cuttings */
function my_post_type_press()
{
    register_post_type('press', array(
        'label' => __('Press Cuttings'),
        'singular_label' => __('Press Item', 'corto'),
        '_builtin' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_nav_menus' => false,
        'hierarchical' => false,
        'capability_type' => 'page',
        'menu_position' => 6,
        'rewrite' => array(
            'slug' => 'press-cuttings',
            'with_front' => FALSE,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields')
            )
    );
}

/* 7 Media */
function my_post_type_media()
{
    register_post_type('media', array(
        'label' => __('Reserved Media'),
        'singular_label' => __('Media Item', 'corto'),
        '_builtin' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_nav_menus' => false,
        'hierarchical' => false,
        'capability_type' => 'page',
        'menu_position' => 7,
        'rewrite' => array(
            'slug' => 'media',
            'with_front' => FALSE,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields')
            )
    );
}

/* 8 Releases */
function my_post_type_releases()
{
    register_post_type('releases', array(
        'label' => __('Press releases'),
        'singular_label' => __('Release', 'corto'),
        '_builtin' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_nav_menus' => false,
        'hierarchical' => false,
        'capability_type' => 'page',
        'menu_position' => 8,
        
        'rewrite' => array(
            'slug' => 'release',
            'with_front' => false,
        ),
        
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt')
            )
    );
}

/* 9 Collections */
function my_post_type_collections()
{
    register_post_type('collections', array(
        'label' => __('Collections'),
        'singular_label' => __('Collection', 'corto'),
        '_builtin' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_nav_menus' => false,
        'hierarchical' => false,
        'capability_type' => 'page',
        'menu_position' => 7,
        'rewrite' => array(
            'slug' => 'collections',
            'with_front' => FALSE,
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt')
            )
    );
}

add_action('init', 'my_post_type_event');
add_action('init', 'my_post_type_press');
add_action('init', 'my_post_type_media');
add_action('init', 'my_post_type_releases');
add_action('init', 'my_post_type_collections');


function my_rewrite_flush()
{
//flush_rewrite_rules();
}

add_action( 'after_switch_theme', 'my_rewrite_flush' );

add_rewrite_rule('press\-releases/page/([0-9]+)/?$', 'page/$matches[1]/index.php?pagename=press\-releases', 'top');
add_rewrite_rule('press\-cuttings/page/([0-9]+)/?$', 'page/$matches[1]/index.php?pagename=press\-cuttings', 'top');
add_rewrite_rule('release/([^/]*)/?$', 'index.php?releases=$matches[1]', 'top');
add_rewrite_rule('collections/([^/]*)/?$', 'index.php?collections=$matches[1]', 'top');
add_rewrite_rule('topics/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$','index.php?category_name=$matches[1]&feed=$matches[2]', 'top');
add_rewrite_rule('topics/(.+?)/(feed|rdf|rss|rss2|atom)/?$', 'index.php?category_name=$matches[1]&feed=$matches[2]', 'top');
add_rewrite_rule('topics/(.+?)/page/?([0-9]{1,})/?$', 'index.php?category_name=$matches[1]&paged=$matches[2]', 'top');
add_rewrite_rule('topics/(.+?)/?$', 'index.php?category_name=$matches[1]', 'top');
add_rewrite_rule('topics/([^/]*)/?$', 'index.php?category_name=$matches[1]', 'top');

flush_rewrite_rules();

?>
