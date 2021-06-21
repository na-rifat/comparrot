<?php

namespace Comparrot\Processor;

use Comparrot\Comparrot;

/**
 * Processes CSV file
 *
 * Prepares procssed data to insert as posts and page
 */
class Processor {
    use \Comparrot\Traits\Helper;
    /**
     * Useful variables
     */
    const supported_types = [
        'post',
        'page',
    ];
    const supported_status = [
        'publish',
        'draft',
    ];
    const supported_layouts = [
        'full_width',
        'container',
    ];

    public $image_url;
    public $current_user;
    public $default_category;
    public $default_category_name;

    /**
     * Builds the class
     */
    function __construct() {
        add_action( 'init', [$this, 'add_nav_menu'] );
    }

    public function add_nav_menu() {
        register_nav_menu( 'comparrot_template_header', __( 'Comparrot template header', 'comparrot' ) );
    }

    /**
     * Initializes the class
     *
     * @return void
     */
    public function init() {
        $this->current_user          = get_current_user_id();
        $this->image_url             = wp_upload_dir()['baseurl'];
        $this->default_category      = get_option( 'default_category' );
        $this->default_category_name = get_cat_name( $this->default_category );
    }

    /**
     * Converts array to json string
     *
     * @param  array  $obj
     * @return void
     */
    public static function json_status( $obj = [] ) {
        echo json_encode( $obj );
        return;
    }

    /**
     * Encodes layout options for user friendly use
     *
     * @param  string   $layout
     * @return string
     */
    public static function layout_encoded_name( $layout ) {
        switch ( strtolower( $layout ) ) {
            case 'full_width':
            case 'full width':
            case 'fullwidth':
                return 'full_width';
                break;
            case 'container':
                return 'container';

        }
    }

    /**
     * Decodes layout options for user friendly use
     *
     * @param  string   $layout
     * @return string
     */
    public static function layout_decoded_name( $layout ) {
        switch ( strtolower( $layout ) ) {
            case 'full_width':
            case 'full width':
            case 'fullwidth':
                return 'Full width';
                break;
            case 'container':
                return 'Container';
        }
    }

    /**
     * Inserts the categories
     *
     * @param  array|object|string   $categories
     * @return array|object|string
     */
    public function generate_categories( $categories ) {
        $result     = [];
        $categories = explode( ', ', $categories );

        foreach ( $categories as $category ) {
            $result[] = get_cat_ID( $category );
        }

        return $result;
    }

    /**
     * Processes image files
     *
     * @param  array|object   $files
     * @return array|object
     */
    public function process_img_files( $files ) {
        $result = [];

        foreach ( $files as $file ) {
            $result[] = [$files['name'] => $file];
        }

        return $result;
    }

    /**
     * Processes image urls immediatley after insert as post attachments
     *
     * @param  array|object   $meta
     * @return array|object
     */
    public static function process_image_urls( $meta ) {
        $image_url = wp_upload_dir()['baseurl'];
        $sub_base  = str_replace( basename( $meta['file'] ), '', $meta['file'] );

        return [
            'large'     => "{$image_url}/{$meta['file']}",
            'medium'    => "{$image_url}/{$sub_base}{$meta['sizes']['medium']['file']}",
            'thumbnail' => "{$image_url}/{$sub_base}{$meta['sizes']['thumbnail']['file']}",
        ];
    }

    /**
     * Processes the CSV file
     *
     * @param  array|object $csv_data
     * @param  array|object $image_files
     * @return void
     */
    public static function begin_inserting_pages( $csv_data, $image_files ) {
        $columns = $csv_data[0];
        unset( $csv_data[0] );
        $data = self::build_csv_data( $columns, $csv_data );

        // print_r( $data );exit;

        $inserted = [];
        $skipped  = [];

        // var_dump( $data );exit;

        // self::move_merge_template();
        foreach ( $data as $row ) {
            if ( self::is_page_exists( $row ) ) {
                $skipped[] = $row;
            } else {
                self::create_page( $row, $image_files );
                $inserted[] = $row;
            }
        }

        // echo sizeof( $skipped );exit;
        // exit;

        self::attach_non_csv_images( $data, $image_files );

        $log = self::generate_logs( $inserted, $skipped );

        self::json_status(
            [
                'inserted' => $log['inserted'],
                'skipped'  => $log['skipped'],
            ]
        );

        exit;
    }

