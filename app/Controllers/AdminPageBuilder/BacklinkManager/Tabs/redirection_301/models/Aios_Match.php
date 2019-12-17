<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models;
/**
 * Matching
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */


class Aios_Match {
	public $url;

	function __construct( $values = '' ) {
            if ( $values ) {
                $this->url = $values;

                $obj = maybe_unserialize( $values );

                if ( is_array( $obj ) ) {
                    foreach ( $obj as $key => $value ) {
                        $this->$key = $value;
                    }
                }
            }
	}

	function data( $details ) {
		$data = $this->save( $details );
		if ( count( $data ) === 1 && ! is_array( current( $data ) ) )
			$data = current( $data );
		else
			$data = serialize( $data );
		return $data;
	}

	function save( $details ) {
		return array();
	}

	function name() {
		return '';
	}

	function show() {
	}

	function wants_it() {
		return true;
	}

	function get_target( $url, $matched_url, $regex ) {
		return false;
	}

	function sanitize_url( $url ) {
		// No new lines
		$url = preg_replace( "/[\r\n\t].*?$/s", '', $url );

		// Clean control codes
		$url = preg_replace( '/[^\PC\s]/u', '', $url );

		return $url;
	}

	public static function get_avaliable_class( $name, $data = '' ) {
            $classname = ucfirst( $name ).'_Match';
            $newClassPath = "CsSeoMegaBundlePack\\Controllers\\AdminPageBuilder\\BacklinkManager\\Tabs\\redirection_301\\matches\\" . $classname;
            if( class_exists($newClassPath)){
                return new $newClassPath( $data );
            }else  return false;
	}

        public static function get_match_select(){
            $data = array();
            $avail = self::matching_options();
            foreach ( $avail as $name => $file ) {
                    $obj = self::get_avaliable_class( $name );
                    $data[ $name ] = $obj->name();
            }
            return $data;
        }

	static function matching_options() {
	 	return array(
			'url'      => 'url.php',
			'referrer' => 'referrer.php',
			'agent'    => 'user-agent.php',
			'login'    => 'login.php',
		 );
	}

	function match_name() {
		return '';
	}
}
