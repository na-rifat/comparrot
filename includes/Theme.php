<?php

namespace Comparrot;

class Theme {

    function __construct() {
        $schema             = new \Comparrot\Schema\Schema();
        $this->templates    = new Templates();
        $this->style_schema = $schema::get_settings( 'style' );

        $this->init();
        $this->do_actions();
    }

    /**
     * Initializes the functions
     *
     * @return void
     */
    public function init() {

    }

    /**
     * Does necessary actions
     *
     * @return void
     */
    public function do_actions() {
        add_action( 'compt_header', [$this->templates, 'import'] );
        add_action( 'compt_footer', [$this->templates, 'import'] );
        add_action( 'compt_toc', [$this->templates, 'import'] );
        add_action( 'compt-templates', [$this->templates, 'import'] );

        add_filter( 'theme_page_templates', [$this, 'theme_templates'] );
        add_action( 'wp_head', [$this, 'hide_breadcrumb'] );

        add_action( 'wp_head', [$this, 'render_breadcrumb_bg'] );

        add_action( 'wp_head', [$this, 'load_meta'], 999 );

        add_filter( 'page_template', [$this, 'wpa3396_page_template'] );
        add_filter( 'wp_enqueue_scripts', [$this, 'add_template_files'] );
    }

    /**
     * Load meta data
     *
     * @return void
     */
    public function load_meta() {
        $meta = get_post_meta( get_the_ID(), 'comparrot-meta', true );

        echo $meta == false ? '' : $meta;
    }

    public function render_breadcrumb_bg() {
        echo sprintf(
            '<style>
            .compt-page-identity{ %s:url(%s) }
            .compt-cta-background-image{%s:url(%s}
            </style>',
            'background-image',
            COMPARROT_URL . '/assets/img/breadcrumb-bg.jpg',
            'background-image',
            COMPARROT_URL . '/assets/img/widget-bg.jpg'
        );
    }

    /**
     * Registers template options in edit page section
     *
     * @param  array   $templates
     * @return array
     */
    public function theme_templates( $templates ) {

        $my_virtual_templates = array(
            'theme'     => 'Theme',
            'comparrot' => 'Comparrot',
            'comparrot_canvas' => 'Comparrot canvas',
        );

        // Merge with any templates already available
        $templates = array_merge( $templates, $my_virtual_templates );

        return $templates;
    }

    public function wpa3396_page_template( $page_template ) {
        global $post;
        $template = get_post_meta( $post->ID, '_wp_page_template', true );

        if ( $template == 'comparrot' ) {
            $page_template = COMPARROT_TEMPLATES_PATH . '/page.php';
        } elseif ( $template == 'comparrot_canvas' ) {
            $page_template = COMPARROT_TEMPLATES_PATH . '/page-canvas.php';
        }
        // exit(var_dump($page_template));

        return $page_template;
    }

    public function hide_breadcrumb() {
        global $post;
        $page_template = get_post_meta( $post->ID, '_wp_page_template', true );

        if ( ! $page_template == 'comparrot' ) {
            return;
        }
        echo '<style>#site-title{display: none;}</style>';
    }

    public function add_template_files() {
        global $post;

        $template          = get_post_meta( $post->ID, '_wp_comparrot_template', true );
        $template_path     = ABSPATH . '/wp-content/html-templates/';
        $template_name     = explode( '.', $template )[0];
        $templates_url     = site_url( '/wp-content/html-templates/' );
        $stylesheet_path   = $template_path . $template_name . '.css';
        $script_path       = $template_path . $template_name . '.js';
        $stylesheet        = $templates_url . $template_name . '.css';
        $script            = $templates_url . $template_name . '.js';
        $stylesheet_handle = $template_name . '-styles';
        $script_handle     = $template_name . '-scripts';

        if ( file_exists( $stylesheet_path ) ) {
            wp_enqueue_style( $stylesheet_handle, $stylesheet, [], filemtime( $stylesheet_path ) );
        }
        if ( file_exists( $script_path ) ) {
            wp_enqueue_script( $script_handle, $script, ['jquery'], filemtime( $script_path ) );
        }

    }

}