    /**
     * Generates import logs based on reported data
     *
     * @param  array|object $inserted
     * @param  array|object $skipped
     * @return void
     */
    public static function generate_logs( $inserted, $skipped ) {
        ob_start();
        new \Comparrot\Admin\Uploadlog( $inserted );
        $inserted = ob_get_clean();

        ob_start();
        new \Comparrot\Admin\Skipped( $skipped );
        $skipped = ob_get_clean();

        return [
            'inserted' => $inserted,
            'skipped'  => $skipped,
        ];
    }

    /**
     * Validates CSV rows
     *
     * @param  array|object   $csv_data
     * @return object|array
     */
    public static function build_csv_data( $columns, $csv_data ) {
        $result = [];

        foreach ( $csv_data as $row ) {
            $tmp_row = [];
            $i       = 0;
            foreach ( $columns as $col ) {
                $tmp_row[$col] = self::process_col( $col, $row[$i] );
                $i++;
            }
            $result[] = $tmp_row;
        }

        return $result;
    }

    /**
     * Processes each columns
     *
     * Replaces, modifies or converts specific values
     *
     * @param  string   $col_name
     * @param  string   $value
     * @return string
     */
    public static function process_col( $col_name, $value ) {
        switch ( $col_name ) {
            default:
                return $value;
                break;
        }
    }

    /**
     * Copying sample merge template in wp-contents
     *
     * @return void
     */
    public static function move_merge_template() {
        // Creates the directory if not exists
        if ( ! file_exists( ABSPATH . '/wp-content/html-templates/' ) ) {
            mkdir( ABSPATH . '/wp-content/html-templates/' );
        }

        $files = scandir( CMP_MT_PATH );

        foreach ( $files as $file ) {
            if ( $file === '.' || $file === '.' ) {
                continue;
            }

            $handler_path = ABSPATH . "/wp-content/html-templates/{$file}";

            $handler       = fopen( $handler_path, 'w' );
            $template_file = fopen( CMP_MT_PATH . $file, 'r' );

            $template = fread( $template_file, filesize( CMP_MT_PATH . $file ) );
            fwrite( $handler, $template );

            fclose( $template_file );
            fclose( $handler );
        }
    }

    public static function sanitize_page_content( $content ) {
        if ( empty( $content ) || is_null( $content ) ) {
            return '';
        }

        if ( preg_match( '/^\{?[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}\}?$/', $content ) ) {

            $api_key = \Comparrot\Schema\Schema::get_settings( 'header' )['seo-textwriter-api-key']['value'];

            $curl = curl_init();

            curl_setopt_array( $curl, array(
                CURLOPT_URL            => sprintf( 'https://api.seotextwriter.com/api/ContentEditor/GetContentTextByGuid/%s', $content ),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_HTTPHEADER     => array(
                    "cache-control: no-cache",
                    sprintf( 'SeoTextWriterApiKey: %s', $api_key ),
                ),
            ) );

            $result = json_decode( curl_exec( $curl ) );
            curl_close( $curl );

            if ( $result == 'Error: not authorized' ||
                $result == "Error: The parameter '{$api_key}' is not a valid guid" ) {
                return null;
            }

            return $result;

        } else {
            return $content;
        }
    }

    public static function collect_meta_fields( $data, $image_urls, $modified_time ) {
        $fields = \Comparrot\Schema\Schema::get( 'meta_fields' );
        $result = '';

        foreach ( $fields as $field ) {
            $key = strtolower(
                str_replace(
                    'Meta',
                    '',
                    str_replace(
                        'Meta_og:',
                        '',
                        $field
                    )
                )
            );

            switch ( $key ) {
                case 'title':
                    $result .= ! empty( $data['PageTitle'] ) ? sprintf( '<meta property="og:%s" content="%s" >' . "\n", $key, $data['PageTitle'] ) : '';
                    break;
                case 'image':
                    $result .= ! empty( $image_urls['large'] ) ? sprintf( '<meta property="og:%s" content="%s" >' . "\n", $key, $image_urls['large'] ) : '';
                    break;
                case 'article:modified_time':
                    $result .= ! empty( $modified_time ) ? sprintf( '<meta property="article:modified_time" content="%s" >' . "\n", $modified_time ) : '';
                    break;
                case 'robots':
                case 'description':
                    $result .= ! empty( $data[$field] ) ? sprintf( '<meta name="%s" content="%s" >' . "\n", $key, $data[$field] ) : '';
                    break;
                default:
                    $result .= ! empty( $data[$field] ) ? sprintf( '<meta property="og:%s" content="%s" >' . "\n", $key, $data[$field] ) : '';
                    break;
            }
        }

        return $result;
    }

