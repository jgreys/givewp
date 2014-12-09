<?php
/**
 * Plugin Name: Give
 * Plugin URI: http://give.wordimpress.com
 * Description: <strong>Democratizing Generosity</strong> - Quickly and Easily Accept Donations
 * Author: WordImpress
 * Author URI: http://wordimpress.com
 * Version: 1.0.0
 * Text Domain: give
 * Domain Path: languages
 *
 * Give is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Give is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Give. If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Give' ) ) : /**
 * Main GIVE Class
 *
 * @since 1.0
 */ {
	final class Give {
		/** Singleton *************************************************************/

		/**
		 * @var Give The one true Give
		 * @since 1.0
		 */
		private static $instance;


		/**
		 * Main Give Instance
		 *
		 * Insures that only one instance of Give exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since     1.0
		 * @static
		 * @staticvar array $instance
		 * @uses      Give::setup_constants() Setup the constants needed
		 * @uses      Give::includes() Include the required files
		 * @uses      Give::load_textdomain() load the language files
		 * @see       Give()
		 * @return The one true Give
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Give ) ) {
				self::$instance = new Give;
				self::$instance->setup_constants();


				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

				self::$instance->includes();
				self::$instance->give_settings_pages = new Give_Plugin_Settings();

			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since  1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'give' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since  1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'give' ), '1.0' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version
			if ( ! defined( 'GIVE_VERSION' ) ) {
				define( 'GIVE_VERSION', '1.0.0' );
			}

			// Plugin Folder Path
			if ( ! defined( 'GIVE_PLUGIN_DIR' ) ) {
				define( 'GIVE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'GIVE_PLUGIN_URL' ) ) {
				define( 'GIVE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'GIVE_PLUGIN_FILE' ) ) {
				define( 'GIVE_PLUGIN_FILE', __FILE__ );
			}

			// Make sure CAL_GREGORIAN is defined
			if ( ! defined( 'CAL_GREGORIAN' ) ) {
				define( 'CAL_GREGORIAN', 1 );
			}
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since  1.0
		 * @return void
		 */
		private function includes() {
			global $give_options;

			require_once GIVE_PLUGIN_DIR . 'includes/admin/register-settings.php';
			//		$give_options = give_get_settings();

			require_once GIVE_PLUGIN_DIR . 'includes/post-types.php';
			require_once GIVE_PLUGIN_DIR . 'includes/scripts.php';
			require_once GIVE_PLUGIN_DIR . 'includes/class-give-roles.php';

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

				require_once GIVE_PLUGIN_DIR . 'includes/admin/welcome.php';
				require_once GIVE_PLUGIN_DIR . 'includes/admin/admin-pages.php';
				require_once GIVE_PLUGIN_DIR . 'includes/admin/forms/metabox.php';

			} else {

			}

			require_once GIVE_PLUGIN_DIR . 'includes/install.php';

		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since  1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$give_lang_dir = dirname( plugin_basename( GIVE_PLUGIN_FILE ) ) . '/languages/';
			$give_lang_dir = apply_filters( 'give_languages_directory', $give_lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'give' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'give', $locale );

			// Setup paths to current locale file
			$mofile_local  = $give_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/give/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/give folder
				load_textdomain( 'give', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/give/languages/ folder
				load_textdomain( 'give', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'give', false, $give_lang_dir );
			}
		}
	}
}

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true Give
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $give = Give(); ?>
 *
 * @since 1.0
 * @return object The one true Give Instance
 */
function Give() {
	return Give::instance();
}

// Get Give Running
Give();
