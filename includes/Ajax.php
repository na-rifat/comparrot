<?php

namespace Comparrot;

use Comparrot\Processor\Processor;

/**
 * Handles ajax requests
 */
class Ajax {
    use \Comparrot\Traits\File;
    /**
     * Useful variables
     */
    function __construct() {

    }

    /**
     * Initializes the class
     *
     * @return void
     */
    function init() {
        $this->register();
    }

    /**
     * Registers ajax requests
     *
     * @return void
     */
    public function register() {
        comparrot_ajax( 'comparrot_upload_file', [$this, 'upload_files'] );
        comparrot_ajax( 'save_toggle_value', [$this, 'save_toggle_value'] );
        comparrot_ajax( 'download_csv_template', [$this, 'download_csv_template'] );
        comparrot_ajax( 'save_logo_url', [$this, 'save_logo_url'] );
        comparrot_ajax( 'compt_reset_theme', [$this, 'compt_reset_theme'] );

        // New
        comparrot_ajax( 'comparrot_save_settings', [$this, 'comparrot_save_settings'] );
        comparrot_ajax( 'regenerate_templates', [$this, 'regenerate_templates'] );
        comparrot_ajax( 'regenerate_pages', [$this, 'regenerate_pages'] );

    }

    public function upload_files() {
        $csv_files   = [];
        $image_files = [];
        $csv         = new \Comparrot\Schema\CSV();

        // Nonce verification
        if ( ! wp_verify_nonce( comparrot_var( 'nonce' ), 'comparrot_upload_file' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid nonce!', 'comparrot' ),
                ]
            );
            exit;
        }

        // Collecting data from post
        $image_files = self::get_files_by_ext( $_FILES['all_files'], ['jpg', 'jpeg', 'bmp', 'png'] );
        $image_files = self::make_image_key( $image_files );

        // Merging CSV fields
        $csv_file = self::get_files_by_ext( $_FILES['all_files'], ['csv'] );

        if ( sizeof( $csv_file ) == 0 ) {
            \Comparrot\Processor\Processor::attach_non_csv_images( [], $image_files );
            \Comparrot\Processor\Processor::json_status(
                [
                    'success' => true,
                    'msg'     => __( 'No CSV file were found, uploaded images has been inserted.', 'comparrot' ),
                ]
            );
            exit;
        }

        if ( empty( $csv_file ) ) {
            \Comparrot\Processor\Processor::json_status(
                [
                    'success' => false,
                    'msg'     => __( 'No CSV file found!', 'comparrot' ),
                ]
            );
            exit;
        }

        $csv_data = $csv::file_to_array( $csv_file[0] );

