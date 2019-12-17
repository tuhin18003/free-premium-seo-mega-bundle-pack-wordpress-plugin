<?php

/** @var  \Herbert\Framework\Application $container */
/** @var  \Herbert\Framework\Http $http */
/** @var  \Herbert\Framework\Router $router */
/** @var  \Herbert\Framework\Enqueue $enqueue */
/** @var  \Herbert\Framework\Panel $panel */
/** @var  \Herbert\Framework\Shortcode $shortcode */
/** @var  \Herbert\Framework\Widget $widget */

use CsSeoMegaBundlePack\Helper;
global $aios_db_version;
$aios_db_version = Helper::get('db_version');

if( !function_exists( 'aios_db_install' ) ){
    /**
     * 
     * @global type $wpdb
     * @global type $aios_db_versionInstall / Update DB Table
     * 
     * @since 1.0.0
     */
    function aios_db_install(){
        global $wpdb;
        global $aios_db_version;
        
        $charset_collate = $wpdb->get_charset_collate();

        $installed_ver = get_option( "aios_db_version" );
//        if ( $installed_ver != $aios_db_version ) {
//            //update sql code goes here
//            
//            update_option( 'aios_db_version', $aios_db_version );
//            
//        }else{
           
            $create_tbl = array(
                
                        "CREATE TABLE `{$wpdb->prefix}csmbp_domains` (
                          `id` bigint(20) UNSIGNED primary key auto_increment,
                          `url` mediumtext,
                          `url_type` tinyint(4),
                          `url_status` smallint(6),
                          `url_group_id` smallint(6),
                          `created_on` datetime
                        ) $charset_collate ",
                        "CREATE TABLE `{$wpdb->prefix}csmbp_groups` (
                          `id` smallint(6) primary key auto_increment,
                          `name` mediumtext,
                          `description` mediumtext,
                          `type` tinyint(4),
                          `created_on` datetime
                        ) $charset_collate",
                        "CREATE TABLE `{$wpdb->prefix}csmbp_keywords` (
                          `id` mediumint(9) primary key auto_increment,
                          `domain_id` bigint(20) UNSIGNED,
                          `keyword` mediumtext,
                          `from_url` mediumtext,
                          `auto_update` tinyint(4)
                        ) $charset_collate",
                        "CREATE TABLE `{$wpdb->prefix}csmbp_keyword_rankings` (
                          `id` int(11) NOT NULL,
                          `keyword_id` mediumint(9) DEFAULT NULL,
                          `current_position` tinyint(4) DEFAULT NULL,
                          `position_increased` tinyint(4) DEFAULT NULL,
                          `position_decreased` tinyint(4) DEFAULT NULL,
                          `created_on` datetime DEFAULT NULL
                        ) $charset_collate",
                                
                                
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_redirection_items`(
				`id` int(11) unsigned NOT NULL auto_increment,
			  `url` mediumtext NOT NULL,
			  `regex` int(11) unsigned NOT NULL default '0',
			  `position` int(11) unsigned NOT NULL default '0',
			  `last_count` int(10) unsigned NOT NULL default '0',
			  `last_access` datetime NOT NULL,
			  `group_id` int(11) NOT NULL default '0',
			  `status` enum('enabled','disabled' ) NOT NULL default 'enabled',
			  `action_type` varchar(20) NOT NULL,
			  `action_code` int(11) unsigned NOT NULL,
			  `action_data` mediumtext,
			  `match_type` varchar(20) NOT NULL,
			  `description` mediumtext NULL,
			  PRIMARY KEY ( `id`),
				KEY `url` (`url`(200)),
			  KEY `status` (`status`),
			  KEY `regex` (`regex`),
				KEY `group_idpos` (`group_id`,`position`),
			  KEY `group` (`group_id`)
			) $charset_collate",

			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_redirection_groups`(
			  `id` int(11) NOT NULL auto_increment,
			  `name` varchar(50) NOT NULL,
			  `tracking` int(11) NOT NULL default '1',
			  `module_id` int(11) unsigned NOT NULL default '0',
		  	`status` enum('enabled','disabled' ) NOT NULL default 'enabled',
		  	`position` int(11) unsigned NOT NULL default '0',
			  PRIMARY KEY ( `id`),
				KEY `module_id` (`module_id`),
		  	KEY `status` (`status`)
			) $charset_collate",

			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_redirection_logs`(
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `created` datetime NOT NULL,
			  `url` mediumtext NOT NULL,
			  `sent_to` mediumtext,
			  `agent` mediumtext NOT NULL,
			  `referrer` mediumtext,
			  `redirection_id` int(11) unsigned default NULL,
			  `ip` varchar(17) NOT NULL default '',
			  `module_id` int(11) unsigned NOT NULL,
				`group_id` int(11) unsigned default NULL,
			  PRIMARY KEY ( `id`),
			  KEY `created` (`created`),
			  KEY `redirection_id` (`redirection_id`),
			  KEY `ip` (`ip`),
			  KEY `group_id` (`group_id`),
			  KEY `module_id` (`module_id`)
			) $charset_collate",

			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_redirection_404` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `created` datetime NOT NULL,
			  `url` varchar(255) NOT NULL DEFAULT '',
			  `agent` varchar(255) DEFAULT NULL,
			  `referrer` varchar(255) DEFAULT NULL,
			  `ip` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `created` (`created`),
			  KEY `url` (`url`),
			  KEY `ip` (`ip`),
			  KEY `referrer` (`referrer`)
		  	) $charset_collate;",
                                
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_internal_link_groups` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                          `name` varchar(100) DEFAULT NULL,
                          `status` varchar(1) DEFAULT NULL,
                          `created_on` datetime DEFAULT NULL
                        ) $charset_collate;",
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_internal_link_items` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                          `target_keywords` text,
                          `target_url` mediumtext,
                          `status` varchar(1) DEFAULT NULL,
                          `group_id` int(11) DEFAULT NULL,
                          `created_on` datetime DEFAULT NULL
                        ) $charset_collate;",
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_properties` (
                          `id` int(11) unsigned NOT NULL auto_increment primary key,
                          `url` mediumtext,
                          `url_status` varchar(10)  DEFAULT NULL,
                          `group_id` int(11) NOT NULL,
                          `automatic_update_status` varchar(1) DEFAULT NULL,
                          `auto_find_backlink` varchar(1) NOT NULL
                        ) $charset_collate;",
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_properties_assets` (
                          `id` int(11) unsigned NOT NULL auto_increment primary key,
                          `p_id` int(11) DEFAULT NULL,
                          `alexa_data` mediumtext,
                          `moz_data` mediumtext,
                          `created_on` date DEFAULT NULL
                        ) $charset_collate;",
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_property_groups` (
                          `id` int(11) unsigned NOT NULL auto_increment primary key,
                          `name` varchar(100) DEFAULT NULL,
                          `description` varchar(200) DEFAULT NULL,
                          `created_on` datetime DEFAULT NULL
                        ) $charset_collate;",
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_backlinks` (
                          `b_id` int(11) unsigned NOT NULL auto_increment primary key,
                          `p_id` int(11),
                          `url` mediumtext,
                          `price` int(11),
                          `group_id` int(11) NOT NULL,
                          `automatic_update_status` varchar(1),
                          `created_on` date DEFAULT NULL
                        ) $charset_collate;",
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_backlinks_assets` (
                          `id` int(11) unsigned NOT NULL auto_increment primary key,
                          `b_id` int(11) DEFAULT NULL,
                          `url_status` varchar(5),
                          `link_to` mediumtext,
                          `link_text` tinytext,
                          `backlink_type` varchar(20),
                          `alexa_data` mediumtext,
                          `moz_data` mediumtext,
                          `created_on` date DEFAULT NULL
                        ) $charset_collate;",
                        "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}aios_backlink_groups` (
                          `id` int(11) unsigned NOT NULL auto_increment primary key,
                          `name` varchar(100) DEFAULT NULL,
                          `price_monthly` int(11),
                          `description` varchar(200),
                          `created_on` datetime
                        ) $charset_collate;",
		);
                        
            
            add_option( 'aios_installed_on', date('Y-m-d H:i:s') );
            add_option( 'aios_db_version', $aios_db_version );
