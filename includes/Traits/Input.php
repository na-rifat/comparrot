<?php

namespace Comparrot\Traits;

use Comparrot\Comparrot;
use Comparrot\Schema\Schema;

trait Input {
    public $__        = Comparrot::domain;
    public $domain    = Comparrot::domain;
    public $supported = [
        'text',
        'email',
        'url',
        'color',
        'date',
        'datetime',
        'file',
        'select',
        'switch',
        'textarea',
    ];
    /**
     * Creates a form of input fields based schema
     *
     * @param  string       $form_name
     * @param  array|object $schema
     * @param  boolean      $admin
     * @param  integer      $start
     * @param  integer      $end
     * @return void
     */
    public static function create_settings_form( Array $args = [] ) {
        $self     = new self();
        $defaults = [
            'settings_key' => $self->domain . '-schema-form',
            'admin'        => true,
            'start'        => 0,
            'end'          => 0,
            'class'        => [],
        ];
        $args   = wp_parse_args( $args, $defaults );
        $fields = '';

        $schema         = new Schema();
        $args['schema'] = $schema::get_settings( $args['settings_key'] );

        foreach ( $args['schema'] as $field => $val ) {
            $val = self::prepare_single_field( $val );
            if ( ! in_array( $val['type'], $self->supported ) ) {
                continue;
            }

            $fields .= self::make_single_field_markup( $field, $val, $args['admin'] );
        }

        return sprintf( '<form data-name="%s" class="comparrot-schema-form %s" method="POST", action="%s">%s</form>',
            $args['settings_key'],
            implode( ' ', $args['class'] ),
            '',
            $fields );
    }

    /**
     * Creates a form of input fields based schema
     *
     * @param  string       $form_name
     * @param  array|object $schema
     * @param  boolean      $admin
     * @param  integer      $start
     * @param  integer      $end
     * @return void
     */
    public static function create_form( Array $args = [] ) {
        $self     = new self();
        $defaults = [
            'form_name' => $self->domain . '-schema-form',
            'admin'     => false,
            'start'     => 0,
            'end'       => 0,
            'class'     => [],
            'schema'    => [],
        ];
        $args   = wp_parse_args( $args, $defaults );
        $fields = '';

        foreach ( $args['schema'] as $field => $val ) {
            $val = self::prepare_single_field( $val );
            if ( ! in_array( $val['type'], $self->supported ) ) {
                continue;
            }

            $fields .= self::make_single_field_markup( $field, $val, $args['admin'] );
        }

        return sprintf( '<form data-name="%s" class="comparrot-schema-form %s" method="POST", action="%s">%s</form>',
            $args['form_name'],
            implode( ' ', $args['class'] ),
            '',
            $fields );
    }

