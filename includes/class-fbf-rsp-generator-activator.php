<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.chapteragency.com
 * @since      1.0.0
 *
 * @package    Fbf_Rsp_Generator
 * @subpackage Fbf_Rsp_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Fbf_Rsp_Generator
 * @subpackage Fbf_Rsp_Generator/includes
 * @author     Kevin Price-Ward <kevin.price-ward@chapteragency.com>
 */
class Fbf_Rsp_Generator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        //Install the logging database
        self::db_install();
	}

    private static function db_install()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fbf_rsp_rules';
        $table_name2 = $wpdb->prefix . 'fbf_rsp_rule_items';
        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          sort_order int(4) NOT NULL DEFAULT 0,
          name varchar(30) NOT NULL,
          created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,  
          price_match boolean NOT NULL DEFAULT 0,
          price_match_addition float(4,2) NULL,
          amount float(4,2) NULL,
          is_pc boolean NOT NULL DEFAULT 1,
          PRIMARY KEY  (id)
        ) $charset_collate;";
        //dbDelta($sql);

        $sql2 = "CREATE TABLE $table_name2 (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            rule_id mediumint(9) NOT NULL,
            taxonomy varchar(20) NOT NULL,
            term varchar(60) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta([$sql, $sql2]);

        $wpdb->query("ALTER TABLE $table_name2 ADD FOREIGN KEY (rule_id) REFERENCES  $table_name(id) ON DELETE CASCADE"); //Add the foreign key constraint via wpdb because dbdelta does not support it!!!

        add_option('fbf_rsp_generator_db_version', FBF_RSP_GENERATOR_DB_VERSION);
    }

}
