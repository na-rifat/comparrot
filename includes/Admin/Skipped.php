<?php

namespace Comparrot\Admin;

use Comparrot\Processor\Processor;

class Skipped extends \WP_List_Table {
    function __construct( $posts ) {
        $GLOBALS['comparrot_import_log'] = $posts;
        parent::__construct( array(
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false,
        ) );

        $this->_show();
    }

    /**
     * Get columns
     *
     * @return void
     */
    function get_columns() {
        return [
            'PageTitle'   => 'PageTitle',
            'AuthorName'  => 'AuthorName',
            'PublishDate' => 'PublishDate',
        ];
    }

    /**
     * Sortable columns list
     *
     * @return void
     */
    function get_sortable_columns() {
        $sortable_columns = [

        ];
        return $sortable_columns;
    }

    /**
     * Formats and sends default comments
     *
     * @param  [type] $item
     * @param  [type] $column_name
     * @return void
     */
    protected function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'image':
                return "<img class='comparrot-imported-thumbnail' src='{$item['img_url']['thumbnail']}' alt='" . __( 'Comparrot imported post thumbnail', 'comparrot' ) . "' />";
            case 'post_id':
                return $item['ID'];
            case 'title':
                return $item['post_title'];
            case 'category':
                $categories = $item['post_category'];
                $result     = [];
                foreach ( $categories as $category ) {
                    $result[] = get_cat_name( $category );
                }
                return implode( ', ', $result );

                return $item['post_category'] == $this->processor->default_category ? $this->processor->default_category_name : get_cat_name( $item['post_category'] );
            case 'type':
                return ucfirst( $item['post_type'] );
            case 'status':
                return ucfirst( $item['post_status'] );
            case 'parent':
                return $item['post_parent'];
            case 'layout':
                return Processor::layout_decoded_name( $item['post_layout'] );
            default:
                return isset( $item[$column_name] ) ? $item[$column_name] : '';
                break;
        }
    }

    /**
     * Prepares items
     *
     * @return void
     */
    public function prepare_items() {
        $column   = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $column, $hidden, $sortable );

        $this->items = is_object( $GLOBALS['comparrot_import_log'] ) ? $GLOBALS['comparrot_import_log'] : (object) [];
        $this->items = $GLOBALS['comparrot_import_log'];

        $size = count( $this->items );

        $this->set_pagination_args( array(
            'total_items' => $size,
            'per_page'    => $size,
        ) );
    }

    /**
     * Generates content for a single row of the table.
     *
     * @param object|array $item The current item
     */
    public function single_row( $item ) {
        echo "<tr>";
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    /**
     * Creates the list
     *
     * @param  [type] $list
     * @return void
     */
    public function _show() {
        $this->prepare_items();
        $this->display();
    }
}