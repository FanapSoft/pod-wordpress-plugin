<?php

/**
 * Load Style
 */
function wpsso_public_scripts()
{
    wp_enqueue_style('sso-public-css', POD_SSO_DIR_URL . '/public/assets/css/style.css');
}
add_action('login_enqueue_scripts', 'wpsso_public_scripts');

/**
 * Add SSO Link-Button To Loging Form
 */
function add_sso_button_to_login_form()
{
    echo do_shortcode('[sso_login_button]');
}
add_action('login_form', 'add_sso_button_to_login_form');
