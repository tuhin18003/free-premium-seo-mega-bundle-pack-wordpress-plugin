<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models;
/**
 * Redirect
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Log as Aios_Log;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Module as Aios_Module;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Match as Aios_Match;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Action as Aios_Action;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\modules\WordPress_Module as WordPress_Module;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\Helpers\Redirection_Helper as Redirection_Helper;

class Aios_Redirect {
	private $id          = null;
	private $created;
	private $referrer;
	private $url         = null;
	private $regex       = false;
	private $action_data = null;
	private $action_code = 0;
	private $action_type;
	private $match_type;
	private $description;
	private $last_access = null;
	private $last_count  = 0;
	private $tracking    = true;
	private $status;
	private $position;
	private $group_id;

	function __construct( $values, $type = '', $match = '' ) {
		if ( is_object( $values ) ) {
			foreach ( $values as $key => $value ) {
			 	$this->$key = $value;
			}

			if ( $this->match_type === '' ) {
				$this->match_type = 'url';
			}

			$this->regex = (bool)$this->regex;
			$this->match              = Aios_Match::get_avaliable_class( $this->match_type, $this->action_data );
			$this->match->id          = $this->id;
			$this->match->action_code = $this->action_code;

			$action = false;

			if ( $this->action_type ) {
				$action = Aios_Action::create( $this->action_type, $this->action_code );
			}

			if ( $action ) {
				$this->action = $action;
				$this->match->action = $this->action;
			}
			else
				$this->action = Aios_Action::create( 'nothing', 0 );

			if ( $this->last_access === '0000-00-00 00:00:00' )
				$this->last_access = 0;
			else
				$this->last_access = mysql2date( 'U', $this->last_access );
		}
		else {
			$this->url   = $values;
			$this->type  = $type;
			$this->match = $match;
		}
	}

	static function get_all_for_module( $module ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT {$wpdb->prefix}aios_redirection_items.*,{$wpdb->prefix}aios_redirection_groups.tracking FROM {$wpdb->prefix}aios_redirection_items INNER JOIN {$wpdb->prefix}aios_redirection_groups ON {$wpdb->prefix}aios_redirection_groups.id={$wpdb->prefix}aios_redirection_items.group_id AND {$wpdb->prefix}aios_redirection_groups.status='enabled' AND {$wpdb->prefix}aios_redirection_groups.module_id=%d WHERE {$wpdb->prefix}aios_redirection_items.status='enabled' ORDER BY {$wpdb->prefix}aios_redirection_groups.position,{$wpdb->prefix}aios_redirection_items.position", $module );

		$rows  = $wpdb->get_results( $sql );
		$items = array();
		if ( count( $rows ) > 0 ) {
			foreach ( $rows as $row ) {
				$items[] = new Aios_Redirect( $row );
			}
		}

		return $items;
	}

	static function get_for_url( $url, $type ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT {$wpdb->prefix}aios_redirection_items.*,{$wpdb->prefix}aios_redirection_groups.position AS group_pos FROM {$wpdb->prefix}aios_redirection_items INNER JOIN {$wpdb->prefix}aios_redirection_groups ON {$wpdb->prefix}aios_redirection_groups.id={$wpdb->prefix}aios_redirection_items.group_id AND {$wpdb->prefix}aios_redirection_groups.status='enabled' AND {$wpdb->prefix}aios_redirection_groups.module_id=%d WHERE ({$wpdb->prefix}aios_redirection_items.regex=1 OR {$wpdb->prefix}aios_redirection_items.url=%s)", WordPress_Module::MODULE_ID, $url );

		$rows = $wpdb->get_results( $sql );
		$items = array();
		if ( count( $rows ) > 0 ) {
			foreach ( $rows as $row ) {
				$items[] = array( 'position' => ( $row->group_pos * 1000 ) + $row->position, 'item' => new Aios_Redirect( $row ) );
			}
		}

		usort( $items, 'self::sort_urls' );
		$items = array_map( 'self::reduce_sorted_items', $items );

		// Sort it in PHP
		ksort( $items );
		$items = array_values( $items );
		return $items;
	}

	static function sort_urls( $first, $second ) {
		if ( $first['position'] === $second['position'] )
			return 0;

		return ($first['position'] < $second['position']) ? -1 : 1;
	}

	static  function reduce_sorted_items( $item ) {
		return $item['item'];
	}

	static function get_by_module( $module ) {
		global $wpdb;

		$sql = "SELECT {$wpdb->prefix}aios_redirection_items.* FROM {$wpdb->prefix}aios_redirection_items INNER JOIN {$wpdb->prefix}aios_redirection_groups ON {$wpdb->prefix}aios_redirection_groups.id={$wpdb->prefix}aios_redirection_items.group_id";
		$sql .= $wpdb->prepare( " WHERE {$wpdb->prefix}aios_redirection_groups.module_id=%d", $module );

		$rows = $wpdb->get_results( $sql );
		$items = array();

		foreach ( (array) $rows as $row ) {
			$items[] = new Aios_Redirect( $row );
		}

		return $items;
	}

        /**
         * Get Redirection Row by id
         * 
         * @global $wpdb
         * @param array $user_input
         * @return array
         */
	static function get_by_id( $id ) {
		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}aios_redirection_items WHERE id=%d", $id ) );
		if ( $row )
			return new Aios_Redirect( $row );
		return false;
	}

        /**
         * Add New Redirection
         * 
         * @global $wpdb
         * @param array $user_input
         * @return array
         */
	static function add_new_redirects( array $user_input ) {
		global $wpdb;

		// Auto generate URLs
                if ( empty( $user_input['source_url'] ) )
                        $user_input['source_url'] = Redirection_Helper::auto_generate();

                if ( empty( $user_input['destination_url'] ) )
                        $user_input['destination_url'] = Redirection_Helper::auto_generate();

		// Make sure we don't redirect to ourself
                if ( $user_input['source_url'] === $user_input['destination_url'] ){
                    return $json_data = array(
                        'error' => true,
                        'title' => __( 'Error!', SR_TEXTDOMAIN ),
                        'response_text' => __( 'Source and destination url URL must be different', SR_TEXTDOMAIN )
                    );
                }

		$parsed_url = parse_url( $user_input['source_url'] );
                $parsed_domain = parse_url( site_url() );
                if ( isset( $parsed_url['scheme'] ) && ( $parsed_url['scheme'] === 'http' || $parsed_url['scheme'] === 'https' ) && $parsed_url['host'] !== $parsed_domain['host'] ) {
                    return $json_data = array(
                        'error' => true,
                        'title' => __( 'Error!', SR_TEXTDOMAIN ),
                        'response_text' => __( sprintf( __( 'You can only redirect from a relative URL (<code>%s</code>) on this domain (<code>%s</code>).', SR_TEXTDOMAIN ), $parsed_url['path'], $parsed_domain['host'] ), SR_TEXTDOMAIN )
                    );
                }

		$group_id = intval( $user_input['group_id'] );
        $regex    = ( isset( $user_input['regex'] ) && (bool) $user_input['regex'] !== false ) ? 1 : 0;
        $position = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}aios_redirection_items WHERE group_id=%d", $group_id ) );
        
        $action = $user_input['action_type'];
        $action_code = 0;
        if ( $action === 'url' || $action === 'random' )
                $action_code = 301;
        elseif ( $action === 'error' )
                $action_code = 404;
        
        $data = array(
                'url'         => Redirection_Helper::sanitize_url( $user_input['source_url'], $regex ),
                'action_type' => $user_input['action_type'],
                'regex'       => $regex,
                'position'    => $position,
                'match_type'  => $user_input['match_type'],
                'action_data' => $user_input['destination_url'],
                'action_code' => $action_code,
                'last_access' => '0000-00-00 00:00:00',
                'group_id'    => $group_id,
                'description'    => $user_input['description'],
        );

            $data = apply_filters( 'aios_redirection_create_redirect', $data );
            $wpdb->delete( $wpdb->prefix.'aios_redirection_items', array( 'url' => $data['action_data'], 'action_type' => $data['action_type'], 'action_data' => $data['url'] ) );
            if ( $wpdb->insert( $wpdb->prefix.'aios_redirection_items', $data ) ) {
                
                    Aios_Module::flush( $group_id );
                    return $json_data = array(
                        'error' => false,
                        'title' => __( 'Success!', SR_TEXTDOMAIN ),
                        'response_text' => __( 'Your redirection has been added successfully.', SR_TEXTDOMAIN )
                    );
            }
            
            return $json_data = array(
                'error' => true,
                'title' => __( 'Error!', SR_TEXTDOMAIN ),
                'response_text' => __( 'Unable to add new redirect - delete Redirection from the options page and re-install', SR_TEXTDOMAIN )
            );
	}

        /**
         * Update Redirects Rule
         * 
         * @version 1.0.0
         * @since 1.0.0
         * @global obj $wpdb
         * @param array $user_input
         * @return boolean
         */
	public function update_redirect( $user_input ) {
            if ( strlen( $user_input['rule_id'] ) > 0 ) {
                    global $wpdb;
                    $user_input = array_map( 'stripslashes', $user_input );
                    $this->regex = isset( $user_input['regex'] ) ? 1 : 0;
                    $this->url   = Redirection_Helper::sanitize_url( $user_input['source_url'], $this->regex );
                    $this->description = $user_input['description'];
                    $data = Redirection_Helper::sanitize_url($user_input['destination_url']);
                    $this->action_code = 0;
                    $action = $user_input['action_type'];
                    if ( $action === 'url' || $action === 'random' )
                            $this->action_code = 301;
                    elseif ( $action === 'error' )
                            $this->action_code = 404;

                    $old_group = false;
                    if ( isset( $user_input['group_id'] ) ) {
                            $old_group = intval( $this->group_id );
                            $this->group_id = intval( $user_input['group_id'] );
                    }

                    // Update data
                    $wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'url' => $this->url, 'regex' => $this->regex, 'action_type' => $action, 'action_code' => $this->action_code, 'action_data' => $data, 'group_id' => $this->group_id, 'description' => $this->description ), array( 'id' => $this->id ) );

