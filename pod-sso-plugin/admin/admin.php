<?php
namespace SSO;

defined('ABSPATH') or die('UnAuthorized Access!');


class WPOSSO_Admin
{
    protected $option_name = 'pod_options';
    protected $urlAdmin = POD_SSO_DIR_URL . 'admin/';
    protected $dirAdmin = POD_SSO_DIR_ADMIN;

    public static function init()
    {
        add_action("admin_enqueue_scripts", array(new self, 'wpsso_enqueue_script'));
        add_action('admin_init', array(new self, 'admin_init'));
        add_action('admin_menu', array(new self, 'add_menu_pod'));
    }

    /**
     * Register && Enqueue Style
     */
    public function wpsso_enqueue_script()
    {
        wp_register_style('wposso_admin',  $this->urlAdmin . '/assets/css/admin.css');
        wp_enqueue_style('wposso_admin');
    }

    /**
     * Register a setting Wordpress
     */
    public function admin_init()
    {
        register_setting('pod_options', $this->option_name);
    }

    /**
     * Add Menu to Admin Page
     */
    public function add_menu_pod()
    {
        $page = array(
            'page_title' => 'تنظیمات احراز هویت پاد',
            'menu_title' => 'POD SSO',
            'capability' => 'manage_options',
            'menu_slug' => 'pod_settings2',
            'callback' => array($this, 'html_page_form'),
            'icon_url' => "{$this->urlAdmin}assets/images/logo.svg",
            'position' => 80
        );
        add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
    }

    /**
     * HTML Page
     */
    public function html_page_form()
    {
        $options = get_option($this->option_name); // use variable in page-form.php (Don't Delete this variable)
        require_once("{$this->dirAdmin}/page-form.php");
    }
}

WPOSSO_Admin::init();