        \Comparrot\Processor\Processor::begin_inserting_pages( $csv_data, $image_files );

    }

    /**
     * Handles CSV template download request
     *
     * @return void
     */
    public function download_csv_template() {
        // Nonce verification
        if ( ! wp_verify_nonce( comparrot_var( 'nonce' ), 'download_csv_template' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid nonce!', 'comparrot' ),
                ]
            );
            exit;
        }

        wp_send_json_success(
            [
                'msg' => __( 'File is ready to download.', 'comparrot' ),
                'url' => \Comparrot\Schema\CSV::create_template( 'csv_fields' ),
            ]
        );
        exit;
    }

    /**
     * Reset theme settings in Schema1
     *
     * @return void
     */
    public function compt_reset_theme() {

        if ( ! wp_verify_nonce( comparrot_var( 'nonce' ), 'compt_reset_theme' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid nonce!', 'comparrot' ),
                ]
            );
            exit;
        }

        \Comparrot\Schema\Schema::reset_all_settings();

        wp_send_json_success(
            [
                'msg' => __( 'Your theme reset operation succeeded', 'comparrot' ),
            ]
        );
        exit;

    }

    /**
     * Stores logo url to the database
     *
     * @return void
     */
    public function save_logo_url() {
        $url = comparrot_var( 'url' );

        update_option( 'compt-logo', $url );

        wp_send_json_success(
            [
                'msg' => __( 'Logo uploaded succesfully', 'comparrot' ),
            ]
        );
        exit;
    }

    /**
     * Stores toggle value
     *
     * @return void
     */
    public function save_toggle_value() {
        $atts = [
            'nonce' => comparrot_var( 'nonce' ),
            'key'   => comparrot_var( 'key' ),
            'value' => comparrot_var( 'value' ),
        ];

        if ( ! wp_verify_nonce( $atts['nonce'], 'save_toggle_value' ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Invalid nonce', 'comparrot' ),
                ]
            );
            exit;
        }

        update_option( $atts['key'], $atts['value'] );

        wp_send_json_success(
            [
                'message' => __( 'Key settings saved sucessfully', 'comparrot' ),
            ]
        );
        exit;
    }

    /**
     * Stores a schema settings from frontend request to the database
     *
     * @return void
     */
    public function comparrot_save_settings() {
        // Nonce check

        if ( ! wp_verify_nonce( comparrot_var( 'nonce' ), 'comparrot_save_settings' ) ) {
            wp_send_json_error(

                [
                    'msg' => __( 'Invalid nonce!', 'comparrot' ),
                ]
            );
            exit;
        }

        $schema  = new \Comparrot\Schema\Schema();
        $current = $schema::set_settings( comparrot_var( 'form' ),
            $schema::merge_schema_values(
                $schema::get_posted_settings_data(
                    comparrot_var( 'form' )
                ),
                $schema::get_settings( comparrot_var( 'form' ) )
            )
        );

        self::additional_settings( comparrot_var( 'form' ),
            $schema::merge_schema_values(
                $schema::get_posted_settings_data(
                    comparrot_var( 'form' )
                ),
                $schema::get_settings( comparrot_var( 'form' ) )
            )
        );

        wp_send_json_success(
            [
                'data' => $current,
                'msg'  => __( 'Settings saved successfully!', 'comparrot' ),
            ]
        );exit;

    }

    public static function additional_settings( $name, $schema ) {
        switch ( $name ) {
            case 'header':
                update_option( 'blogname', $schema['site-title']['value'] );
                break;
        }
    }

    public function regenerate_templates() {
        if ( ! wp_verify_nonce( comparrot_var( 'nonce' ), 'regenerate_templates' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid token!', 'comparrot' ),
                ]
            );
            exit;
        }

        Processor::move_merge_template();

        wp_send_json_success(
            [
                'msg' => __( 'Successfully regenerated templates', 'comparrot' ),
            ]
        );
        exit;

    }

    public function regenerate_pages() {
        if ( ! wp_verify_nonce( comparrot_var( 'nonce' ), 'regenerate_pages' ) ) {
            wp_send_json_error(
                [
                    'msg' => __( 'Invalid token!', 'comparrot' ),
                ]
            );exit;
        }

        $files     = scandir( CMP_DV_PATH );
        $csv_files = [];
        $img_files = [];
        $csv       = new \Comparrot\Schema\CSV();

        // Filter csv files
        foreach ( $csv_files as $file ) {
            $ext = self::file_ext( $file );
            if ( $ext === 'csv' ) {
                $csv_files[] = $file;
            }
        }

        // Get data
        $csv_data = [];
        $file     = fopen( CMP_DV_PATH . $csv_files[0], 'r' );

        while (  ( $line = fgetcsv( $file ) ) !== FALSE ) {
            $csv_data[] = $line;
        }

        fclose( $file );

        // Remove older pages
        foreach ( $csv_data as $page ) {
            $existed = get_page_by_title( $page['PageTitle'] );
            if ( ! $existed !== null ) {
                wp_delete_post( $existed->ID );
            }
        }

        Processor::begin_inserting_pages( $csv_data, [] );

        wp_send_json_success(
            [
                'msg' => __( 'Development pages regenerated' ),
            ]
        );
        exit;
    }

}
