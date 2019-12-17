<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models;
/**
 * Modules
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\modules\WordPress_Module as WordPress_Module;
//use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\modules\Nginx_Module as Nginx_Module;
//use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\modules\Apache_Module as Apache_Module;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\Helpers\Redirection_Helper as Redirection_Helper;

abstract class Aios_Module {
	public function __construct( $options ) {
            if ( is_array( $options ) )
                    $this->load( $options );
	}

	static function get( $id ) {
		$id = intval( $id );
		$options = Redirection_Helper::aios_get_options();

//		if ( $id === Apache_Module::MODULE_ID )
//			return new Apache_Module( isset( $options['modules'][ Apache_Module::MODULE_ID ] ) ? $options['modules'][ Apache_Module::MODULE_ID ] : array() );
		if ( $id === WordPress_Module::MODULE_ID )
			return new WordPress_Module( isset( $options['modules'][ WordPress_Module::MODULE_ID ] ) ? $options['modules'][ WordPress_Module::MODULE_ID ] : array() );
//		else if ( $id === Nginx_Module::MODULE_ID )
//			return new Nginx_Module( isset( $options['modules'][ Nginx_Module::MODULE_ID ] ) ? $options['modules'][ Nginx_Module::MODULE_ID ] : array() );

		return false;
	}

	public function get_total_redirects() {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}aios_redirection_items INNER JOIN {$wpdb->prefix}aios_redirection_groups ON {$wpdb->prefix}aios_redirection_items.group_id={$wpdb->prefix}aios_redirection_groups.id WHERE {$wpdb->prefix}aios_redirection_groups.module_id=%d", $this->get_id() ) );
	}

	static public function is_valid_id( $id ) {
		if ( $id === Apache_Module::MODULE_ID || $id === WordPress_Module::MODULE_ID )
			return true;
		return false;
	}

	static function get_for_select() {
		$options = red_get_options();

		return array(
			WordPress_Module::MODULE_ID => self::get( WordPress_Module::MODULE_ID ),
			Apache_Module::MODULE_ID    => self::get( Apache_Module::MODULE_ID ),
			Nginx_Module::MODULE_ID     => Nginx_Module::get( Nginx_Module::MODULE_ID ),
		);
	}

	static function flush( $group_id ) {
		$group = Aios_Group::get( $group_id );

		if ( $group ) {
			$module = self::get( $group->get_module_id() );

			if ( $module )
				$module->flush_module();
		}
	}

	static function flush_by_module( $module_id ) {
		$module = self::get( $module_id );

		if ( $module )
			$module->flush_module();
	}

	abstract public function get_id();
	abstract public function get_name();
	abstract public function get_description();

	abstract public function render_config();
	abstract public function get_config();
	abstract public function can_edit_config();
	abstract public function update( $options );

	abstract protected function load( $options );
	abstract protected function flush_module();
}
