<?php

namespace Comparrot;

/**
 * Registers essential assets
 */
class Assets {
    /**
     * Construct assets class
     */
    function __construct() {
        add_action( 'wp_enqueue_scripts', [$this, 'register'] );
        add_action( 'admin_enqueue_scripts', [$this, 'register'] );
        add_action( 'wp_enqueue_scripts', [$this, 'load'] );
        add_action( 'admin_enqueue_scripts', [$this, 'load'] );
    }

    /**
     * Return scripts from array
     *
     * @return array
     */
    public function get_scripts() {
        return [
            'comparrot-admin-script'    => comparrot_jsfile( 'admin', ['jquery'] ),
            'comparrot-toc-script'      =>
            [
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/tocbot/4.11.1/tocbot.min.js',
                'version' => '4.11.1',
                'deps'    => ['jquery'],
            ],
            'comparrot-frontend-script' => comparrot_jsfile( 'script', ['jquery', 'comparrot-toc-script'] ),
            'cmp-owl-carousel'          =>
            [
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js',
                'version' => '1.3.3',
                'deps'    => ['jquery'],
            ],
        ];
    }

    /**
     * Return styles from array
     *
     * @return array
     */
    public function get_styles() {
        return [
            'comparrot-admin-styles'   => comparrot_cssfile( 'admin' ),
            'comparrot-fontawesome'    => [
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css',
                'version' => '5.15.2',
            ],
            'comparrot-frontend-style' => comparrot_cssfile( 'stylesheet' ),
            'comparrot-self-strap'     => comparrot_cssfile( 'self_strap' ),
            'comparrot-toc-style'      => [
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/tocbot/4.11.1/tocbot.css',
                'version' => '4.1.1',
            ],
            'cmp-owl-carousel'         => [
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css',
                'version' => '1.3.3',
            ],
            'cmp-owl-theme'            => [
                'src'     => 'https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css',
                'version' => '1.3.3',
            ],
        ];
    }

    /**
     * Return localize variable from array
     *
     * @return array
     */
    public function get_localize() {
        global $post;
        return [
            'comparrot-admin-script'    => [
                'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                'save_toggle_value_nonce'       => wp_create_nonce( 'save_toggle_value' ),
                'comparrot_upload_file_nonce'   => wp_create_nonce( 'comparrot_upload_file' ),
                'went_wrong'                    => __( 'Something went wrong!', 'comparrot' ),
                'compt_reset_theme_nonce'       => wp_create_nonce( 'compt_reset_theme' ),
                'comparrot_save_settings_nonce' => wp_create_nonce( 'comparrot_save_settings' ),
                'download_csv_template_nonce'   => wp_create_nonce( 'download_csv_template' ),
                'regenerate_templates_nonce'    => wp_create_nonce( 'regenerate_templates' ),
                'regenerate_pages_nonce'        => wp_create_nonce( 'regenerate_pages' ),
            ],
            'comparrot-frontend-script' => [
                'rcs'      => CMP_RESOURCE_URL,
                'site_url' => site_url(),
            ],
        ];
    }

    /**
     * Registers scripts, styles and localize variables
     *
     * @return void
     */
    public function register() {
        // Scripts
        $scripts = $this->get_scripts();

        foreach ( $scripts as $handle => $script ) {
            $deps = isset( $script['deps'] ) ? $script['deps'] : false;

            wp_register_script( $handle, $script['src'], $deps, ! empty( $script['version'] ) ? $script['version'] : false, true );

        }

        // Styles
        $styles = $this->get_styles();

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, ! empty( $style['version'] ) ? $style['version'] : false );
        }

        // Localization
        $localize = $this->get_localize();

        foreach ( $localize as $handle => $vars ) {
            wp_localize_script( $handle, 'comparrot', $vars );
        }
    }

    /**
     * Loads the scripts to frontend
     *
     * @return void
     */
    public function load() {
        if ( is_admin() ) {
            wp_enqueue_style( 'comparrot-admin-styles' );
            wp_enqueue_script( 'comparrot-admin-script' );
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_media();
            if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'comparrot' || $_GET['page'] == 'comparrot-general-settings' ) ) {
                wp_enqueue_style( 'comparrot-fontawesome' );
            }
        } else {
            wp_enqueue_style( 'comparrot-fontawesome' );
            wp_enqueue_script( 'comparrot-frontend-script' );
            wp_enqueue_script( 'comparrot-toc-script' );
            wp_enqueue_script( 'cmp-owl-carousel' );
            wp_enqueue_style( 'comparrot-toc-style' );
            wp_enqueue_style( 'comparrot-frontend-style' );
            // wp_enqueue_style( 'comparrot-self-strap' );
            wp_enqueue_style( 'cmp-owl-carousel' );
            wp_enqueue_style( 'cmp-owl-theme' );
        }

        if ( is_admin() && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
            wp_enqueue_style( 'comparrot-fontawesome' );
            wp_enqueue_script( 'comparrot-frontend-script' );
            wp_enqueue_style( 'comparrot-frontend-style' );
            // wp_enqueue_style( 'comparrot-self-strap' );
        }
    }
}