<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models;
/**
 * Group
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Module as Aios_Module;

class Aios_Group {
	private $items = 0;
	private $name;
	private $tracking;
	private $module_id;
	private $status;
	private $position;

	public function __construct( $values = ''  ) {
		if ( is_object( $values ) ) {
			foreach ( $values as $key => $value ) {
			 	$this->$key = $value;
			}
		}
	}

	public function get_name() {
		return $this->name;
	}

	public function get_id() {
		return $this->id;
	}

	public function is_enabled() {
		return $this->status === 'enabled' ? true : false;
	}

	static function get( $id ) {
		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT {$wpdb->prefix}aios_redirection_groups.*,COUNT( {$wpdb->prefix}aios_redirection_items.id ) AS items,SUM( {$wpdb->prefix}aios_redirection_items.last_count ) AS redirects FROM {$wpdb->prefix}aios_redirection_groups LEFT JOIN {$wpdb->prefix}aios_redirection_items ON {$wpdb->prefix}aios_redirection_items.group_id={$wpdb->prefix}aios_redirection_groups.id WHERE {$wpdb->prefix}aios_redirection_groups.id=%d GROUP BY {$wpdb->prefix}aios_redirection_groups.id", $id ) );
		if ( $row )
			return new Aios_Group( $row );
		return false;
	}

	static function get_for_select() {
		global $wpdb;

		$data = array();
		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}aios_redirection_groups" );

		if ( $rows ) {
                    foreach ( $rows as $row ) {
                        $module = Aios_Module::get( $row->module_id );
                        if ( $module ) {
                                $data[ $module->get_name() ][ $row->id ] = $row->name;
                        }
                    }
		}

		return $data;
	}

        /**
         * Create new group
         * 
         * @global object $wpdb
         * @param type $name
         * @param type $module_id
         * @return boolean
         */
	static function create( $name, $module_id ) {
		global $wpdb;

		$name = trim( $name );

		if ( $name !== '' && $module_id > 0 ) {
			$position = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM {$wpdb->prefix}aios_redirection_groups WHERE module_id=%d", $module_id ) );

			$data = array(
				'name'      => trim( $name ),
				'module_id' => intval( $module_id ),
				'position'  => intval( $position ),
			);

			$wpdb->insert( $wpdb->prefix.'aios_redirection_groups', $data );

			return Aios_Group::get( $wpdb->insert_id );
		}

		return false;
	}

	public function update( $data ) {
		global $wpdb;

		$old_id = $this->module_id;
		$this->name = trim( wp_kses( stripslashes( $data['name'] ), array() ) );

		if ( Aios_Module::is_valid_id( intval( $data['module_id'] ) ) )
			$this->module_id = intval( $data['module_id'] );

		$wpdb->update( $wpdb->prefix.'aios_redirection_groups', array( 'name' => $this->name, 'module_id' => $this->module_id ), array( 'id' => intval( $this->id ) ) );

		if ( $old_id !== $this->module_id ) {
			Aios_Module::flush_by_module( $old_id );
			Aios_Module::flush_by_module( $this->module_id );
		}
	}

        /**
         * Delete Group
         * 
         * @global object $wpdb
         * @param array $group_id_arr
         */
	public function delete() {
		global $wpdb;

		// Delete all items in this group
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}aios_redirection_items WHERE group_id=%d", $this->id ) );

		Aios_Module::flush( $this->id );

		// Delete the group
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}aios_redirection_groups WHERE id=%d", $this->id ) );

		if ( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}aios_redirection_groups" ) === 0 )
			$wpdb->insert( $wpdb->prefix.'aios_redirection_groups', array( 'name' => __( 'Redirections' ), 'module_id' => 1, 'position' => 0 ) );
                
                return true;
	}

        /**
         * Enable Group
         * 
         * @global object $wpdb
         * @param array $group_id_arr
         * @return boolean
         */
	public static function enable( $group_id_arr ) {
            global $wpdb;
            if($group_id_arr){
                foreach($group_id_arr as $group_id){
                    $wpdb->update( $wpdb->prefix.'aios_redirection_groups', array( 'status' => 'enabled' ), array( 'id' => $group_id ) );
                    $wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'status' => 'enabled' ), array( 'group_id' => $group_id ) );

                    Aios_Module::flush( $group_id );
                }
            }
            return false;
	}

        /**
         * Disable Group
         * 
         * @global object $wpdb
         * @param array $group_id_arr
         * @return boolean
         */
	public static function disable( $group_id_arr ) {
            global $wpdb;
            if($group_id_arr){
                foreach($group_id_arr as $group_id){
                    $wpdb->update( $wpdb->prefix.'aios_redirection_groups', array( 'status' => 'disabled' ), array( 'id' => $group_id ) );
                    $wpdb->update( $wpdb->prefix.'aios_redirection_items', array( 'status' => 'disabled' ), array( 'group_id' => $group_id ) );

                    Aios_Module::flush( $group_id );
                }
            }
            return false;
	}

	public function get_module_id() {
		return $this->module_id;
	}
}