//        }

       
//        require_once( ABSPATH. 'wp-admin/includes/upgrade.php');
//        dbDelta( $create_tbl );
        foreach ( $create_tbl as $sql ) {
                if ( $wpdb->query( $sql ) === false )
                        return false;
        }

        // Groups
        if ( intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}aios_redirection_groups", 10 ) ) === 0 ) {
                $wpdb->insert( $wpdb->prefix.'aios_redirection_groups', array( 'name' => __( 'Redirections' ), 'module_id' => 1, 'position' => 0 ) );
                $wpdb->insert( $wpdb->prefix.'aios_redirection_groups', array( 'name' => __( 'Modified Posts' ), 'module_id' => 1, 'position' => 1 ) );

                $options = get_option( 'redirection_options' );
                $options['monitor_post']     = 2;
                $options['monitor_category'] = 2;

                update_option( 'redirection_options', $options );
        }
        
        if( intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}aios_properties", 10 ) ) === 0 ) {
            $wpdb->insert( $wpdb->prefix. 'aios_properties', array( 'url' => site_url(), 'automatic_update_status'=> 1, 'auto_find_backlink' => 1 ));
        }
    }
}

if( ! function_exists( 'aios_update_db_check' ) ){
    /**
     * Check DB version
     * 
     * @global global $aios_db_version
     */
    function aios_update_db_check() {
        global $aios_db_version;
        if ( get_site_option( 'aios_db_version' ) != $aios_db_version ) {
            aios_db_install();
        }
    }
}
    