    /**
     * Creates post using wp_insert_post
     *
     * @param  array|object $data
     * @param  array|object $image_files
     * @return void
     */
    public static function create_page( $data, $image_files ) {
        $date_format = \Comparrot\Schema\Schema::get_settings( 'header' )['date-format']['value'];
        // Insert the post
        $content = '';

        $content = self::sanitize_page_content( $data['PageContent'] );
        if ( is_null( $content ) ) {
            self::json_status(
                [
                    'success' => false,
                    'msg'     => __( 'An error occured while gettting content from the API.', 'comparrot' ),
                ]
            );
            exit;
        }

        $data['PageContent'] = $content;
        $post_content        = self::prepare_page_content_before( $data );
        $true_template       = isset( $data['Canvas'] ) && $data['Canvas'] == 1 ? 'comparrot_canvas' : 'comparrot';

        if ( $post_content ) {
            $post_id = wp_insert_post(
                [
                    'post_title'     => $data['PageTitle'],
                    'post_content'   => $post_content,
                    'post_type'      => 'page',
                    'post_mime_type' => 'text/plain',
                    'post_status'    => 'publish',
                    'post_parent'    => self::get_page_parent_id( $data ),
                    'meta_input'     => [
                        '_wp_page_template'      => $true_template,
                        '_wp_comparrot_template' => $data['Template'],
                    ],
                ]
            );
        }

        $image_urls    = [];
        $post          = get_post( $post_id );
        $modified_date = wp_date( $date_format, strtotime( $post->post_modified ) );
        $publish_date  = wp_date( $date_format, strtotime( $post->post_date ) );

        // Insert featured image
        if ( isset( $image_files[$data['FeaturedImage']] ) ) {
            $image_urls = self::upload_feature_image( $post_id, $image_files[$data['FeaturedImage']] );
        }

        wp_update_post(
            [
                'ID'           => $post_id,
                'post_content' => self::prepare_page_content_after( [
                    'FeaturedImage' => ! empty( $data['FeaturedImage'] ) ? $data['FeaturedImage'] : '',
                    'PageUrl'       => get_page_link( $post_id ),
                    'SiteName'      => get_option( 'blog_name' ),
                    'SiteUrl'       => site_url(),
                    'ImageFolder'   => 'comparrot',
                    'UpdateDate'    => sprintf( '<div class="date-updated" data-mergefield="UpdateDate">%s</div>', $modified_date ),
                    'PublishDate'   => sprintf( '<div class="date-published" data-mergefield="PublishDate">%s</div>', $publish_date ),
                    'RCS'           => CMP_RESOURCE_URL,
                    'rcs'           => CMP_RESOURCE_URL,
                    'Breadcrumb'    => self::create_breadcrumb( $data ),
                    'imgf'          => site_url( 'wp-content/uploads/comparrot' ),
                    'Menu'          => wp_nav_menu(
                        [
                            'echo'           => false,
                            'theme_location' => 'comparrot_template_header',
                        ]
                    ),
                ], $post_content ),
                'meta_input'   => [
                    'comparrot-meta' => self::collect_meta_fields(
                        $data,
                        $image_urls,
                        $modified_date
                    ),
                ],
            ]
        );
    }

