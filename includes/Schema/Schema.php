<?php
namespace Comparrot\Schema;

use Comparrot\Comparrot;

class Schema {
    use \Comparrot\Traits\Input;
    use \Comparrot\Traits\Helper;

    const prefix     = Comparrot::domain;
    const data_types = [
        's' => ['text', 'longtext', 'varchar'],
        'd' => ['number', 'longint', 'int', 'integer'],
        'f' => ['float', 'double'],
    ];
    const checkbox_off = 'No';
    const __           = Comparrot::domain;

    function __construct() {
        $this->register();

    }

    /**
     * Builds the initial schemas
     *
     * @return void
     */
    public function register() {
        $domain        = self::__;
        $this->log_col = [
            'page_title'   => __( 'Title', $domain ),
            'author_name'  => __( 'Author', $domain ),
            'publish_date' => __( 'Publish date', $domain ),
        ];

        // Core fields started
        $this->csv_fields_core = [
            'page_title'   => 'post_title',
            'page_content' => 'post_content',
        ];
        // Core fields ended

        // Reverse csv fields started
        $this->csv_fields_reverse = [
            'page_title'   => 'post_title',
            'page_content' => 'post_content',
        ];
        // Reverse csv feilds endede

        // Meta fields
        $this->meta_fields = [
            'MetaTitle',
            'MetaDescription',
            'MetaRobots',
            'Meta_og:locale',
            'Meta_og:type',
            'Meta_og:url',
            'Meta_og:site_name',
            'Meta_og:article:modified_time',
            'Meta_og:image',
        ];
        // Meta fields ended

        // CSV fields started
        $this->csv_fields = [
            'breadcrumb'                 => [
                'label' => __( 'Breadcrumb', $domain ),
            ],
            'page_title'                 => [
                'label' => __( 'PageTitle', $domain ),
            ],
            'author_name'                => [
                'label' => __( 'AuthorName', $domain ),
            ],
            'publish_date'               => [
                'label' => __( 'PublishDate', $domain ),
            ],
            'prefill_small_widget'       => [
                'label' => __( 'PrefillSmallWidget', $domain ),
                'type'  => 'textarea',
            ],
            'use_toc_widget'             => [
                'label' => __( 'UseTocWidget', $domain ),
            ],
            'page_intro'                 => [
                'label' => __( 'PageIntro', $domain ),
            ],
            'featured_image'             => [
                'label' => __( 'FeaturedImage', $domain ),
            ],
            'page_content'               => [
                'label' => __( 'PageContent', $domain ),
            ],
            'call_to_action_button_text' => [
                'label' => __( 'CallToActionButtonText', $domain ),
            ],
            'call_to_action_button_link' => [
                'label' => __( 'CallToActionButtonLink', $domain ),
            ],
            'share_icons_title'          => [
                'label' => __( 'ShareIconsTitle', $domain ),
            ],
            'author_picture'             => [
                'label' => __( 'AuthorPicture', $domain ),
            ],
            'author_name'                => [
                'label' => __( 'AuthorName', $domain ),
            ],
            'author_role'                => [
                'label' => __( 'AuthorRole', $domain ),
            ],
            'author_description'         => [
                'label' => __( 'AuthorDescription', $domain ),
            ],
            'author_facebook'            => [
                'label' => __( 'AuthorFacebook', $domain ),
            ],
            'author_twitter'             => [
                'label' => __( 'AuthorTwitter', $domain ),
            ],
            'author_linkedin'            => [
                'label' => __( 'AuthorLinkedin', $domain ),
            ],
            'author_rating_title'        => [
                'label' => __( 'AuthorRatingTitle', $domain ),
            ],
            'author_rating_description'  => [
                'label' => __( 'AuthorRatingDescription', $domain ),
            ],
            'author_rating_badge_text'   => [
                'label' => __( 'AuthorRatingBadgeText', $domain ),
            ],
            'cta_full_width_title'       => [
                'label' => __( 'CTAFullWidthTitle', $domain ),
                'type'  => 'textarea',
            ],
            'prefill_full_width_widget'  => [
                'label' => __( 'PrefillFullWidthWidget', $domain ),
                'type'  => 'textarea',
            ],
            'interlink_title'            => [
                'label' => __( 'InterlinkTitle', $domain ),
            ],
            'interlink_sub_title_box1'   => [
                'label' => __( 'InterlinkSubTitleBox1', $domain ),
            ],
            'interlink_sub_title_box2'   => [
                'label' => __( 'InterlinkSubTitleBox2', $domain ),
            ],
            'interlink_sub_title_box3'   => [
                'label' => __( 'InterlinkSubTitleBox3', $domain ),
            ],
            'interlink_box_1'            => [
                'label' => __( 'InterlinkBox1', $domain ),
                'type'  => 'textarea',
            ],
            'interlink_box_2'            => [
                'label' => __( 'InterlinkBox2', $domain ),
                'type'  => 'textarea',
            ],
            'interlink_box_3'            => [
                'label' => __( 'InterlinkBox3', $domain ),
                'type'  => 'textarea',
            ],
            'template'                   => [
                'label' => __( 'Template', $domain ),
            ],
            'featured_image_alt_text'    => [
                'label' => __( 'FeaturedImageAltText', $domain ),
            ],
        ];
        // CSV fields ended

        // Settings started
        $this->settings = [
            'style'  => [
                'transition' => [
                    'label' => __( 'Transition', $domain ),
                    'type'  => 'text',
                    'value' => 'all .3s linear',
                ],
            ],
            'header' => [
                'site-title'             => [
                    'label' => __( 'Site title', $domain ),
                    'type'  => 'text',
                    'value' => get_option( 'blogname', $domain ),
                    'class' => ['regular-text'],
                ],
                'date-format'            => [
                    'label'   => __( 'Date format', $domain ),
                    'type'    => 'select',
                    'value'   => 'd-n-Y H:i',
                    'options' => [
                        'd-n-Y H:i'        => 'd-n-Y H:i',
                        'dd-MM-yyyy hh:mm' => 'dd-MM-yyyy hh:mm',
                        'l, F j, Y'        => 'l, F j, Y',
                        'F j, Y g:i a'     => 'F j, Y g:i a',
                        'F j, Y'           => 'F j, Y',
                        'F, Y'             => 'F, Y',
                        'g:i a'            => 'g:i a',
                        'g:i:s a'          => 'g:i:s a',
                        'l, F jS, Y'       => 'l, F jS, Y',
                        'M j, Y @ G:i'     => 'M j, Y @ G:i',
                        'Y/m/d \a\t g:i A' => 'Y/m/d \a\t g:i A',
                        'Y/m/d \a\t g:ia'  => 'Y/m/d \a\t g:ia',
                        'Y/m/d g:i:s A'    => 'Y/m/d g:i:s A',
                        'Y/m/d'            => 'Y/m/d',
                    ],
                    'class'   => ['regular-text'],
                ],
                'seo-textwriter-api-key' => [
                    'label' => __( 'SEO TextWriter API key', $domain ),
                    'type'  => 'text',
                    'value' => '',
                    'class' => ['regular-text'],
                ],
            ],
        ];

        // Settings scheam ended

        // Page layout settings
        $this->page_layout_settings = [
            'layout'     => [
                'label'   => __( 'Layout', $domain ),
                'type'    => 'select',
                'value'   => 'full_width',
                'options' => [
                    'full_width' => __( 'Full width', $domain ),
                    'container'  => __( 'Container', $domain ),
                ],
                'class'   => ['regular-text'],
            ],
            'breadcrumb' => [
                'label'   => __( 'Breadcrumb', $domain ),
                'type'    => 'select',
                'value'   => 'enable',
                'options' => [
                    'enable'  => __( 'Enable', $domain ),
                    'disable' => __( 'Disable', $domain ),
                ],
                'class'   => ['regular-text'],
            ],
            'sidebar'    => [
                'label'   => __( 'Sidebar', $domain ),
                'type'    => 'select',
                'value'   => 'enable',
                'options' => [
                    'enable'  => __( 'Enable', $domain ),
                    'disable' => __( 'Disable', $domain ),
                ],
                'class'   => ['regular-text'],
            ],
        ];
        // Page layout settings ended
    }

