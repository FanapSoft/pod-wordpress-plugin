<?php
defined('ABSPATH') or exit;

function pod_sso_plugin_unistall()
{
    delete_option('pod_options');
}

pod_sso_plugin_unistall();