//                    if ( $old_group !== $this->group_id ) {
                            Aios_Module::flush( $this->group_id );
                            Aios_Module::flush( $old_group );
//                    }

                return true;    
            }
            return false;
	}
        
        /**
         * Delete Redirect Rules
         * 
         * @version 1.0.0
         * @since 1.0.0
         * @global obj $wpdb
         * @return boolean
         */
	public function delete_redirect() {
            global $wpdb;
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}aios_redirection_items WHERE id=%d", $this->id ) );
            Aios_Log::delete_for_id( $this->id );
            // Reorder all elements
            $rows = $wpdb->get_results( "SELECT id FROM {$wpdb->prefix}aios_redirection_items ORDER BY position" );
            if ( count( $rows ) > 0 ) {
                    foreach ( $rows as $pos => $row ) {
                            $wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'position' => $pos ), array( 'id' => $row->id ) );
                    }
            }
            Aios_Module::flush( $this->group_id );
            
            return true;
	}

	static function save_order( $items, $start ) {
		global $wpdb;

		foreach ( $items as $pos => $id ) {
			$wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'position' => $pos + $start ), array( 'id' => $id ) );
		}

		Red_Module::flush( $this->group_id );
	}

	function matches( $url ) {
		$this->url = str_replace( ' ', '%20', $this->url );
		$matches   = false;

		// Check if we match the URL
		if ( ( $this->regex === false && ( $this->url === $url || $this->url === rtrim( $url, '/' ) || $this->url === urldecode( $url ) ) ) || ( $this->regex === true && @preg_match( '@'.str_replace( '@', '\\@', $this->url ).'@', $url, $matches ) > 0) || ( $this->regex === true && @preg_match( '@'.str_replace( '@', '\\@', $this->url ).'@', urldecode( $url ), $matches ) > 0) ) {
			// Check if our match wants this URL
			$target = $this->match->get_target( $url, $this->url, $this->regex );

			if ( $target ) {
				$target = $this->replace_special_tags( $target );

				$this->visit( $url, $target );

				if ( $this->status === 'enabled' )
					return $this->action->process_before( $this->action_code, $target );
			}
		}

		return false;
	}

	function replace_special_tags( $target ) {
		if ( is_numeric( $target ) )
			$target = get_permalink( $target );
		else {
			$user = wp_get_current_user();
			if ( ! empty( $user ) ) {
				$target = str_replace( '%userid%', $user->ID, $target );
				$target = str_replace( '%userlogin%', isset( $user->user_login ) ? $user->user_login : '', $target );
				$target = str_replace( '%userurl%', isset( $user->user_url ) ? $user->user_url : '', $target );
			}
		}

		return $target;
	}

	function visit( $url, $target ) {
		if ( $this->tracking && $this->id ) {
			global $wpdb;

			// Update the counters
			$count = $this->last_count + 1;
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}aios_redirection_items SET last_count=%d, last_access=NOW() WHERE id=%d", $count, $this->id ) );

			if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
			  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			elseif ( isset( $_SERVER['REMOTE_ADDR'] ) )
			  $ip = $_SERVER['REMOTE_ADDR'];

			$options = Redirection_Helper::aios_get_options();
			if ( isset( $options['expire_redirect'] ) && $options['expire_redirect'] >= 0 )
				$log = Aios_Log::create( $url, $target, $_SERVER['HTTP_USER_AGENT'], $ip, isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '', array( 'redirect_id' => $this->id, 'group_id' => $this->group_id ) );
		}
	}

	public function is_enabled() {
		return $this->status === 'enabled';
	}

        /**
         * Reset Hits Counter
         * 
         * @version 1.0.0
         * @since 1.0.0
         * @global obj $wpdb
         * @return boolean
         */
	function reset() {
            global $wpdb;
            $this->last_count  = 0;
            $this->last_access = '0000-00-00 00:00:00';
            $wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'last_count' => 0, 'last_access' => $this->last_access ), array( 'id' => $this->id ) );
            Aios_Log::delete_for_id( $this->id );
            return true;
	}

        /**
         * Enable Redirect Rule
         * 
         * @version 1.0.0
         * @since 1.0.0
         * @global obj $wpdb
         * @return boolean
         */
	public function enable() {
            global $wpdb;
            $this->status = 'enabled';
            $wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'status' => $this->status ), array( 'id' => $this->id ) );
            return true;
	}

        /**
         * Disable Redirect Rule
         * 
         * @version 1.0.0
         * @since 1.0.0
         * @global obj $wpdb
         * @return boolean
         */
	public function disable() {
            global $wpdb;
            $this->status = 'disabled';
            $wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'status' => $this->status ), array( 'id' => $this->id ) );
            return true;
	}

	static function actions( $action = '' ) {
		$actions = array(
			'url'     => __( 'Redirect to URL', SR_TEXTDOMAIN ),
			'random'  => __( 'Redirect to random post', SR_TEXTDOMAIN ),
			'pass'    => __( 'Pass-through', SR_TEXTDOMAIN ),
			'error'   => __( 'Error (404)', SR_TEXTDOMAIN ),
			'nothing' => __( 'Do nothing', SR_TEXTDOMAIN ),
		);

		if ( $action )
			return $actions[ $action ];
		return $actions;
	}

	function match_name() {
		return $this->match->match_name();
	}

	function type() {
		if ( ( $this->action_type === 'url' || $this->action_type === 'error' || $this->action_type === 'random' ) && $this->action_code > 0 )
			return $this->action_code;
		else if ( $this->action_type === 'pass' )
			return 'pass';
		return '&mdash;';
	}

	public function get_id() {
		return $this->id;
	}

	public function get_position() {
		return $this->position;
	}

	public function get_group_id() {
		return $this->group_id;
	}

	public function get_url() {
		return $this->url;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_hits() {
		return $this->last_count;
	}

	public function get_last_hit() {
		return $this->last_access;
	}

	public function is_regex() {
		return $this->regex ? true : false;
	}

	public function get_match_type() {
		return $this->match_type;
	}

	public function get_action_type() {
		return $this->action_type;
	}

	public function get_action_code() {
		return intval( $this->action_code );
	}

	public function get_action_data() {
		return $this->action_data;
	}
}
