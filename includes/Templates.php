<?php

namespace Comparrot;

/**
 * Name: Templates class
 *
 * Handles template parts
 *
 *
 *
 *
 * @author Rafalo tech <admin@rafalotech.com>
 *
 * @since 1.0.0
 */
class Templates {

    function __construct() {

    }

    /**
     * Includes a template file
     *
     * @param  [type] $file
     * @return void
     */
    public function get( $file ) {
        ob_start();
        include COMPARROT_TEMPLATES_PATH . "/{$file}.php";
        return ob_get_clean();
    }

    /**
     * Echos a template
     *
     * @param string $name
     * @return void
     */
    public function import( $name ) {
        echo $this->get( $name );
    }

    /**
     * Generate interlink column items
     *
     * @param  string $box_title
     * @param  string $interlinks
     * @return void
     */
    public static function create_interlink_col( $box_title, $interlinks ) {
        $interlinks = explode( ';', $interlinks );

        if ( empty( $interlinks ) ) {
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
        $el = sprintf( '<div class="interlink-col col-md-3"><h3>%s</h3><ul>%s</ul></div>', $box_title, $list );

        return $el;
    }

}