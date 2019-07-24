<?php

/**
 * Short Code Login Button
 */
function sso_login_button_shortcode($atts)
{
    $a = shortcode_atts(array(
        'title' => 'Login using Single Sign On',
        'class' => 'button button-primary button-large sso-login-button',
        'target' => '_self',
        'text' => __('Login With Pod')
    ), $atts);

    return
        '<a class="' . $a['class'] . '" href="' . site_url('?pod-sso') . '" title="' . $a['title'] . '" target="' . $a['target'] . '">
            ' . $a['text'] . '
        </a>';
}
add_shortcode('sso_login_button', 'sso_login_button_shortcode');
