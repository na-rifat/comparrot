<?php
    // namespace Comparrot;
    /**
     * This files contains all important functions for comparrot wp plugin
     */

    /**
     * Return a css files url
     *
     * @param  [type] $filename
     * @return void
     */
    if ( ! function_exists( 'comparrot_cssfile' ) ) {
        function comparrot_cssfile( $filename, $deps = [] ) {
            return ['src' => COMPARROT_CSS . "/{$filename}.css", 'version' => comparrot_cssversion( $filename ), 'deps' => $deps];
        }
    }

    /**
     * Return a js files url
     *
     * @param  [type] $filename
     * @return void
     */
    if ( ! function_exists( 'comparrot_jsfile' ) ) {
        function comparrot_jsfile( $filename, $deps = [] ) {
            return ['src' => COMPARROT_JS . "/{$filename}.js", 'version' => comparrot_jsversion( $filename ), 'deps' => $deps];
        }
    }

    /**
     * Return a image files url
     *
     * @param  [type] $filename
     * @return void
     */
    if ( ! function_exists( 'comparrot_imgfile' ) ) {
        function comparrot_imgfile( $filename ) {
            return COMPARROT_IMAGES . "/$filename";
        }
    }

    /**
     * Get js files version based on date modified
     *
     * @param  [type] $filename
     * @return void
     */
    if ( ! function_exists( 'comparrot_jsversion' ) ) {
        function comparrot_jsversion( $filename ) {
            return filemtime( convert_path_slash( COMPARROT_PATH . "/assets/js/{$filename}.js" ) );
        }
    }
    /**
     * Get css files version based on date modified
     *
     * @param  [type] $filename
     * @return void
     */
    if ( ! function_exists( 'comparrot_cssversion' ) ) {
        function comparrot_cssversion( $filename ) {
            return filemtime( convert_path_slash( COMPARROT_PATH . "/assets/css/{$filename}.css" ) );
        }
    }

    /**
     * Replaces back slashes with slashes from a files path
     *
     * @param  [type] $path
     * @return void
     */
    if ( ! function_exists( 'convert_path_slash' ) ) {
        function convert_path_slash( $path ) {
            return str_replace( "\\", "/", $path );
        }
    }

    /**
     * Pulls a template from views folder
     *
     * @param  [type] $dir
     * @param  [type] $filename
     * @return void
     */
    if ( ! function_exists( 'comparrot_template' ) ) {
        function comparrot_template( $dir, $filename ) {
            ob_start();
            include convert_path_slash( "{$dir}/views/{$filename}.php" );
            return ob_get_clean();
        }
    }

    if ( ! function_exists( 'comparrot_admin_template' ) ) {
        /**
         * Returns a template for admin panel
         *
         * @param  [type] $dir
         * @param  [type] $filename
         * @return void
         */
        function comparrot_admin_template( $dir, $filename ) {
            ob_start();
            include convert_path_slash( "{$dir}/views/{$filename}.php" );
            echo ob_get_clean();
            return;
        }
    }

    /**
     * Creates a action field for forms
     *
     * @param  [type] $action
     * @return void
     */
    if ( ! function_exists( 'comparrot_form_action' ) ) {
        function comparrot_form_action( $action ) {
            ob_start();
        ?>
<input type="hidden" name="action" value="<?php echo $action ?>" />
<?php
    echo ob_get_clean();
        }
    }

    /**
     * get's google recaptcha response
     *
     * @param  [type] $recaptcha
     * @return void
     */
    if ( ! function_exists( 'reCaptcha' ) ) {
        function reCaptcha( $recaptcha ) {
            $secret = get_option( 'comparrot_captcha_secret' ) ? get_option( 'comparrot_captcha_secret' ) : '';
            $ip     = $_SERVER['REMOTE_ADDR'];

            $postvars = array(
                "secret"   => $secret,
                "response" => $recaptcha,
                "remoteip" => $ip,
            );
            $url = "https://www.google.com/recaptcha/api/siteverify";
            $ch  = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $postvars );
            $data = curl_exec( $ch );
            curl_close( $ch );

            return json_decode( $data, true );
        }
    }

    /**
     * Verifies if a function is okay or not
     *
     * @return void
     */
    if ( ! function_exists( 'verify_comparrot_captcha' ) ) {
        function verify_comparrot_captcha() {
            $recaptcha = $_POST['g-recaptcha-response'];
            $res       = reCaptcha( $recaptcha );
            if ( ! $res['success'] ) {
                return true;
            } else {
                return false;
            }
        }
    }

    if ( ! function_exists( 'comparrot_ajax' ) ) {
        /**
         * Registers an ajax hook
         *
         * @param  [type] $action
         * @param  array  $func
         * @return void
         */
        function comparrot_ajax( $action, $func = [] ) {
            add_action( "wp_ajax_$action", $func );
            add_action( "wp_ajax_nopriv_$action", $func );
        }
    }

    if ( ! function_exists( 'comparrot_var' ) ) {
        /**
         * Returns formatted variable
         *
         * @param  [type]                        $var
         * @return void|string|int|array|mixed
         */
        function comparrot_var( $var ) {
            return isset( $_POST[$var] ) && ! empty( $_POST[$var] ) ? $_POST[$var] : '';
        }

        if ( ! function_exists( 'comparrot_get_option' ) ) {
            function comparrot_get_option( $key ) {
                return stripslashes( get_option( $key ) );
            }
        }
    }

    if ( ! function_exists( 'array2options' ) ) {
        function array2options( $array ) {
            $result = '';
            foreach ( $array as $item ) {
                $caption = ucwords( $item );
                $result .= "<option value='{$item}'>{$caption}</option";
            }
            return $result;
        }
    }

    if ( ! function_exists( 'std2array' ) ) {
        function std2array( $std ) {
            return json_decode( json_encode( $std ), true );
        }
    }
    if ( ! function_exists( 'comparrot_toggle' ) ) {
        function comparrot_toggle( $atts ) {
            $curernt_val = get_option( $atts['key'] );
            $val1        = $atts['val1'];
            $val2        = $atts['val2'];
            $title1      = $atts['title1'];
            $title2      = $atts['title2'];

            ob_start();
            if ( $curernt_val == $val2 ) {
            ?>
<div id="<?php echo $atts['id'] ?>" class="cp-toggle shad" data-key="<?php echo $atts['key'] ?>"
    data-value="<?php echo $curernt_val ?>">
    <div class="toggle-item" data-value="<?php echo $val1 ?>"><?php _e( $title1, 'comparrot' )?></div>
    <div class="toggle-item active-toggle" data-value="<?php echo $val2 ?>"><?php _e( $title2, 'comparrot' )?></div>
</div>
<?php
    } else {
            ?>
<div id="<?php echo $atts['id'] ?>" class="cp-toggle shad" data-key="<?php echo $atts['key'] ?>"
    data-value="<?php echo $curernt_val ?>">
    <div class="toggle-item active-toggle" data-value="<?php echo $val1 ?>"><?php _e( $title1, 'comparrot' )?></div>
    <div class="toggle-item" data-value="<?php echo $val2 ?>"><?php _e( $title2, 'comparrot' )?></div>
</div>
<?php
    }

        }
    }

    if ( ! function_exists( 'hex_color' ) ) {
        function hex_color( $rgb ) {
            if ( strpos( $rgb, 'rgb' ) !== FALSE ) {
                $rgb = str_replace( ')', '', str_replace( '(', '', str_replace( 'rgb', '', $rgb ) ) );
                $rgb = explode( ', ', $rgb );
                $hex = sprintf( "#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2] );

                return $hex;
            } else {
                $hex = $rgb;
                if ( strlen( $hex ) == 4 ) {
                    $hex = '#' . str_replace( '#', '', $hex ) . str_replace( '#', '', $hex );
                }

                return $hex;
            }
    }
}