    /**
     * Resets all settings
     *
     * @return void
     */
    public static function reset_all_settings() {
        $settings_keys = self::get( 'settings' );

        foreach ( $settings_keys as $key => $value ) {
            update_option( self::settings_name( $key ), $value );
            // foreach($value as $single){
            //     update
            // }
            // self::set_settings( $key, $value );
        }
    }

    /**
     * Creates formatted settings name|key
     *
     * @param  [type]   $name
     * @return string
     */
    public static function settings_name( $name ) {
        return sprintf( '%s-%s-%s', self::__, 'settings', $name );
    }

    /**
     * Returns a schema based settings
     *
     * @param  [type] $name
     * @return void
     */
    public static function get_settings( $name ) {
        $self     = new self();
        $settings = get_option( self::settings_name( $name ), $self->settings[$name] );
        $settings = empty( $settings ) || gettype( $settings ) == 'string' ? $self->settings[$name] : $settings;
        $core     = $self->settings[$name];

        if ( ! sizeof( $core ) == sizeof( $settings ) ) {
            foreach ( $core as $key => $schema ) {
                if ( ! isset( $settings[$key] ) ) {
                    $settings[$key] = $schema;
                }
            }
        }

        return $settings;
    }

    /**
     * Stores a schema based settings
     *
     * @param  [type] $name
     * @param  [type] $schema
     * @return void
     */
    public static function set_settings( $name, $schema ) {
        $self = new self();

        $core    = $self->settings[$name];
        $current = get_option( self::settings_name( $name ), $self->settings[$name] );
        $current = empty( $current ) || gettype( $current ) == 'string' ? $self->settings[$name] : $current;

        foreach ( $core as $key => $val ) {

            // !isset($current)
            if ( ! isset( $current[$key] ) ) {
                $current[$key] = $val;
            } else {
                $current[$key] = $schema[$key];
            }
        }

        update_option( self::settings_name( $name ), $current );

        return $current;
    }

