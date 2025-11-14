<?php
/**
 * 2007-2022 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2022 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

namespace LeoElements\Core\Editor;

require_once LEOELEMENTS_PATH . 'src/Leo_Editors.php';

use LeoElements\Src\_LEO_Editors;
use LeoElements\Core\Common\Modules\Ajax\Module as Ajax;
use LeoElements\Core\Debug\Loading_Inspection_Manager;
use LeoElements\Core\Responsive\Responsive;
use LeoElements\Core\Settings\Manager as SettingsManager;
use LeoElements\Icons_Manager;
use LeoElements\Plugin;
use LeoElements\Schemes_Manager;
use LeoElements\Settings;
use LeoElements\Shapes;
use LeoElements\TemplateLibrary\Source_Local;
use LeoElements\Tools;
use LeoElements\User;
use LeoElements\Utils;
use LeoElements\Leo_Helper;

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor editor.
 *
 * Elementor editor handler class is responsible for initializing Elementor
 * editor and register all the actions needed to display the editor.
 *
 * @since 1.0.0
 */
class Editor {

	/**
	 * The nonce key for Elementor editor.
	 * @deprecated 2.3.0
	 */
	const EDITING_NONCE_KEY = 'elementor-editing';

	/**
	 * User capability required to access Elementor editor.
	 */
	const EDITING_CAPABILITY = 'edit_posts';

	/**
	 * Post ID.
	 *
	 * Holds the ID of the current post being edited.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var int Post ID.
	 */
	private $_post_id;

	/**
	 * Whether the edit mode is active.
	 *
	 * Used to determine whether we are in edit mode.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var bool Whether the edit mode is active.
	 */
	private $_is_edit_mode;

	/**
	 * @var Notice_Bar
	 */
	public $notice_bar;