add_action( 'aios_update_db_check', 'aios_update_db_check' );
do_action('aios_update_db_check');


if( !function_exists( 'cs_custom_cron_schedule' ) ){
    
    /**
     * Custom Cron Schedule
     * 
     * @return array
     */
    function cs_custom_cron_schedule(){
        $schedule = array(
            'twice_per_hour' => array(
                'interval' => 30 * 60, 
                'display' => __( 'Twice per Hour', SR_TEXTDOMAIN )
            ),
            'once_per_hour' => array(
                'interval' => 60 * 60, 
                'display' => __( 'Once Hourly', SR_TEXTDOMAIN )
            ),
            'twice_daily' => array(
                'interval' => 12 * 60 * 60, 
                'display' => __( 'Twice Daily', SR_TEXTDOMAIN )
            ),
            'once_daily' => array(
                'interval' => 24 * 60 * 60, 
                'display' => __( 'Once Daily', SR_TEXTDOMAIN )
            ),
            'onceper5days' => array(
                'interval' => 5 * 24 * 60 * 60,
                'display' => __( 'Once Every 5 days', SR_TEXTDOMAIN )
            ),
            'onceper10days' => array(
                'interval' => 10 * 24 * 60 * 60,
                'display' => __( 'Once Every 10 days', SR_TEXTDOMAIN )
            ),
            'weekly' => array(
                'interval' => 7 * 24 * 60 * 60, //7 days * 24 hours * 60 minutes * 60 seconds
                'display' => __( 'Once Weekly', SR_TEXTDOMAIN )
            ),
            'biweekly' => array(
                'interval' => 7 * 24 * 60 * 60 * 2,
                'display' => __( 'Every Other Week', SR_TEXTDOMAIN )
            ),
            'monthly' => array(
                'interval' => 30 * 24 * 60 * 60,
                'display' => __( 'Once Per Month', SR_TEXTDOMAIN )
            ),
            'csnow' => array(
                'interval' => 30 * 24 * 60 * 60,
                'display' => __( 'CS Now', SR_TEXTDOMAIN )
            ),
            
        );
                
        return $schedule;
    }
}
add_filter( 'cron_schedules', 'cs_custom_cron_schedule' ); 


//tables
/*
aios_social_publish_schedule
aios_facebook_statistics
 aios_crawler_errors
 * aios_se_indexed_pages
 * aios_remove_urls
 */