<?php


/**
 * Fired during plugin activation.
 *
 */
class Selcont_Activator {

	public static function activate() {
        flush_rewrite_rules();
	}

}
