<?php
namespace Comparrot\Schema;

class CSV {
    use \Comparrot\Traits\File;

    function __construct() {

    }

    /**
     * Converts a CSV file to array
     *
     * @param  array          $file
     * @param  string         $delimiter
     * @return array|object
     */
    public static function file_to_array( $file ) {

        if ( empty( $file ) ) {
            return false;
        }

        $result = [];
        $file   = fopen( $file['tmp_name'], 'r' );

        while (  ( $line = fgetcsv( $file ) ) !== FALSE ) {
            $result[] = $line;
        }

        fclose( $file );

        return $result;
    }

    /**
     * Returns the first csv file
     *
     * @return void
     */
    public static function get_the_csv_file() {
        $files = self::get_uploaded_list();

        if ( empty( $files ) ) {
            return false;
        }

        return $files[0];
    }

    /**
     * Collects CSV files list from upload
     *
     * @return array|object
     */
    public static function get_uploaded_list() {
        $files = $_FILES;

        foreach ( $files as $file ) {
            if ( self::file_ext( $file ) !== 'csv' ) {
                unset( $files[$file] );
            }
        }

        return $files;
    }

    /**
     * Sanitizes CSV files
     *
     * Basically find outs CSV file from random files list
     *
     * @param  array|object   $files
     * @return array|object
     */
    public static function sanitize_files( $files ) {
        $result = [];
        foreach ( $files as $file ) {
            if ( ! self::file_ext( $file ) == 'csv' ) {
                continue;
            }

            $result[] = $file;
        }

        return $result;
    }

    /**
     * Replaces indexed array to column name by Schema
     *
     * @param  array|object   $schema
     * @param  array|object   $rows
     * @return array|object
     */
    public static function merge_with_column_name( $schema, $rows ) {
        $result = [];

        foreach ( $rows as $row ) {
            $j       = 0;
            $tmp_row = [];
            foreach ( $schema as $key => $value ) {
                $tmp_row[$key] = $row[$j];
                $j++;
            }
            $result[] = $tmp_row;
        }

        return $result;

    }

    /**
     * Converts a list of CSV files to a level 1 array
     *
     * @param  string         $schema_name
     * @param  array|object   $files
     * @return array|object
     */
    public static function get_data_from_files( $schema_name, $files ) {
        $schema = self::schema()::get( $schema_name );
        $result = [];

        // Convert files to array
        foreach ( $files as $file ) {
            $result[] = self::file_to_array( $file );
        }
        // Merge array level
        $rows = [];

        foreach ( $result as $file ) {
            foreach ( $file as $row ) {
                $rows[] = $row;
            }
        }

        return self::merge_with_column_name( $schema, $rows );
    }

    /**
     * Creates a blank CSV template for writing
     *
     * @param  string   $schema_name
     * @return string
     */
    public static function create_template( $schema_name ) {
        $file   = fopen( __DIR__ . "/tmp/comparrot_csv_template.csv", "w" );
        $cols   = [];
        $schema = self::schema()::get( $schema_name );

        foreach ( $schema as $col ) {
            $cols[] = $col['label'];
        }

        fputcsv( $file, $cols );
        fclose( $file );

        return plugins_url( '/includes/Schema/tmp/comparrot_csv_template.csv', COMPARROT_FILE );
    }

    /**
     * Returns an instance of Schema class
     *
     * @return mixed
     */
    public static function schema() {
        return new Schema();
    }

    /**
     * Returns an instance of CRUD class
     *
     * @return mixed
     */
    public static function CRUD() {
        return new CRUD();
    }
}