<?php
/**
 * 301 Redirection Frontend engagement
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Module as Aios_Module;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\modules\WordPress_Module as WordPress_Module;
use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Flusher as Aios_Flusher;

class Cs_Frontend_301_Engagement {
	private static $instance = null;
	private $module;

	static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Cs_Frontend_301_Engagement();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->module = Aios_Module::get( WordPress_Module::MODULE_ID );
		$this->module->start();

		add_action( Aios_Flusher::DELETE_HOOK, array( $this, 'cs_clean_redirection_logs' ) );
	}

	public function cs_clean_redirection_logs() {
		$flusher = new Aios_Flusher();
		$flusher->flush();
	}
}

add_action( 'plugins_loaded', array( 'Cs_Frontend_301_Engagement', 'init' ) );