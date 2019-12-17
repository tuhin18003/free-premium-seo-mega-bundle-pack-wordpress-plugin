<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\actions;
/**
 * Error Actions
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

use CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models\Aios_Action as Aios_Action;

class Error_Action extends Aios_Action {
	function can_change_code() {
		return true;
	}

	function can_perform_action() {
		return false;
	}

	function action_codes() {
		return array(
			404 => get_status_header_desc( 404 ),
			410 => get_status_header_desc( 410 ),
		);
	}

	function process_after( $code, $target ) {
		global $wp_query;
		$wp_query->is_404 = true;

		// Page comments plugin interferes with this
		remove_filter( 'template_redirect', 'paged_comments_alter_source', 12 );
	}
}