	/**
	 * Init.
	 *
	 * Initialize Elementor editor. Registers all needed actions to run Elementor,
	 * removes conflicting actions etc.
	 *
	 * Fired by `admin_action_elementor` action.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $die Optional. Whether to die at the end. Default is `true`.
	 */
	public function init( $die = true ) {			
		if ( !$this->is_edit_mode() ) {
			return;
		}
		
		$leo_helper = Leo_Helper::instance();
		
		// Handle `wp_head`
		Leo_Helper::add_action( 'wp_head', [ $leo_helper, 'wp_enqueue_scripts' ], 1 );
		Leo_Helper::add_action( 'wp_head', [ $leo_helper, 'wp_print_styles' ], 8 );
		Leo_Helper::add_action( 'wp_head', [ $leo_helper, 'wp_print_head_scripts' ], 9 );
		Leo_Helper::add_action( 'wp_head', [ $this, 'editor_head_trigger' ], 30 );

		// Handle `wp_footer`
		Leo_Helper::add_action( 'wp_footer', [ $leo_helper, 'wp_print_footer_scripts' ], 20 );
		Leo_Helper::add_action( 'wp_footer', [ $this, 'wp_footer' ] );

		Leo_Helper::add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 999999 );
		Leo_Helper::add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 999999 );

		// Setup default heartbeat options
		Leo_Helper::add_filter( 'heartbeat_settings', function( $settings ) {
			$settings['interval'] = 15;
			return $settings;
		} );

		Leo_Helper::do_action( 'elementor/editor/init' );

		$this->print_editor_template();

		// From the action it's an empty string, from tests its `false`
		if ( false !== $die ) {
			die;
		}
	}

	/**
	 * Retrieve post ID.
	 *
	 * Get the ID of the current post.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @return int Post ID.
	 */
	public function get_post_id() {
		return $this->_post_id;
	}

	/**
	 * Redirect to new URL.
	 *
	 * Used as a fallback function for the old URL structure of Elementor page
	 * edit URL.
	 *
	 * Fired by `template_redirect` action.
	 *
	 * @since 1.6.0
	 * @access public
	 */
	public function redirect_to_new_url() {
		if ( ! Tools::getIsset('elementor') ) {
			return;
		}

		$document = Plugin::$instance->documents->get( Leo_Helper::get_the_ID() );

		if ( ! $document ) {
			wp_die( Leo_Helper::__( 'Document not found.', 'elementor' ) );
		}

		if ( ! $document->is_editable_by_current_user() || ! $document->is_built_with_elementor() ) {
			return;
		}

		wp_safe_redirect( $document->get_edit_url() );
		die;
	}

	/**
	 * Whether the edit mode is active.
	 *
	 * Used to determine whether we are in the edit mode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id Optional. Post ID. Default is `null`, the current
	 *                     post ID.
	 *
	 * @return bool Whether the edit mode is active.
	 */
	public function is_edit_mode( $post_id = null ) {
		if ( null !== $this->_is_edit_mode ) {
			return $this->_is_edit_mode;
		}

		if ( empty( $post_id ) ) {
			$post_id = $this->_post_id;
		}

		$document = Plugin::$instance->documents->get( $post_id );

		if ( ! $document ) {
			return false;
		}

		// Ajax request as Editor mode
		$actions = [
			'elementor',

			// Templates
			'elementor_get_templates',
			'elementor_save_template',
			'elementor_get_template',
			'elementor_delete_template',
			'elementor_import_template',
			'elementor_library_direct_actions',
		];

		if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $actions ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Lock post.
	 *
	 * Mark the post as currently being edited by the current user.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id The ID of the post being edited.
	 */
	public function lock_post( $post_id ) {
		if ( ! function_exists( 'wp_set_post_lock' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		wp_set_post_lock( $post_id );
	}

	/**
	 * Get locked user.
	 *
	 * Check what user is currently editing the post.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id The ID of the post being edited.
	 *
	 * @return \WP_User|false User information or false if the post is not locked.
	 */
	public function get_locked_user( $post_id ) {
		if ( ! function_exists( 'wp_check_post_lock' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		$locked_user = wp_check_post_lock( $post_id );
		if ( ! $locked_user ) {
			return false;
		}

		return get_user_by( 'id', $locked_user );
	}

	/**
	 * Print Editor Template.
	 *
	 * Include the wrapper template of the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function print_editor_template() {
		include LEOELEMENTS_PATH . 'includes/editor-templates/editor-wrapper.php';
	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers all the editor scripts and enqueues them.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {

		$plugin = Plugin::$instance;

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || defined( 'LEOELEMENTS_TESTS' ) && LEOELEMENTS_TESTS ) ? '' : '.min';

		Leo_Helper::wp_register_script(
			'elementor-editor-modules',
			LEOELEMENTS_ASSETS_URL . 'js/editor-modules' . $suffix . '.js',
			[
				'elementor-common-modules',
			],
			LEOELEMENTS_VERSION,
			true
		);
		// Hack for waypoint with editor mode.
		Leo_Helper::wp_register_script(
			'elementor-waypoints',
			LEOELEMENTS_ASSETS_URL . 'lib/waypoints/waypoints-for-editor.js',
			[
				'jquery',
			],
			'4.0.2',
			true
		);

		Leo_Helper::wp_register_script(
			'perfect-scrollbar',
			LEOELEMENTS_ASSETS_URL . 'lib/perfect-scrollbar/js/perfect-scrollbar' . $suffix . '.js',
			[],
			'1.4.0',
			true
		);

		Leo_Helper::wp_register_script(
			'jquery-easing',
			LEOELEMENTS_ASSETS_URL . 'lib/jquery-easing/jquery-easing' . $suffix . '.js',
			[
				'jquery',
			],
			'1.3.2',
			true
		);

		Leo_Helper::wp_register_script(
			'nprogress',
			LEOELEMENTS_ASSETS_URL . 'lib/nprogress/nprogress' . $suffix . '.js',
			[],
			'0.2.0',
			true
		);

		Leo_Helper::wp_register_script(
			'tipsy',
			LEOELEMENTS_ASSETS_URL . 'lib/tipsy/tipsy' . $suffix . '.js',
			[
				'jquery',
			],
			'1.0.0',
			true
		);

		Leo_Helper::wp_register_script(
			'jquery-elementor-select2',
			LEOELEMENTS_ASSETS_URL . 'lib/e-select2/js/e-select2.full' . $suffix . '.js',
			[
				'jquery',
			],
			'4.0.6-rc.1',
			true
		);

		Leo_Helper::wp_register_script(
			'flatpickr',
			LEOELEMENTS_ASSETS_URL . 'lib/flatpickr/flatpickr' . $suffix . '.js',
			[
				'jquery',
			],
			'1.12.0',
			true
		);

		Leo_Helper::wp_register_script(
			'ace',
			'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ace.js',
			[],
			'1.2.5',
			true
		);

		Leo_Helper::wp_register_script(
			'ace-language-tools',
			'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ext-language_tools.js',
			[
				'ace',
			],
			'1.2.5',
			true
		);

		Leo_Helper::wp_register_script(
			'jquery-hover-intent',
			LEOELEMENTS_ASSETS_URL . 'lib/jquery-hover-intent/jquery-hover-intent' . $suffix . '.js',
			[],
			'1.0.0',
			true
		);

		Leo_Helper::wp_register_script(
			'nouislider',
			LEOELEMENTS_ASSETS_URL . 'lib/nouislider/nouislider' . $suffix . '.js',
			[],
			'13.0.0',
			true
		);
		
		Leo_Helper::wp_register_script(
			'autocomplete',
			LEOELEMENTS_ASSETS_URL . 'lib/jquery/ui/autocomplete.min.js',
			[
				'jquery',
			],
			'1.11.4',
			true
		);

		Leo_Helper::wp_register_script(
			'imagesloaded',
			LEOELEMENTS_ASSETS_URL . 'lib/wp-include/imagesloaded.min.js',
			[],
			'3.2.0',
			true
		);

		Leo_Helper::wp_register_script(
			'leo-editor',
			LEOELEMENTS_ASSETS_URL . 'js/leo-editor' . $suffix . '.js',
			[
			],
			'',
			true
		);

		Leo_Helper::wp_register_script(
			'elementor-editor',
			LEOELEMENTS_ASSETS_URL . 'js/editor' . Leo_Helper::get_elementor_editor_suffix() . $suffix . '.js',
			[
				'elementor-common',
				'elementor-editor-modules',
				'jquery-ui-sortable',
				'jquery-ui-resizable',
				'perfect-scrollbar',
				'nprogress',
				'tipsy',
				'imagesloaded',
				'heartbeat',
				'jquery-elementor-select2',
				'flatpickr',
				'ace',
				'ace-language-tools',
				'jquery-hover-intent',
				'nouislider',
				'autocomplete',
				'leo-editor'
			],
			LEOELEMENTS_VERSION,
			true
		);

		/**
		 * Before editor enqueue scripts.
		 *
		 * Fires before Elementor editor scripts are enqueued.
		 *
		 * @since 1.0.0
		 */
		Leo_Helper::do_action( 'elementor/editor/before_enqueue_scripts' );
		
		$data = $this->get_elementor_data();
		
		// Get document data *after* the scripts hook - so plugins can run compatibility before get data, but *before* enqueue the editor script - so elements can enqueue their own scripts that depended in editor script.
		$editor_data = $data['raw_data'];
		
		$page_title_selector = Leo_Helper::get_option( 'elementor_page_title_selector' );

		if ( empty( $page_title_selector ) ) {
			$page_title_selector = 'h1.entry-title';
		}
		$config = [
			'version' => LEOELEMENTS_VERSION,
			'home_url' => __PS_BASE_URI__,
			'data' => $editor_data,
			'document' => $data['document'],
			'autosave_interval' => LEO_AUTOSAVE_INTERVAL,
			'current_user_can_publish' => true, 
			'controls' => $plugin->controls_manager->get_controls_data(),
			'elements' => $plugin->elements_manager->get_element_types_config(),
			'widgets' => $plugin->widgets_manager->get_widget_types_config(),
			'schemes' => [
				'items' => $plugin->schemes_manager->get_registered_schemes_data(),
				'enabled_schemes' => Schemes_Manager::get_enabled_schemes(),
			],
			'icons' => [
				'libraries' => Icons_Manager::get_icon_manager_tabs_config(),
				'goProURL' => Utils::get_pro_link( 'https://subdomain.leoelements.com/pro/?utm_source=icon-library-go-pro&utm_campaign=gopro&utm_medium=wp-dash' ),
			],
			'fa4_to_fa5_mapping_url' => LEOELEMENTS_ASSETS_URL . 'lib/font-awesome/migration/mapping.json',
			'default_schemes' => $plugin->schemes_manager->get_schemes_defaults(),
			'settings' => SettingsManager::get_settings_managers_config(),
			'system_schemes' => $plugin->schemes_manager->get_system_schemes(),
			'wp_editor' => $this->get_wp_editor_config(),
			'settings_page_link' => Leo_Helper::get_exit_to_dashboard( 'AdminLeoElementsProfiles' ),
			'tools_page_link' =>  Leo_Helper::get_exit_to_dashboard( 'AdminLeoElementsProfiles' ),
			'docs_elementor_site' => 'https://subdomain.leoelements.com/docs/',
			'help_the_content_url' => 'https://subdomain.leoelements.com/the-content-missing/',
			'help_right_click_url' => 'https://subdomain.leoelements.com/meet-right-click/',
			'help_flexbox_bc_url' => 'https://subdomain.leoelements.com/flexbox-layout-bc/',
			'additional_shapes' => Shapes::get_additional_shapes_for_config(),
			'locked_user' => '',
			'user' => [
				'restrictions' => [],
				'is_administrator' => true,
				'introduction' => [],
			],
			'preview' => [
				'help_preview_error_url' => 'https://subdomain.leoelements.com/preview-not-loaded/',
				'help_preview_http_error_url' => 'https://subdomain.leoelements.com/preview-not-loaded/#permissions',
				'help_preview_http_error_500_url' => 'https://subdomain.leoelements.com/500-error/',
				'debug_data' => Loading_Inspection_Manager::instance()->run_inspections(),
			],
			'id_lang' => Leo_Helper::$id_lang,
			'locale' => \Context::getContext()->language->iso_code,
			'languages' => \Language::getLanguages(true),
			'rich_editing_enabled' => 1,
			'page_title_selector' => $page_title_selector,
			'tinymceHasCustomConfig' => class_exists( 'Tinymce_Advanced' ),
			'inlineEditing' => Plugin::$instance->widgets_manager->get_inline_editing_config(),
			'dynamicTags' => Plugin::$instance->dynamic_tags->get_config(),
			'editButtons' => Leo_Helper::get_option( 'elementor_edit_buttons' ),
			'i18n' => [		
				'elementor' => Leo_Helper::__( 'LeoElements', 'elementor' ),
				'delete' => Leo_Helper::__( 'Delete', 'elementor' ),
				'cancel' => Leo_Helper::__( 'Cancel', 'elementor' ),
				'got_it' => Leo_Helper::__( 'Got It', 'elementor' ),
				/* translators: %s: Element type. */
				'add_element' => Leo_Helper::__( 'Add %s', 'elementor' ),
				/* translators: %s: Element name. */
				'edit_element' => Leo_Helper::__( 'Edit %s', 'elementor' ),
				/* translators: %s: Element type. */
				'duplicate_element' => Leo_Helper::__( 'Duplicate %s', 'elementor' ),
				/* translators: %s: Element type. */
				'delete_element' => Leo_Helper::__( 'Delete %s', 'elementor' ),
				'flexbox_attention_header' => Leo_Helper::__( 'Note: Flexbox Changes', 'elementor' ),
				'flexbox_attention_message' => Leo_Helper::__( 'LeoElements 1.0.0 introduces key changes to the layout using CSS Flexbox. Your existing pages might have been affected, please review your page before publishing.', 'elementor' ),

				// Menu.
				'about_elementor' => Leo_Helper::__( 'About LeoElements', 'elementor' ),
				'color_picker' => Leo_Helper::__( 'Color Picker', 'elementor' ),
				'elementor_settings' => Leo_Helper::__( 'Dashboard Settings', 'elementor' ),
				'global_colors' => Leo_Helper::__( 'Default Colors', 'elementor' ),
				'global_fonts' => Leo_Helper::__( 'Default Fonts', 'elementor' ),
				'global_style' => Leo_Helper::__( 'Style', 'elementor' ),
				'settings' => Leo_Helper::__( 'Settings', 'elementor' ),
				'go_to' => Leo_Helper::__( 'Go To', 'elementor' ),
				'view_page' => Leo_Helper::__( 'View Page', 'elementor' ),
				'exit_to_dashboard' => Leo_Helper::__( 'Exit To Dashboard', 'elementor' ),

				// Elements.
				'inner_section' => Leo_Helper::__( 'Inner Section', 'elementor' ),

				// Control Order.
				'asc' => Leo_Helper::__( 'Ascending order', 'elementor' ),
				'desc' => Leo_Helper::__( 'Descending order', 'elementor' ),

				// Clear Page.
				'clear_page' => Leo_Helper::__( 'Delete All Content', 'elementor' ),
				'dialog_confirm_clear_page' => Leo_Helper::__( 'Attention: We are going to DELETE ALL CONTENT from this page. Are you sure you want to do that?', 'elementor' ),

				// Enable SVG uploads.
				'enable_svg' => Leo_Helper::__( 'Enable SVG Uploads', 'elementor' ),
				'dialog_confirm_enable_svg' => Leo_Helper::__( 'Before you enable SVG upload, note that SVG files include a security risk. LeoElements does run a process to remove possible malicious code, but there is still risk involved when using such files.', 'elementor' ),

				// Enable fontawesome 5 if needed.
				'enable_fa5' => Leo_Helper::__( 'LeoElements\'s New Icon Library', 'elementor' ),
				'dialog_confirm_enable_fa5' => Leo_Helper::__( 'LeoElements v1.0 includes an upgrade from Font Awesome 4 to 5. In order to continue using icons, be sure to click "Upgrade".', 'elementor' ) . ' <a href="https://subdomain.leoelements.com/fontawesome-migration/" target="_blank">' . Leo_Helper::__( 'Learn More', 'elementor' ) . '</a>',

				// Panel Preview Mode.
				'back_to_editor' => Leo_Helper::__( 'Show Panel', 'elementor' ),
				'preview' => Leo_Helper::__( 'Hide Panel', 'elementor' ),

				// Inline Editing.
				'type_here' => Leo_Helper::__( 'Type Here', 'elementor' ),

				// Library.
				'an_error_occurred' => Leo_Helper::__( 'An error occurred', 'elementor' ),
				'category' => Leo_Helper::__( 'Category', 'elementor' ),
				'delete_template' => Leo_Helper::__( 'Delete Template', 'elementor' ),
				'delete_template_confirm' => Leo_Helper::__( 'Are you sure you want to delete this template?', 'elementor' ),
				'import_template_dialog_header' => Leo_Helper::__( 'Import Document Settings', 'elementor' ),
				'import_template_dialog_message' => Leo_Helper::__( 'Do you want to also import the document settings of the template?', 'elementor' ),
				'import_template_dialog_message_attention' => Leo_Helper::__( 'Attention: Importing may override previous settings.', 'elementor' ),
				'library' => Leo_Helper::__( 'Library', 'elementor' ),
				'no' => Leo_Helper::__( 'No', 'elementor' ),
				'page' => Leo_Helper::__( 'Page', 'elementor' ),
				/* translators: %s: Template type. */
				'save_your_template' => Leo_Helper::__( 'Save Your %s to Library', 'elementor' ),
				'save_your_template_description' => Leo_Helper::__( 'Your designs will be available for export and reuse on any page or website', 'elementor' ),
				'section' => Leo_Helper::__( 'Section', 'elementor' ),
				'templates_empty_message' => Leo_Helper::__( 'This is where your templates should be. Design it. Save it. Reuse it.', 'elementor' ),
				'templates_empty_title' => Leo_Helper::__( 'Haven’t Saved Templates Yet?', 'elementor' ),
				'templates_no_favorites_message' => Leo_Helper::__( 'You can mark any pre-designed template as a favorite.', 'elementor' ),
				'templates_no_favorites_title' => Leo_Helper::__( 'No Favorite Templates', 'elementor' ),
				'templates_no_results_message' => Leo_Helper::__( 'Please make sure your search is spelled correctly or try a different words.', 'elementor' ),
				'templates_no_results_title' => Leo_Helper::__( 'No Results Found', 'elementor' ),
				'templates_request_error' => Leo_Helper::__( 'The following error(s) occurred while processing the request:', 'elementor' ),
				'yes' => Leo_Helper::__( 'Yes', 'elementor' ),
				'blocks' => Leo_Helper::__( 'Blocks', 'elementor' ),
				'pages' => Leo_Helper::__( 'Pages', 'elementor' ),
				'my_templates' => Leo_Helper::__( 'My Templates', 'elementor' ),

				// Incompatible Device.
				'device_incompatible_header' => Leo_Helper::__( 'Your browser isn\'t compatible', 'elementor' ),
				'device_incompatible_message' => Leo_Helper::__( 'Your browser isn\'t compatible with all of LeoElements\'s editing features. We recommend you switch to another browser like Chrome or Firefox.', 'elementor' ),
				'proceed_anyway' => Leo_Helper::__( 'Proceed Anyway', 'elementor' ),

				// Preview not loaded.
				'learn_more' => Leo_Helper::__( 'Learn More', 'elementor' ),
				'preview_el_not_found_header' => Leo_Helper::__( 'Sorry, the content area was not found in your page.', 'elementor' ),
				'preview_el_not_found_message' => Leo_Helper::__( 'You must call \'the_content\' function in the current template, in order for LeoElements to work on this page.', 'elementor' ),

				// Gallery.
				'delete_gallery' => Leo_Helper::__( 'Reset Gallery', 'elementor' ),
				'dialog_confirm_gallery_delete' => Leo_Helper::__( 'Are you sure you want to reset this gallery?', 'elementor' ),
				/* translators: %s: The number of images. */
				'gallery_images_selected' => Leo_Helper::__( '%s Images Selected', 'elementor' ),
				'gallery_no_images_selected' => Leo_Helper::__( 'No Images Selected', 'elementor' ),
				'insert_media' => Leo_Helper::__( 'Insert Media', 'elementor' ),

				// Take Over.
				/* translators: %s: User name. */
				'dialog_user_taken_over' => Leo_Helper::__( '%s has taken over and is currently editing. Do you want to take over this page editing?', 'elementor' ),
				'go_back' => Leo_Helper::__( 'Go Back', 'elementor' ),
				'take_over' => Leo_Helper::__( 'Take Over', 'elementor' ),

				// Revisions.
				/* translators: %s: Template type. */
				'dialog_confirm_delete' => Leo_Helper::__( 'Are you sure you want to remove this %s?', 'elementor' ),

				// Saver.
				'before_unload_alert' => Leo_Helper::__( 'Please note: All unsaved changes will be lost.', 'elementor' ),
				'published' => Leo_Helper::__( 'Published', 'elementor' ),
				'publish' => Leo_Helper::__( 'Publish', 'elementor' ),
				'save' => Leo_Helper::__( 'Save', 'elementor' ),
				'saved' => Leo_Helper::__( 'Saved', 'elementor' ),
				'update' => Leo_Helper::__( 'Update', 'elementor' ),
				'enable' => Leo_Helper::__( 'Enable', 'elementor' ),
				'submit' => Leo_Helper::__( 'Submit', 'elementor' ),
				'working_on_draft_notification' => Leo_Helper::__( 'This is just a draft. Play around and when you\'re done - click update.', 'elementor' ),
				'keep_editing' => Leo_Helper::__( 'Keep Editing', 'elementor' ),
				'have_a_look' => Leo_Helper::__( 'Have a look', 'elementor' ),
				'view_all_revisions' => Leo_Helper::__( 'View All Revisions', 'elementor' ),
				'dismiss' => Leo_Helper::__( 'Dismiss', 'elementor' ),
				'saving_disabled' => Leo_Helper::__( 'Saving has been disabled until you’re reconnected.', 'elementor' ),

				// Ajax
				'server_error' => Leo_Helper::__( 'Server Error', 'elementor' ),
				'server_connection_lost' => Leo_Helper::__( 'Connection Lost', 'elementor' ),
				'unknown_error' => Leo_Helper::__( 'Unknown Error', 'elementor' ),

				// Context Menu
				'duplicate' => Leo_Helper::__( 'Duplicate', 'elementor' ),
				'copy' => Leo_Helper::__( 'Copy', 'elementor' ),
				'paste' => Leo_Helper::__( 'Paste', 'elementor' ),
				'copy_style' => Leo_Helper::__( 'Copy Style', 'elementor' ),
				'paste_style' => Leo_Helper::__( 'Paste Style', 'elementor' ),
				'reset_style' => Leo_Helper::__( 'Reset Style', 'elementor' ),
				'save_as_global' => Leo_Helper::__( 'Save as a Global', 'elementor' ),
				'save_as_block' => Leo_Helper::__( 'Save as Template', 'elementor' ),
				'new_column' => Leo_Helper::__( 'Add New Column', 'elementor' ),
				'copy_all_content' => Leo_Helper::__( 'Copy All Content', 'elementor' ),
				'delete_all_content' => Leo_Helper::__( 'Delete All Content', 'elementor' ),
				'navigator' => Leo_Helper::__( 'Navigator', 'elementor' ),

				// Right Click Introduction
				'meet_right_click_header' => Leo_Helper::__( 'Meet Right Click', 'elementor' ),
				'meet_right_click_message' => Leo_Helper::__( 'Now you can access all editing actions using right click.', 'elementor' ),

				// Hotkeys screen
				'keyboard_shortcuts' => Leo_Helper::__( 'Keyboard Shortcuts', 'elementor' ),

				// Deprecated Control
				'deprecated_notice' => Leo_Helper::__( 'The <strong>%1$s</strong> widget has been deprecated since %2$s %3$s.', 'elementor' ),
				'deprecated_notice_replacement' => Leo_Helper::__( 'It has been replaced by <strong>%1$s</strong>.', 'elementor' ),
				'deprecated_notice_last' => Leo_Helper::__( 'Note that %1$s will be completely removed once %2$s %3$s is released.', 'elementor' ),

				//Preview Debug
				'preview_debug_link_text' => Leo_Helper::__( 'Click here for preview debug', 'elementor' ),

				'icon_library' => Leo_Helper::__( 'Icon Library', 'elementor' ),
				'my_libraries' => Leo_Helper::__( 'My Libraries', 'elementor' ),
				'upload' => Leo_Helper::__( 'Upload', 'elementor' ),
				'icons_promotion' => Leo_Helper::__( 'Become a Pro user to upload unlimited font icon folders to your website.', 'elementor' ),
				'go_pro_»' => Leo_Helper::__( 'Go Pro »', 'elementor' ),
				'custom_positioning' => Leo_Helper::__( 'Custom Positioning', 'elementor' ),

				// TODO: Remove.
				'autosave' => Leo_Helper::__( 'Autosave', 'elementor' ),
				'elementor_docs' => Leo_Helper::__( 'Documentation', 'elementor' ),
				'reload_page' => Leo_Helper::__( 'Reload Page', 'elementor' ),
				'session_expired_header' => Leo_Helper::__( 'Timeout', 'elementor' ),
				'session_expired_message' => Leo_Helper::__( 'Your session has expired. Please reload the page to continue editing.', 'elementor' ),
				'soon' => Leo_Helper::__( 'Soon', 'elementor' ),
				'unknown_value' => Leo_Helper::__( 'Unknown Value', 'elementor' ),
				
				// TODO: history.
				'history' => Leo_Helper::__( 'History', 'elementor' ),
				'template' => Leo_Helper::__( 'Template', 'elementor' ),
				'added' => Leo_Helper::__( 'Added', 'elementor' ),
				'removed' => Leo_Helper::__( 'Removed', 'elementor' ),
				'edited' => Leo_Helper::__( 'Edited', 'elementor' ),
				'moved' => Leo_Helper::__( 'Moved', 'elementor' ),
				'editing_started' => Leo_Helper::__( 'Editing Started', 'elementor' ),
				'style_pasted' => Leo_Helper::__( 'Style Pasted', 'elementor' ),
				'style_reset' => Leo_Helper::__( 'Style Reset', 'elementor' ),
				'all_content' => Leo_Helper::__( 'All Content', 'elementor' ),
				
				'custom_css' => Leo_Helper::__( 'Custom CSS', 'elementor' ),
				
				'see_it_in_action' => Leo_Helper::__( 'Active License', 'elementor' ),
				'promotion_header_message' => Leo_Helper::__( '%s Widget', 'elementor' ),
				'promotion_message' => Leo_Helper::__( 'Use %s widget and dozens more pro features to extend your toolbox and build sites faster and better.', 'elementor' ),
				
				'changes_lost' => Leo_Helper::__( 'You have unsaved changes!', 'elementor' ),
				'dialog_confirm_changes_lost' => Leo_Helper::__( 'Please return and save, otherwise your changes will be lost.', 'elementor' ),
				
				'language_dialog_title' => Leo_Helper::__( 'Erase content and import', 'elementor' ),
				'language_dialog_msg' => Leo_Helper::__( 'Please confirm that you want to erase content of this page and import content of other language', 'elementor' ),
				'file_manager' => Leo_Helper::__( 'File Manager', 'elementor' ),
			],
			'elementPromotionURL' => Leo_Helper::get_exit_to_dashboard( 'AdminLeoElementsLicense' ),
			'customIconsURL' => Leo_Helper::get_exit_to_dashboard( 'AdminLeoElementsLicense' ),
			'library_connect' => [
				'is_connected' => false
			],
			'isActive' => Leo_Helper::api_is_license_active(),
		];

		$localized_settings = [];

		/**
		 * Localize editor settings.
		 *
		 * Filters the editor localized settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $localized_settings Localized settings.
		 * @param int   $post_id            The ID of the current post being edited.
		 */
		$localized_settings = Leo_Helper::apply_filters( 'elementor/editor/localize_settings', $localized_settings, $this->_post_id );

		if ( ! empty( $localized_settings ) ) {
			$config = array_replace_recursive( $config, $localized_settings );
		}

		Utils::print_js_config( 'elementor-editor', 'ElementorConfig', $config );
		
		Leo_Helper::wp_localize_script( 'elementor-editor', 'baseAdminDir', Leo_Helper::get_base_admin_dir() );

		Leo_Helper::wp_enqueue_script( 'elementor-editor' );

		$plugin->controls_manager->enqueue_control_scripts();

		/**
		 * After editor enqueue scripts.
		 *
		 * Fires after Elementor editor scripts are enqueued.
		 *
		 * @since 1.0.0
		 */
		Leo_Helper::do_action( 'elementor/editor/after_enqueue_scripts' );
	}

	/**
	 * Enqueue styles.
	 *
	 * Registers all the editor styles and enqueues them.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_styles() {
		/**
		 * Before editor enqueue styles.
		 *
		 * Fires before Elementor editor styles are enqueued.
		 *
		 * @since 1.0.0
		 */
		Leo_Helper::do_action( 'elementor/editor/before_enqueue_styles' );

		$suffix = Utils::is_script_debug() ? '' : '.min';

		$direction_suffix = Leo_Helper::is_rtl() ? '-rtl' : '';

		Leo_Helper::wp_register_style(
			'font-awesome',
			LEOELEMENTS_ASSETS_URL . 'lib/font-awesome/css/font-awesome' . $suffix . '.css',
			[],
			'4.7.0'
		);
		
		Leo_Helper::wp_register_style(
			'line-awesome',
			LEOELEMENTS_ASSETS_URL . 'lib/line-awesome/line-awesome.min.css',
			[],
			'1.3.0'
		);

		Leo_Helper::wp_register_style(
			'pe-icon',
			LEOELEMENTS_ASSETS_URL . 'lib/pe-icon/Pe-icon-7-stroke.min.css',
			[],
			'1.2.0'
		);

		Leo_Helper::wp_register_style(
			'elementor-select2',
			LEOELEMENTS_ASSETS_URL . 'lib/e-select2/css/e-select2' . $suffix . '.css',
			[],
			'4.0.6-rc.1'
		);

		Leo_Helper::wp_register_style(
			'google-font-roboto',
			'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700',
			[],
			LEOELEMENTS_VERSION
		);

		Leo_Helper::wp_register_style(
			'flatpickr',
			LEOELEMENTS_ASSETS_URL . 'lib/flatpickr/flatpickr' . $suffix . '.css',
			[],
			'1.12.0'
		);
		
		Leo_Helper::wp_register_style(
			'elementor-common',
			LEOELEMENTS_ASSETS_URL . 'css/common' . $suffix . '.css',
			[],
			LEOELEMENTS_VERSION
		);
						
		Leo_Helper::wp_register_style(
			'elementor-editor',
			LEOELEMENTS_ASSETS_URL . 'css/editor' . $direction_suffix . $suffix . '.css',
			[
				'font-awesome',
				'elementor-common',
				'elementor-select2',
				'elementor-icons',
				'google-font-roboto',
				'flatpickr',
			],
			LEOELEMENTS_VERSION
		);

		Leo_Helper::wp_enqueue_style( 'elementor-editor' );
		
		Leo_Helper::wp_register_style(
			'elementor-leo-editor',
			LEOELEMENTS_ASSETS_URL . 'css/leo-editor' . $direction_suffix . $suffix . '.css',
			[],
			LEOELEMENTS_VERSION
		);
		
		Leo_Helper::wp_enqueue_style( 'elementor-leo-editor' );

		if ( Responsive::has_custom_breakpoints() ) {
			$breakpoints = Responsive::get_breakpoints();

			wp_add_inline_style( 'elementor-editor', '.elementor-device-tablet #elementor-preview-responsive-wrapper { width: ' . $breakpoints['md'] . 'px; }' );
		}

		/**
		 * After editor enqueue styles.
		 *
		 * Fires after Elementor editor styles are enqueued.
		 *
		 * @since 1.0.0
		 */
		Leo_Helper::do_action( 'elementor/editor/after_enqueue_styles' );
	}

	/**
	 * Get WordPress editor config.
	 *
	 * Config the default WordPress editor with custom settings for Elementor use.
	 *
	 * @since 1.9.0
	 * @access private
	 */
	private function get_wp_editor_config() {
		Leo_Helper::wp_print_jquery();
		
		_LEO_Editors::print_default_editor_scripts();
		_LEO_Editors::print_tinymce_scripts();
			
		ob_start();

		_LEO_Editors::editor(
			'%%EDITORCONTENT%%',
			'elementorwpeditor',
			[
				'editor_class' => 'elementor-wp-editor',
				'editor_height' => 250,
				'drag_drop_upload' => true,
			]
		);

		$config = ob_get_clean();
		
		_LEO_Editors::editor_js();

		return $config;
	}

	/**
	 * Editor head trigger.
	 *
	 * Fires the 'elementor/editor/wp_head' action in the head tag in Elementor
	 * editor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function editor_head_trigger() {
		/**
		 * Elementor editor head.
		 *
		 * Fires on Elementor editor head tag.
		 *
		 * Used to prints scripts or any other data in the head tag.
		 *
		 * @since 1.0.0
		 */
		Leo_Helper::do_action( 'elementor/editor/wp_head' );
	}

	/**
	 * Add editor template.
	 *
	 * Registers new editor templates.
	 *
	 * @since 1.0.0
	 * @deprecated 2.3.0 Use `Plugin::$instance->common->add_template()`
	 * @access public
	 *
	 * @param string $template Can be either a link to template file or template
	 *                         HTML content.
	 * @param string $type     Optional. Whether to handle the template as path
	 *                         or text. Default is `path`.
	 */
	public function add_editor_template( $template, $type = 'path' ) {
		// _deprecated_function( __METHOD__, '2.3.0', 'Plugin::$instance->common->add_template()' );

		$common = Plugin::$instance->common;

		if ( $common ) {
			Plugin::$instance->common->add_template( $template, $type );
		}
	}

	/**
	 * WP footer.
	 *
	 * Prints Elementor editor with all the editor templates, and render controls,
	 * widgets and content elements.
	 *
	 * Fired by `wp_footer` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wp_footer() {
		$plugin = Plugin::$instance;

		$plugin->controls_manager->render_controls();
		$plugin->widgets_manager->render_widgets_content();
		$plugin->elements_manager->render_elements_content();

		$plugin->schemes_manager->print_schemes_templates();

		$plugin->dynamic_tags->print_templates();

		$this->init_editor_templates();

		/**
		 * Elementor editor footer.
		 *
		 * Fires on Elementor editor before closing the body tag.
		 *
		 * Used to prints scripts or any other HTML before closing the body tag.
		 *
		 * @since 1.0.0
		 */
		Leo_Helper::do_action( 'elementor/editor/footer' );
	}

	/**
	 * Set edit mode.
	 *
	 * Used to update the edit mode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $edit_mode Whether the edit mode is active.
	 */
	public function set_edit_mode( $edit_mode ) {
		$this->_is_edit_mode = $edit_mode;
	}

	/**
	 * Editor constructor.
	 *
	 * Initializing Elementor editor and redirect from old URL structure of
	 * Elementor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$this->_is_edit_mode = Leo_Helper::is_admin();
		Leo_Helper::add_action( 'admin_action_elementor', [ $this, 'init' ] );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function filter_wp_link_query_args( $query ) {
		$library_cpt_key = array_search( Source_Local::CPT, $query['post_type'], true );
		if ( false !== $library_cpt_key ) {
			unset( $query['post_type'][ $library_cpt_key ] );
		}

		return $query;
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function filter_wp_link_query( $results ) {
            $post = $GLOBALS['_POST'];
		if ( isset( $post['editor'] ) && 'elementor' === $post['editor'] ) {
			$post_type_object = get_post_type_object( 'post' );
			$post_label = $post_type_object->labels->singular_name;

			foreach ( $results as & $result ) {
				if ( 'post' === get_post_type( $result['ID'] ) ) {
					$result['info'] = $post_label;
				}
			}
		}

		return $results;
	}

	/**
	 * Create nonce.
	 *
	 * If the user has edit capabilities, it creates a cryptographic token to
	 * give him access to Elementor editor.
	 *
	 * @since 1.8.1
	 * @since 1.8.7 The `$post_type` parameter was introduces.
	 * @deprecated 2.3.0 Use `Plugin::$instance->common->get_component( 'ajax' )->create_nonce()` instead
	 * @access public
	 *
	 * @param string $post_type The post type to check capabilities.
	 *
	 * @return null|string The nonce token, or `null` if the user has no edit
	 *                     capabilities.
	 */
	public function create_nonce( $post_type ) {
		// _deprecated_function( __METHOD__, '2.3.0', 'Plugin::$instance->common->get_component( \'ajax\' )->create_nonce()' );

		/** @var Ajax $ajax */
		$ajax = Plugin::$instance->common->get_component( 'ajax' );

		return $ajax->create_nonce();
	}

	/**
	 * Verify nonce.
	 *
	 * The user is given an amount of time to use the token, so therefore, since
	 * the user ID and `$action` remain the same, the independent variable is
	 * the time.
	 *
	 * @since 1.8.1
	 * @deprecated 2.3.0
	 * @access public
	 *
	 * @param string $nonce Nonce to verify.
	 *
	 * @return false|int If the nonce is invalid it returns `false`. If the
	 *                   nonce is valid and generated between 0-12 hours ago it
	 *                   returns `1`. If the nonce is valid and generated
	 *                   between 12-24 hours ago it returns `2`.
	 */
	public function verify_nonce( $nonce ) {
		// _deprecated_function( __METHOD__, '2.3.0', 'wp_verify_nonce()' );

		return wp_verify_nonce( $nonce );
	}

	/**
	 * Verify request nonce.
	 *
	 * Whether the request nonce verified or not.
	 *
	 * @since 1.8.1
	 * @deprecated 2.3.0 Use `Plugin::$instance->common->get_component( 'ajax' )->verify_request_nonce()` instead
	 * @access public
	 *
	 * @return bool True if request nonce verified, False otherwise.
	 */
	public function verify_request_nonce() {
		// _deprecated_function( __METHOD__, '2.3.0', 'Plugin::$instance->common->get_component( \'ajax\' )->verify_request_nonce()' );

		/** @var Ajax $ajax */
		$ajax = Plugin::$instance->common->get_component( 'ajax' );

		return $ajax->verify_request_nonce();
	}

	/**
	 * Verify ajax nonce.
	 *
	 * Verify request nonce and send a JSON request, if not verified returns an
	 * error.
	 *
	 * @since 1.9.0
	 * @deprecated 2.3.0
	 * @access public
	 */
	public function verify_ajax_nonce() {
		// _deprecated_function( __METHOD__, '2.3.0' );

		/** @var Ajax $ajax */
		$ajax = Plugin::$instance->common->get_component( 'ajax' );

		if ( ! $ajax->verify_request_nonce() ) {
			wp_send_json_error( new \WP_Error( 'token_expired', 'Nonce token expired.' ) );
		}
	}

	/**
	 * Init editor templates.
	 *
	 * Initialize default elementor templates used in the editor panel.
	 *
	 * @since 1.7.0
	 * @access private
	 */
	private function init_editor_templates() {
		$template_names = [
			'global',
			'panel',
			'panel-elements',
			'repeater',
			'templates',
			'navigator',
			'hotkeys',
			'history-panel-template',
			'revisions-panel-template',
		];

		foreach ( $template_names as $template_name ) {
			Plugin::$instance->common->add_template( LEOELEMENTS_PATH . "includes/editor-templates/$template_name.php" );
		}
	}
	
    private function get_elementor_data()
    {
        $context = \Context::getContext();
        $id_profile = Leo_Helper::$id_profile;
        $id_product_list = Leo_Helper::$id_product_list;
        $id_post = Leo_Helper::$id_post;
        $id_lang = Leo_Helper::$id_lang;
        $key_related = Leo_Helper::$key_related;
        $post_type = Leo_Helper::$post_type;
		$front_token = Leo_Helper::getFrontToken();
		$id_employee = is_object($context->employee) ? (int)$context->employee->id : \Tools::getValue('id_employee');
		
		$exit_to_dashboard = Leo_Helper::get_exit_to_dashboard('AdminLeoElementsHome');
		
		$content = [];
//		echo '<pre>' . "\n";
//                print_r($_GET);
//                print_r($_POST);
//                echo '</pre>' . "\n";
//                die();

        switch ( $post_type ) {
            case 'header':
				$params = [ 'id_leoelements_post' => $id_post, 'updateleoelements_post' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoElementsHeader', $params);
                break;
            case 'footer':
				$params = [ 'id_leoelements_post' => $id_post, 'updateleoelements_post' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoElementsFooter', $params);
                break;
            case 'home':
				$params = [ 'id_leoelements_post' => $id_post, 'updateleoelements_post' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoElementsHome', $params);
                break;
            case 'hook':
				$related = Leo_Helper::getContentByIdPost();
				$params = [ 'id_leoelements_related' => $related['id_leoelements_contents'], 'updateleoelements_related' => 1 ];
                                
                                Leo_Helper::$key_related = $related['hook'];
                                $key_related = Leo_Helper::$key_related;
                                
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoElementsHook', $params);
            case 'hook_product_list':
				$related = Leo_Helper::getContentByIdPost();
				$params = [ 'id_leoelements_related' => $related['id_leoelements_contents'], 'updateleoelements_related' => 1 ];
                                
                                Leo_Helper::$key_related = $related['hook'];
                                $key_related = Leo_Helper::$key_related;
                                
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoElementsHook', $params);
                break;
            case 'category':
				$params = [ 'id_category' => $key_related, 'updatecategory' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminCategories', $params);
                break;
            case 'hook_category_layout':
				$related = Leo_Helper::getContentByIdPost();
				$params = [ 'id_leoelements_related' => $related['id_leoelements_contents'], 'updateleoelements_related' => 1 ];
                                
                                Leo_Helper::$key_related = $related['hook'];
                                $key_related = Leo_Helper::$key_related;
                                
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoElementsHook', $params);
                break;
            case 'product':
				$params = [ 'id_product' => $key_related, 'updateproduct' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminProducts', $params);
                break;
            case 'hook_product_layout':
				$related = Leo_Helper::getContentByIdPost();
				$params = [ 'id_leoelements_related' => $related['id_leoelements_contents'], 'updateleoelements_related' => 1 ];
                                
                                Leo_Helper::$key_related = $related['hook'];
                                $key_related = Leo_Helper::$key_related;
                                
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoElementsHook', $params);
            case 'cms':
				$params = [ 'id_cms' => $key_related, 'updatecms' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminCmsContent', $params);
                break;
            case 'manufacturer':
				$params = [ 'id_manufacturer' => $key_related, 'updatemanufacturer' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminManufacturers', $params);
                break;
            case 'supplier':
				$params = [ 'id_supplier' => $key_related, 'updatesupplier' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminSuppliers', $params);
                break;
            case 'blog':
				$params = [ 'id_smart_blog_post' => $key_related, 'updatesmart_blog_post' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminBlogPost', $params);
                break;
            case 'leoguide':
				$params = [ 'id_leoguide_guide' => $key_related, 'update_leoguide' => 1 ];
				$exit_to_dashboard =  Leo_Helper::get_exit_to_dashboard('AdminLeoguideGuides', $params);
                break;
            default:
				\Tools::redirectAdmin( $exit_to_dashboard );
                break;
        }
						
		$permalink 	= Leo_Helper::get_permalink();
		$wp_preview = Leo_Helper::get_permalink( [ 'wp_preview' => $id_post , 'id_profile' => $id_profile] );
		$preview	= Leo_Helper::get_permalink( [ 'id_post' => $id_post, 'post_type' => $post_type, 'key_related' => $key_related, 'id_employee' => $id_employee, 'id_product_list' => $id_product_list, 'id_profile' => $id_profile, 'front_token' => $front_token ] );
                
                if(\Tools::getIsset('cw_hook') && \Tools::getValue('cw_hook')) {
                    $preview	= Leo_Helper::get_permalink( [
                        'id_post' => $id_post,
                        'post_type' => $post_type,
                        
                        'cw_hook' => \Tools::getValue('cw_hook'),
                        'key_related' => \Tools::getValue('cw_hook'),
                        
                        'id_employee' => $id_employee,
                        'id_product_list' => $id_product_list,
                        'id_profile' => $id_profile,
                        'front_token' => $front_token ] );
                }
                
                
		
		$obj = new \LeoElementsContentsModel($id_post, $id_lang);
		
		if ( \Validate::isLoadedObject( $obj ) ) {
			$content = (array) json_decode( $obj->content, true );
			$employee = new \Employee( $obj->id_employee );
			$display_name = $employee->firstname;
			$last_edited = sprintf( Leo_Helper::__( 'Last edited on %1$s by %2$s', 'elementor' ), '<time>' . $obj->date_upd . '</time>', $display_name );
		}else{
			$content = [];
			$last_edited = '';
		}
				
		$data = [];
		
		$data['raw_data'] = $this->get_elements_raw_data( $content );
		
		$data['document'] = [
			'id' => $id_post,
			'type' => 'page',
			'version' => LEOELEMENTS_VERSION,
			'remoteLibrary' => [
				'type'	=> 'page',
				'category'	=> 'post',
				'autoImportSettings' => false,
			],
			'last_edited' => $last_edited,
			'panel' => [
				'widgets_settings' => [],
				'elements_categories' => Plugin::$instance->elements_manager->get_categories(),
				'messages' => [
					/* translators: %s: the document title. */
					'publish_notification' => sprintf( Leo_Helper::__( 'Hurray! Your %s is live.', 'elementor' ), Leo_Helper::$post_title ),
				],
			],
			'container' => 'body',
			'urls' => [
				'exit_to_dashboard' => $exit_to_dashboard,
				'preview' => $preview,
				'wp_preview' => $wp_preview,
				'permalink' => $permalink,
			],
		];
		
		return $data;
    }
	
	/**
	 * @since 2.0.0
	 * @access public
	 *
	 * @param null $data
	 * @param bool $with_html_content
	 *
	 * @return array
	 */
	public function get_elements_raw_data( $data = [] ) {
		$editor_data = [];

		foreach ( $data as $element_data ) {
			$element = Plugin::$instance->elements_manager->create_element_instance( $element_data );

			if ( ! $element ) {
				continue;
			}

			$editor_data[] = $element->get_raw_data( true );
		} // End foreach().

		return $editor_data;
	}	
}
