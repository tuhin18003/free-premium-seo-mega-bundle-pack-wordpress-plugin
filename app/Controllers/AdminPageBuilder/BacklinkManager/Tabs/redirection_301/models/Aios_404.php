<?php namespace CsSeoMegaBundlePack\Controllers\AdminPageBuilder\BacklinkManager\Tabs\redirection_301\models;
/**
 * 404 Error Handler Class
 * 
 * @package Backlink Manager
 * @since 1.0.0
 * @author CodeSolz <customer-service@codesolz.net>
 */

class Aios_404{
	public $id;
	public $created;
	public $url;
	public $agent;
	public $referrer;
	public $ip;

	function __construct( $values ) {
		foreach ( $values as $key => $value ) {
		 	$this->$key = $value;
		}

		$this->created = mysql2date( 'U', $this->created );
	}

	static function get_by_id( $id ) {
		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}aios_redirection_404 WHERE id=%d", $id ) );
		if ( $row )
			return new RE_404( $row );
		return false;
	}

	static function create( $url, $agent, $ip, $referrer ) {
		global $wpdb, $redirection;

		$insert = array(
			'url'     => urldecode( $url ),
			'created' => current_time( 'mysql' ),
			'ip'      => ip2long( $ip ),
		);

		if ( ! empty( $agent ) )
			$insert['agent'] = $agent;

		if ( ! empty( $referrer ) )
			$insert['referrer'] = $referrer;

		$wpdb->insert( $wpdb->prefix.'aios_redirection_404', $insert );
	}

	static function delete( $id ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}aios_redirection_404 WHERE id=%d", $id ) );
	}

	static function delete_all() {
		global $wpdb;

		$where = array();
		if ( isset( $_REQUEST['s'] ) )
			$where[] = $wpdb->prepare( 'url LIKE %s', '%'.$wpdb->esc_like( $_REQUEST['s'] ).'%' );

		$where_cond = '';
		if ( count( $where ) > 0 )
			$where_cond = ' WHERE '.implode( ' AND ', $where );

		$wpdb->query( "DELETE FROM {$wpdb->prefix}aios_redirection_404 ".$where_cond );
	}

	static function export_to_csv() {
		global $wpdb;

		$filename = 'redirection-log-'.date_i18n( get_option( 'date_format' ) ).'.csv';

		header( 'Content-Type: text/csv' );
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-Disposition: attachment; filename="'.$filename.'"' );

		$stdout = fopen( 'php://output', 'w' );

		fputcsv( $stdout, array( 'date', 'source', 'ip', 'referrer' ) );

		$extra = '';
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aios_redirection_404";
		if ( isset( $_REQUEST['s'] ) )
			$extra = $wpdb->prepare( ' WHERE url LIKE %s', '%'.$wpdb->esc_like( $_REQUEST['s'] ).'%' );

		$total_items = $wpdb->get_var( $sql.$extra );
		$exported = 0;

		while ( $exported < $total_items ) {
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}aios_redirection_404 LIMIT %d,%d", $exported, 100 ) );
			$exported += count( $rows );

			foreach ( $rows as $row ) {
				$csv = array(
					$row->created,
					$row->url,
					long2ip( $row->ip ),
					$row->referrer,
				);

				fputcsv( $stdout, $csv );
			}

			if ( count( $rows ) < 100 )
				break;
		}
	}
}
