<?php
/*
Plugin Name:  Single Sign On - POD SSO
Plugin URI: http://docs.pod.land
Description: افزونه ورود یکپارچه پاد این امکان را به شما می دهد تا بتوانید ورود و احراز هویت پاد را در سایت وردپرسی خود داشته باشید.
Version: 1.0.0
Author: podsdk
Author URI: http://fanap.ir
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined('ABSPATH') or exit;

define('POD_SSO_VERSION', '1.0.0');

define('POD_SSO_FILE', __FILE__);

define('POD_SSO_DIR', untrailingslashit(dirname(POD_SSO_FILE)));
define('POD_SSO_DIR_ADMIN', POD_SSO_DIR . '/admin');
define('POD_SSO_DIR_PUBLIC', POD_SSO_DIR . '/public');
define('POD_SSO_DIR_SDK', POD_SSO_DIR . '/pod_sdk');

define('POD_SSO_DIR_URL', plugin_dir_url(__FILE__));

define('POD_SSO_MODULES_DIR', POD_SSO_DIR . '/modules');

if (is_admin()) {
    require_once(POD_SSO_DIR_ADMIN . '/admin.php');
} else {
    require_once(POD_SSO_DIR_PUBLIC . '/shortcodes.php');
    require_once(POD_SSO_DIR_PUBLIC . '/functions.php');

    function template_redirect_init()
    {
        if (isset($_GET['pod-sso'])) {
            require_once(POD_SSO_DIR_PUBLIC  . '/init.php');
        }
    }
    add_action('template_redirect',  'template_redirect_init');
}
