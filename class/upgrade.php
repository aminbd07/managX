<?php

class MANAGX_Upgrade {

    private static $_instance;

    /**
     * Instantiate
     *
     * @since 0.1
     *
     * @return type
     */
    public static function getInstance() {
        if ( ! self::$_instance ) {
            self::$_instance = new MANAGX_Upgrade();
        }

        return self::$_instance;
    }

    /**
     * Initial action
     *
     * @since 0.1
     *
     * @return type
     */
    function __construct() {
        add_action( 'admin_init', array( $this, 'init' ) );
        add_action( 'admin_notices', array( $this, 'notice' ) );
    }

    /**
     * Plugin notice
     *
     * @since 1.1
     */
    function notice() {
        $version = get_option( 'managx_version', '0.0' );

        if ( version_compare( MANAGX_VERSION, $version, '<=' ) ) {
            return;
        }
        ?>
        <div class="notice notice-warning">
            <p><?php _e( '<strong>MANAGX  Data Update Required</strong> &#8211; Please click the button below to update to the latest version.', 'managx' ) ?></p>

            <form action="" method="post" style="padding-bottom: 10px;">
                <?php wp_nonce_field( '_nonce', 'managx_nonce' ); ?>
                <input type="submit" class="button button-primary" name="managx_update" value="<?php _e( 'Run the Update', 'managx' ); ?>">
            </form>
        </div>
        <?php
    }

    /**
     * Initial action
     *
     * @since 0.1
     */
    function init() {
        if ( ! isset( $_POST['managx_update'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['managx_nonce'], '_nonce' ) ) {
            return;
        }

        $this->plugin_upgrades();
        wp_redirect( $_POST['_wp_http_referer'] );
        exit();
    }

    

}