    /**
     * Inserts post attachment image
     *
     * @param  int          $post_id
     * @param  array|object $img
     * @return void
     */
    public static function upload_feature_image( $post_id, $img ) {
        $filename   = $img['name'];
        $upload_dir = wp_upload_dir(); // Set upload folder
        $base_dir   = $upload_dir['basedir'] . '/comparrot';
        $image_data = file_get_contents( $img['tmp_name'] ); // Get image data

        // Check folder permission and define file location
        if ( ! file_exists( $base_dir ) ) {
            wp_mkdir_p( $base_dir );
        }

        $file = $base_dir . '/' . $filename;

        if ( file_exists( $file ) ) {
            global $wpdb;
            $prefix = $wpdb->prefix;
            $title  = sanitize_file_name( $filename );

            $attach_id = $wpdb->get_row(
                "SELECT * FROM {$prefix}posts WHERE post_title='{$title}' AND post_type='attachment'"
            );

            if ( ! empty( $attach_id ) ) {
                $attach_id = $attach_id->ID;
            }

            $data = wp_get_attachment_metadata( $attach_id );

            set_post_thumbnail( $post_id, $attach_id );

            return self::process_image_urls( $data );
        } else {
            // Create the image  file on the server
            file_put_contents( $file, $image_data );

            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );

            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );

            // Include image.php
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // And finally assign featured image to post
            set_post_thumbnail( $post_id, $attach_id );