    public static function split_schema_values( $schema ) {

    }

    public static function merge_schema_values( $values, $schema ) {
        $result = $schema;
        foreach ( $result as $key => $value ) {
            if ( ! isset( $values[$key] ) ) {
                continue;
            }

            $result[$key]['value'] = $values[$key];
        }

        return $result;
    }

    /**
     * Returns a single value of a specific schema based settings
     *
     * @param  string               $settings_family
     * @param  [type]               $key
     * @return mixed|array|object
     */
    public static function get_single_settings( $settings_family, $key ) {
        $self = new self();

        $settings = get_option( self::settings_name( $settings_family ), $self->settings[$settings_family][$key]['value'] );

        return $settings[$key]['value'];
    }

    public static function get_posted_settings_data( $settings_key ) {
        $schema = self::get( 'settings' )[$settings_key];
        $domain = self::__;
        $errors = [];
        $result = [];

        // Getting the data with validation
        foreach ( $schema as $key => $value ) {
            $value = self::prepare_single_field( $value );

            if ( $value['required'] == true && empty( $value['value'] ) ) {
                $errors[] = new \WP_Error( 'required-field-missing', __( sprintf( '%s is missing', $value['label'] ), $domain ) );
            }

            switch ( $value['type'] ) {
                case 'checkbox':
                    $result[$key] = ! isset( $_POST[$key] ) ? self::checkbox_off : $_POST[$key];
                    break;
                default:
                    $result[$key] = self::var ( $key );
                    break;
            }
        }

        // Process data based on type
        foreach ( $result as $key => $value ) {
            switch ( gettype( $value ) ) {
                case 'array':
                    $result[$key] = serialize( $value );
                    break;
                default:
                    break;
            }
        }

        if ( ! empty( $errors ) ) {
            return new \WP_Error( 'post-data-have-error', __( 'Posted data have errors', $errors ) );
        }

        return $result;
    }

    /**
     * Returns an schema
     *
     * @param  string               $name
     * @return mixed|array|object
     */
    public static function get( $name ) {
        // return new self();
        $self = new self();

        return $self->$name;
    }

    /**
     * Sets a schema
     *
     * @param  [type] $name
     * @param  [type] $schema
     * @return void
     */
    public static function set( $name, $schema ) {
        $self = new self();

        $self->$name = $schema;

        return true;
    }

