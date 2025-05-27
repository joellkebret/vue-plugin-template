<?php
/***
* Class to add in basic functions for database manipulation
* db Functions
***/
if (!class_exists('HSS_DB_Tools', false)) {
	class HSS_DB_Tools {
	
		//does table exist
		public static function tableExists( $tableName, $tablePrefix = '') {
			global $wpdb;

			$rows = $wpdb->get_results($wpdb->prepare('SELECT COUNT(*) as bool_table FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = %s', $tablePrefix . $tableName)); // phpcs: ignore WordPress.DB.PreparedSQL.NotPrepared
			if (count($rows) <= 0 ||  0 == $rows[0]->bool_table) {
				return false;
			}
			return true;
		}

		public static function dropTable( $tableName, $tablePrefix = '') {
			global $wpdb;
			$result = $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS `%1$s`', $tablePrefix . $tableName)); // phpcs: ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( false === $result || 0 === $result) {
				error_log('Error deleting table ' . $tablePrefix . $tableName);
				return false;
			} 
			// error_log('Deleted table ' . $tablePrefix . $tableName);
			return true;
		}
		public static function getTableNameWithPrefix( $tableName) {
			global $wpdb;
			$prefix = $wpdb->prefix;
			return $prefix . $tableName;
		}

		public static function hasRowChanged( $tableName, $idField, $idValue, $updateDate, $updateField = 'update_date') {
			global $wpdb;

			$escTable = self::getTableNameWithPrefix($tableName);

			$rows = $wpdb->get_results($wpdb->prepare('SELECT COUNT(*) as bool_no_change FROM `%1$s` WHERE `%2$s` = %3$d AND `%4$s` = "%5$s"', $escTable, $idField, $idValue, $updateField, $updateDate)); // phpcs: ignore WordPress.DB.PreparedSQL.NotPrepared
			if (count($rows) <=0 || 0 == $rows[0]->bool_no_change) {
				return true;
			}
			return false;
		}
	}
}
