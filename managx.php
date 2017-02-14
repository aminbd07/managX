<?php

/*
 * Plugin Name: ManagX
 * Version: 0.1 Beta
 * Plugin URI: https://wordpress.org/plugins/wp-managx
 * Description: An ultimate  project manager plugin for wprdpress. 
 * Author: Nurul Amin & Managx Team
 * Author URI: http://managx.com
 * Requires at least: 4.0        
 * Tested up to: 4.1.1
 * License: GPL2
 * Text Domain: managx
 * Domain Path: /lang/
 *
 */

class Managx {

    public $version = '0.1 Beta';
    public $db_version = '1.0';
    protected static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct() {

        $this->init_actions();

        $this->define_constants();
        spl_autoload_register(array($this, 'autoload'));
        // Include required files


        register_activation_hook(__FILE__, array($this, 'install'));
        //Do some thing after load this plugin

        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

        do_action('managx_loaded');
    }

    function install() {
        
    }

    function init_actions() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    function autoload($class) {
        $name = explode('_', $class);
        if (isset($name[1])) {
            $class_name = strtolower($name[1]);
            $filename = dirname(__FILE__) . '/class/' . $class_name . '.php';
            if (file_exists($filename)) {
                require_once $filename;
            }
        }
    }

    public function define_constants() {

        $this->define('MANAGX_VERSION', $this->version);
        $this->define('MANAGX_DB_VERSION', $this->db_version);
        $this->define('MANAGX_PATH', plugin_dir_path(__FILE__));
        $this->define('MANAGX_URL', plugins_url('', __FILE__));
    }

    public function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    function load_textdomain() {
        load_plugin_textdomain('managx', false, dirname(plugin_basename(__FILE__)) . '/lang/');
    }

    static function admin_scripts() {

        wp_enqueue_script('jquery');
        wp_enqueue_script('underscore');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('managx_admin', plugins_url('assets/js/admin.js', __FILE__), '', false, true);
        wp_localize_script('managx_admin', 'Managx_Vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('managx_nonce'),
            'pluginURL' => MANAGX_URL,
        ));

        wp_enqueue_style('managx_admin', plugins_url('/assets/css/style.css', __FILE__));

        wp_enqueue_style('dashicons');
        do_action('managx_admin_scripts');
    }

    function admin_menu() {
        $capability = 'read'; //minimum level: subscriber

        $hook = add_menu_page(__('ManagX Project Manager', 'managx'), __('ManagX Project Manager', 'managx'), $capability, 'managx', array($this, 'page_handler'), 'dashicons-schedule', 3);
        
        add_action('admin_print_styles-' . $hook, array($this, 'admin_scripts'));

        do_action('managx_admin_menu', $capability, $this);
    }
    
    function page_handler(){
        echo "<div id='managx'> 
            <h1>ManagX :: Project Manager for WP </h1>
         </div>";
        
    }

}

function managx() {
    return Managx::instance();
}

//Managx instance.
$managx = managx();