            return self::process_image_urls( $attach_data );
        }
    }

    /**
     * Checks if a post exists or not
     *
     * Also check if the post exists in the trash or not
     *
     * @param  array|object $post
     * @return bool
     */
    public static function is_page_exists( $post ) {

        if ( post_exists( $post['PageTitle'], '', '', 'page' )
            && self::is_page_in_trash( $post['PageTitle'] ) == false ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a post exists in the trash or not
     *
     * @param  string $title
     * @param  string $type
     * @return bool
     */
    public static function is_page_in_trash( $title ) {
        $post = get_page_by_title( $title, 'OBJECT', 'page' );

        if ( is_null( $post ) ) {
            return false;
        }

        $status = get_post_status( $post->ID );

        if ( $status == 'trash' ) {
            return true;
        }

        return false;
    }

    /**
     * Prepares page contents before insert
     *
     * @param  array|object $data
     * @return void
     */
    public static function prepare_page_content_before( $data ) {

        // Template management part
        if ( ! empty( $data['Template'] ) & file_exists( ABSPATH . "/wp-content/html-templates/{$data['Template']}" ) ) {
            $template_path = ABSPATH . "/wp-content/html-templates/{$data['Template']}";
        } else {
            $template_path = ABSPATH . "/wp-content/html-templates/merge_template_1.html";
        }
        ob_start();
        include $template_path;
        $template = (string) ob_get_clean();
        $template = trim( preg_replace( '/\s\s+/', ' ', $template ) );
        // Template part ended

        foreach ( $data as $key => $value ) {
            switch ( $key ) {
                case 'InterlinkSubTitleBox1':
                case 'InterlinkTitle1':
                    $template = str_replace(
                        sprintf( '[%s]', $key ),
                        self::create_interlink_col( $value, $data['InterlinkBox1'] ),
                        $template
                    );
                    break;
                case 'InterlinkSubTitleBox2':
                case 'InterlinkTitle2':
                    $template = str_replace(
                        sprintf( '[%s]', $key ),
                        self::create_interlink_col( $value, $data['InterlinkBox2'] ),
                        $template
                    );
                    break;
                case 'InterlinkSubTitleBox3':
                case 'InterlinkTitle3':
                    $template = str_replace(
                        sprintf( '[%s]', $key ),
                        self::create_interlink_col( $value, $data['InterlinkBox3'] ),
                        $template
                    );
                    break;
                case 'FeaturedImage':
                case 'UpdateDate':
                case 'PublishDate':
                    break;
                default:
                    $template = str_replace(
                        sprintf( '[%s]', $key ),
                        $value,
                        $template
                    );
                    break;
            }
        }

        return $template;
    }

    /**
     * Prepares page contents after  && before update
     *
     * @param  array|object $data
     * @param  string       $post_content
     * @return string
     */
    public static function prepare_page_content_after( $data, $post_content ) {
        $template = $post_content;

        foreach ( $data as $key => $value ) {
            switch ( $key ) {
                case 'ParentPage':
                    $parent = get_page_by_title( $data[$key] );
                    if ( $parent != null ) {
                        $data['post_parent'] = $parent;
                    }
                    break;
                default:
                    $template = str_replace(
                        sprintf( '[%s]', $key ),
                        $value,
                        $template
                    );
                    break;
            }
        }

        $template = preg_replace( '/<\[(.*?)\]/', '', $template );
        return $template;
    }

    /**
     * Generates interlink columns and urls
     *
     * @param  string              $box_title
     * @param  string|array|object $interlinks
     * @return string
     */
    public static function create_interlink_col( $box_title, $interlinks ) {
        $interlinks = explode( ';', $interlinks );

        if ( empty( $interlinks ) || sizeof( $interlinks ) == 1 ) {
            return;
        }

        $list = '';

        foreach ( $interlinks as $link ) {
            $url   = explode( '|', $link )[0];
            $title = explode( '|', $link )[1];

            if ( empty( $url ) && empty( $title ) ) {
                continue;
            }
            $list .= sprintf( '<li><a href="%s"><i class="fas fa-chevron-right"></i>%s</a></li>', $url, $title );

        }

        $el = sprintf( '<div class="interlink-col comparrot-col-md-4"><h3>%s</h3><ul>%s</ul></div>', $box_title, $list );

        return $el;
    }

    /**
     * Generates page parent ID
     *
     * @param  string $title
     * @return int
     */
    public static function get_page_parent_id( $data ) {
        if ( empty( $data['ParentPage'] ) ) {
            return 0;
        }

        $parent = get_page_by_title( $data['ParentPage'] );

        if ( $parent !== null ) {
            return $parent->ID;
        }

        return 0;
    }

    /**
     * Creates breadcrumb of a page
     *
     * @param  array|object $data
     * @return string
     */
    public static function create_breadcrumb( $data ) {

        if ( isset( $data['Breadcrumb'] ) && ! empty( $data['Breadcrumb'] ) ) {
            return $data['Breadcrumb'];
        }

        $home = sprintf( '<a href="%s">Home</a> » ', get_site_url() );

        $parent = '';

        if ( isset( $data['ParentPage'] ) && get_page_by_title( $data['ParentPage'] ) !== null ) {
            $parent_obj = get_page_by_title( $data['ParentPage'] );
            $parent     = sprintf(
                '<a href="%s"><strong>%s</strong></a> » ',
                site_url( '/' . $parent_obj->post_name ),
                $parent_obj->post_title );
        }

        $current_page = sprintf( '<strong>%s</strong>', $data['PageTitle'] );

        return $home . $parent . $current_page;
    }

    /**
     * Inserts post attachment image
     *
     * @param  int          $post_id
     * @param  array|object $img
     * @return void
     */
    public static function attach_image( $img ) {
        // Add Featured Image to Post
        $filename   = $img['name'];
        $upload_dir = wp_upload_dir(); // Set upload folder
        $base_dir   = $upload_dir['basedir'] . '/comparrot';
        $image_data = file_get_contents( $img['tmp_name'] ); // Get image data

        // Check folder permission and define file location
        // var_dump($base_dir);exit;
        if ( ! file_exists( $base_dir ) ) {
            wp_mkdir_p( $base_dir );
        }

        $file = $base_dir . '/' . $filename;

        if ( file_exists( $file ) ) {
            return;
        }
        // Create the image  file on the server
        file_put_contents( $file, $image_data );

        // Check image file type
        $wp_filetype = wp_check_filetype( $filename, null );

        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name( $filename ),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        // Create the attachment
        $attach_id = wp_insert_attachment( $attachment, $file );

        // Include image.php
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

        // // Assign metadata to attachment
        wp_update_attachment_metadata( $attach_id, $attach_data );
    }

    public static function attach_non_csv_images( $csv_data, $img ) {
        $img = $img;
        foreach ( $csv_data as $row ) {
            if ( isset( $row['FeaturedImage'] ) && isset( $img[$row['FeaturedImage']] ) ) {
                unset( $img[$row['FeaturedImage']] );
            }
        }
        foreach ( $img as $image ) {
            self::attach_image( $image );
        }
    }

    public static function update_modified_date( $date, $content, $merge_field ) {
        return preg_replace( '/data-mergefield=\"' . $merge_field . '\"(.+|.*)>([^<>]*?)<\/span/i',
            sprintf( 'data-mergefield="%s">%s</span', $merge_field, $date ),
            $content );
    }

    public static function create_rating_points( $star_count, $template_name ) {

    }
}