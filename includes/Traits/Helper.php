<?php

namespace Comparrot\Traits;

trait Helper {

    public static function reverse_col_names( $core_array, $reverse ) {
        $result = $core_array;

        foreach ( $reverse as $core_col => $col ) {
            if ( isset( $result[$core_col] ) ) {

                $value = $result[$core_col];

                unset( $result[$core_col] );

                $result[$col] = $value;
            }
        }

        return $result;
    }

    public static function inverse_col_names( $core_array, $reverse ) {
        $result = $core_array;

        foreach ( $reverse as $core_col => $col ) {
            if ( isset( $result[$col] ) ) {

                $value = $result[$col];

                unset( $result[$col] );

                $result[$core_col] = $value;
            }
        }

        return $result;
    }

    public static function calculate_meta_fields( $schema, $mandatory_fields ) {
        $result = $schema;

        foreach ( $mandatory_fields as $field ) {
            unset( $result[$field] );
        }

        return $result;
    }

    public static function replace_schema_values( $schema, $values ) {
        $result = [];

        foreach ( $schema as $key => $value ) {
            $result[$key]          = $value;
            $result[$key]['value'] = htmlspecialchars( $values[$key] );
        }

        return $result;
    }
}