    /**
     * Collects data from POST request
     *
     * Collects based on a Schema
     *
     * @param  [type] $name
     * @return void
     */
    public static function get_posted_data( $name ) {
        $schema = self::get( $name );
        $domain = self::__;
        $errors = [];
        $result = [];

        // Getting the data with validation
        foreach ( $schema as $key => $value ) {
            $value = self::prepare_single_field( $value );

            if ( $value['required'] == true && empty( $value['value'] ) ) {
                $errors[] = new \WP_Error( 'required-field-missing', __( sprintf( '%s is missing', $value['label'] ), $domain ) );
            }

            switch ( $value['type'] ) {
                case 'checkbox':
                    $result[$key] = ! isset( $_POST[$key] ) ? self::checkbox_off : $_POST[$key];
                    break;
                default:
                    $result[$key] = self::var ( $key );
                    break;
            }
        }

        // Process data based on type
        foreach ( $result as $key => $value ) {
            switch ( gettype( $value ) ) {
                case 'array':
                    $result[$key] = serialize( $value );
                    break;
                default:
                    break;
            }
        }

        if ( ! empty( $errors ) ) {
            return new \WP_Error( 'post-data-have-error', __( 'Posted data have errors', $errors ) );
        }

        return $result;
    }

    /**
     * Collects data from POST request
     *
     * Collects based on a Schema
     *
     * @param  [type] $name
     * @return void
     */
    public static function get_posted_data_using_schema( $schema ) {
        $domain = self::__;
        $errors = [];
        $result = [];

        // Getting the data with validation
        foreach ( $schema as $key => $value ) {
            $value = self::prepare_single_field( $value );

            if ( $value['required'] == true && empty( $value['value'] ) ) {
                $errors[] = new \WP_Error( 'required-field-missing', __( sprintf( '%s is missing', $value['label'] ), $domain ) );
            }

            switch ( $value['type'] ) {
                case 'checkbox':
                    $result[$key] = ! isset( $_POST[$key] ) ? self::checkbox_off : $_POST[$key];
                    break;
                default:
                    $result[$key] = self::var ( $key );
                    break;
            }
        }

        // Process data based on type
        foreach ( $result as $key => $value ) {
            switch ( gettype( $value ) ) {
                case 'array':
                    $result[$key] = serialize( $value );
                    break;
                default:
                    break;
            }
        }

        if ( ! empty( $errors ) ) {
            return new \WP_Error( 'post-data-have-error', __( 'Posted data have errors', $errors ) );
        }

        return $result;
    }

    /**
     * Validates a singe POST request field
     *
     * @param  [type] $name
     * @param  string $type
     * @return void
     */
    public static function var ( $name, $type = 'POST' ) {
        switch ( $type ) {
            case 'GET':
                return isset( $_GET[$name] ) ? $_GET[$name] : '';
                break;
            default:
                return isset( $_POST[$name] ) ? $_POST[$name] : '';
                break;
        }
    }

    /**
     * Builds database supported schema
     *
     * @return void
     */
    public static function database_create_table_schema( $table_name, $schema, $primary_key = 'id' ) {
        $prefix          = CRUD::DB()->prefix;
        $charset_collate = CRUD::DB()->get_charset_collate();

        $qry = "CREATE TABLE IF NOT EXISTS `{$prefix}{$table_name}` (
        `{$primary_key}` int(255) NOT NULL AUTO_INCREMENT, ";

        foreach ( $schema as $field => $val ) {
            $qry .= self::database_single_field_schema( $field, $val );
        }

        $qry .= "PRIMARY KEY (`{$primary_key}`) ) {$charset_collate}";

        return $qry;
    }

    /**
     * Creates single database field schema
     *
     * @param  [type] $schema
     * @return void
     */
    public static function database_single_field_schema( $field_name, $schema ) {
        $schema = self::prepare_single_field( $schema );

        $null = $schema['required'] == true ? 'NOT NULL' : 'DEFAULT NULL';

        return "`{$field_name}` {$schema['data_type']} {$null}, ";
    }

    /**
     * Converts columns to data type for SQL operations
     *
     * @return array
     */
    public static function data_col_type_schema( $schema ) {
        $result = [];

        foreach ( $schema as $key => $value ) {
            switch ( $value['data_type'] ) {
                case 'longtext':
                case 'text':
                case 'varchar':
                    $result[] = '%s';
                    break;
                case 'integer':
                case 'number':
                case 'int':
                case 'longint':
                    $result[] = '%d';
                    break;
                case 'float':
                case 'double':
                    $result[] = '%f';
                    break;
                default:
                    $result[] = '%s';
                    break;
            }
        }

        return $result;
    }

    public static function DB_prefix() {
        return CRUD::DB()->prefix;
    }

}