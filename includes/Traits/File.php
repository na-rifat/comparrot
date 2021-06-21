<?php
namespace Comparrot\Traits;

trait File {
    use Helper;
    public static function file_ext( $file ) {
        // return strtolower(explode('.', $file['name']));
        return strtolower( explode( '.', $file['name'] )[sizeof( explode( '.', $file['name'] ) ) - 1] );
    }

    public static function get_files_by_ext( Array $files, Array $ext ) {
        $result = [];

        for ( $i = 0; $i < sizeof( $files['name'] ); $i++ ) {

            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];

            $file['ext'] = self::file_ext( $file );

            if ( in_array( $file['ext'], $ext ) ) {
                $result[] = $file;
            }
        }

        return $result;
    }

    public static function image_file_info( $file ) {
        $result    = [];
        $timestamp = time();

        $result['tmp_name']  = $file['tmp_name'];
        $result['real_name'] = $file['name'];
        $result['type']      = $file['type'];
        $result['size']      = $file['size'];
        $result['ext']       = strtolower( explode( '.', $file['name'] )[sizeof( explode( '.', $file['name'] ) ) - 1] );
        $result['name']      = explode( '.', $file['name'] )[0];

        return $result;
    }

    public static function make_image_key( $files ) {        
        $result = [];

        foreach ( $files as $file ) {
            $result[$file['name']] = $file;
        }

        return $result;
    }

}