    /**
     * Creates markup for a single input field
     *
     * @param  string       $name
     * @param  array|object $field
     * @param  boolean      $admin
     * @return void
     */
    public static function make_single_field_markup( $name, $field, $admin = false ) {
        $label = sprintf( '<label for="%s">%s</label>', $name, $field['label'] );
        $class = implode( ' ', $field['class'] );

        switch ( $field['type'] ) {
            case 'text':
            case 'number':
            case 'date':
            case 'url':
            case 'email':
                $input = sprintf( '<input type="%s" name="%s" id="%s" placeholder="%s" value="%s" class="%s"/>',
                    $field['type'],
                    $name,
                    $name,
                    $field['placeholder'],
                    $field['value'],
                    $class
                );

                break;
            case 'color':
                $value = hex_color( $field['value'] );
                $input = sprintf( '<input type="%s" name="%s" id="%s" placeholder="%s" value="%s" class="%s"/>',
                    $field['type'],
                    $name,
                    $name,
                    $field['placeholder'],
                    $value,
                    $class
                );
                break;
            case 'select':

                $options = '';

                foreach ( $field['options'] as $option_value => $option_title ) {
                    $selected = $field['value'] == $option_value ? ' selected ' : '';
                    $options .= sprintf( '<option value="%s" %s>%s</option>', $option_value, $selected, $option_title );
                }

                $input = sprintf( '<select name="%s" id="%s" class="%s">%s</select>',
                    $name,
                    $name,
                    $class,
                    $options
                );
                break;

            case 'switch':

                $options   = '';
                $separator = '<div class="switch-separator"></div>';

                foreach ( $field['options'] as $option_value => $option_title ) {
                    $options .= sprintf( '<div class="switch-option" data-value="%s">%s</div><input type="radio" name="%s"  value="%s" style="display: none"/>',
                        $option_value,
                        $option_title,
                        $option_value,
                        $option_value
                    );
                }

                $input = sprintf( '<div class="switch" data-value="%s" id="%s">%s%s</div>',
                    $field['value'],
                    $name,
                    $options,
                    $separator
                );

                break;
            case 'textarea':
                $input = sprintf( '<textarea type="%s" name="%s" id="%s" placeholder="%s" class="%s" rows="10" cols="50">%s</textarea>',
                    $field['type'],
                    $name,
                    $name,
                    $field['placeholder'],
                    $class,
                    $field['value']
                );

                break;
            default:
                $input = '';
                break;
        }

        if ( $admin ) {
            return self::admin_group( $label, $input );
        }

        return self::group( $label, $input );
    }

    /**
     * Creates a group of input field
     *
     * Add label and input field by a grouped container
     *
     * Applicable for frotend usages
     *
     * @param  string   $label
     * @param  string   $field
     * @return string
     */
    public static function group( $label, $field ) {
        return sprintf( '<div class="input-group">%s%s</div>', $label, $field );
    }

    /**
     * Creates a group of input
     *
     * Add label and input field by a grouped container|table
     *
     * Applicable for admin|dashboard side usages
     *
     * @param  string $label
     * @param  string $field
     * @return void
     */
    public static function admin_group( $label, $field ) {
        return sprintf( '<table class="form-table"><tr><th>%s</th><td>%s</td></tr></table>', $label, $field );
    }

    /**
     * Merges the defaults values of a input field
     *
     * @param  [type] $field
     * @return void
     */
    public static function prepare_single_field( $field ) {
        $self     = new self();
        $defaults = [
            'title'       => __( 'Input field', $self->__ ),
            'type'        => 'text',
            'data_type'   => 'longtext',
            'required'    => false,
            'options'     => [],
            'class'       => [],
            'value'       => '',
            'placeholder' => '',
        ];

        return wp_parse_args( $field, $defaults );
    }

    /**
     * Creates a switch element from schema
     *
     * @param  string   $name
     * @param  array    $values
     * @return string
     */
    public static function create_switch( $name, $values ) {
        $label = sprintf( '<label for="%s">%s</label>', $name, $values['label'] );

        $options = '';

        foreach ( $values['options'] as $option_value => $option_title ) {
            $options .= sprintf( '<div class="switch-option" data-value="%s">%s</div>',
                $option_value,
                $option_title
            );
        }

        $input = sprintf( '<div class="switch" data-value="%s" id="%s">%s</div>',
            $values['value'],
            $name,
            $name,
            $options
        );

        return sprintf( '<div class="switch-wrapper">%s%s</div>', $label, $input );
    }

    public static function submit( Array $args = [], bool $admin = true ) {
        $self     = new self();
        $defaults = [
            'id'    => 'comparrot-submit-button',
            'label' => __( 'Submit', $self->__ ),
            'class' => [],
        ];
        $args = wp_parse_args( $args, $defaults );

        $button = sprintf( '<div id="%s" class="comparrot-submit-button button button-large %s"><div class="loader"></div>%s</div>', $args['id'], implode( ' ', $args['class'] ), $args['label'] );

        if ( $admin ) {
            return sprintf( '<table class="form-table"><tr><th></th><td>%s</td></tr></table>', $button );
        }

        return $button;
    }

}