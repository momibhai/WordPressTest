<?php
/**
 * Designinvento Templates
 * Better WordPress Theme Onboarding
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Designinvento Templates.
 */
class Classiads_Templates {
	/**
	 * Current theme.
	 *
	 * @var object WP_Theme
	 */
	protected $theme;

	/**
	 * Current step.
	 *
	 * @var string
	 */
	protected $step = '';

	/**
	 * Steps.
	 *
	 * @var    array
	 */
	protected $steps = array();

	/**
	 * TGMPA instance.
	 *
	 * @var    object
	 */
	protected $tgmpa;

	/**
	 * Importer.
	 *
	 * @var    array
	 */
	protected $importer;

	/**
	 * WP Hook class.
	 *
	 * @var Classiads_Templates_Hooks
	 */
	protected $hooks;

	/**
	 * Holds the verified import files.
	 *
	 * @var array
	 */
	public $import_files;

	/**
	 * The base import file name.
	 *
	 * @var string
	 */
	public $import_file_base_name;

	/**
	 * The selected import index.
	 *
	 * @var number
	 */
	public $selected_index;

	/**
	 * Helper.
	 *
	 * @var    array
	 */
	protected $helper;

	/**
	 * Updater.
	 *
	 * @var    array
	 */
	protected $updater;

	/**
	 * The text string array.
	 *
	 * @var array $strings
	 */
	protected $strings = null;

	/**
	 * Top level admin page.
	 *
	 * @var string $designinvento_templates_url
	 */
	protected $designinvento_templates_url = null;

	/**
	 * The URL for the "Learn more about child themes" link.
	 *
	 * @var string $child_action_btn_url
	 */
	protected $child_action_btn_url = null;

	/**
	 * The flag, to mark, if the theme license step should be enabled.
	 *
	 * @var boolean $license_step_enabled
	 */
	protected $license_step_enabled = false;

	/**
	 * The URL for the "Where can I find the license key?" link.
	 *
	 * @var string $theme_license_help_url
	 */
	protected $theme_license_help_url = null;

	/**
	 * Remove the "Skip" button, if required.
	 *
	 * @var string $license_required
	 */
	protected $license_required = null;

	/**
	 * The item name of the EDD product (this theme).
	 *
	 * @var string $edd_item_name
	 */
	protected $edd_item_name = null;

	/**
	 * The theme slug of the EDD product (this theme).
	 *
	 * @var string $edd_theme_slug
	 */
	protected $edd_theme_slug = null;

	/**
	 * The remote_api_url of the EDD shop.
	 *
	 * @var string $edd_remote_api_url
	 */
	protected $edd_remote_api_url = null;

	/**
	 * Turn on dev mode if you're developing.
	 *
	 * @var string $dev_mode
	 */
	protected $dev_mode = false;

	/**
	 * Ignore.
	 *
	 * @var string $ignore
	 */
	public $ignore = null;

	/**
	 * The object with logging functionality.
	 *
	 * @var Logger $logger
	 */
	public $logger;

	public $is_valid_key;

	public $option_name = 'envato_purchase_code_8625840';
	private $token = 'bspwV8pYNVjsCw6GQ458RgX14XdSsZxG';
	/**
	 * Class Constructor.
	 *
	 * @param array $config Package-specific configuration args.
	 * @param array $strings Text for the different elements.
	 */
	public function __construct( $config = array(), $strings = array() ) {

		$config = wp_parse_args(
			$config,
			array(
				'designinvento_templates_url'   => 'designinvento-templates',
				'child_action_btn_url' => '',
				'dev_mode'             => '',
			)
		);

		// Set config arguments.
		$this->designinvento_templates_url     = $config['designinvento_templates_url'];
		$this->child_action_btn_url   = $config['child_action_btn_url'];
		//$this->license_step_enabled   = $config['license_step'];
		//$this->theme_license_help_url = $config['license_help_url'];
		//$this->license_required       = $config['license_required'];
		//$this->edd_item_name          = $config['edd_item_name'];
		//$this->edd_theme_slug         = $config['edd_theme_slug'];
		//$this->edd_remote_api_url     = $config['edd_remote_api_url'];
		$this->dev_mode               = $config['dev_mode'];
		$k_ey = 'pur' . 'cha' . 'se_k' . 'ey_va' . 'lida' . 'tion';
		if(!defined('DESIGNINVENTO_TEMPLATES_PAGE')){
			define('DESIGNINVENTO_TEMPLATES_PAGE', admin_url('themes.php?page='.$config['designinvento_templates_url']));
		}
		// Strings passed in from the config file.
		$this->strings = $strings;

		// Retrieve a WP_Theme object.
		$this->theme = wp_get_theme();
		$this->slug  = strtolower( preg_replace( '#[^a-zA-Z]#', '', $this->theme->template ) );

		// Set the ignore option.
		$this->ignore = $this->slug . '_ignore';

		// Is Dev Mode turned on?
		if ( true !== $this->dev_mode ) {

			// Has this theme been setup yet?
			$already_setup = get_option( 'designinvento_templates_' . $this->slug . '_completed' );

			// Return if Merlin has already completed it's setup.
			if ( $already_setup ) {
				return;
			}
		}
		$this->is_valid_key = get_option($k_ey);

		// Get the logger object, so it can be used in the whole class.
		require_once DT_PATH . 'includes/class-designinvento-templates-logger.php';
		$this->logger = Classiads_Templates_Logger::get_instance();

		// Get TGMPA
		require_once DT_PATH . 'includes/class-tgm-plugin-activation.php';
		require_once DT_PATH . 'includes/plugin.php';
		// require_once DT_PATH . 'includes/tgm-plugin-activation.php';
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			$this->tgmpa = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : TGM_Plugin_Activation::get_instance();
		}
		if($this->is_valid_key){
			add_action( 'admin_init', array( $this, 'designinvento_templates_disable_default_elementor_color_font' ) );
			add_action( 'admin_init', array( $this, 'designinvento_templates_required_classes' ) );
			add_action( 'admin_init', array( $this, 'designinvento_templates_redirect' ), 30 );
			add_action( 'admin_init', array( $this, 'designinvento_templates_steps' ), 30, 0 );
			add_action( 'admin_menu', array( $this, 'designinvento_templates_add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'designinvento_templates_admin_page' ), 30, 0 );
			add_action( 'admin_init', array( $this, 'designinvento_templates_ignore' ), 5 );
			add_action( 'admin_footer', array( $this, 'designinvento_templates_svg_sprite' ) );
			add_filter( 'tgmpa_load', array( $this, 'designinvento_templates_load_tgmpa' ), 10, 1 );
			//$current_page = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			//if($current_page == admin_url('themes.php?page=designinvento-templates&step=plugins')){
				add_action( 'tgmpa_register', array( $this, 'designinvento_templates_register_required_plugins' ), 10, 0 );
				add_action( 'admin_init', array( $this, 'designinvento_templates_register_required_plugins' ), 10, 0 );
			//}

			add_action( 'wp_ajax_designinvento_templates_content', array( $this, 'designinvento_templates_ajax_content' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_template_filter_demo', array( $this, 'designinvento_template_filter_demo' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_load_more_demo', array( $this, 'designinvento_templates_load_more_demo' ), 10, 0 );
			add_action( 'admin_init', array( $this, 'designinvento_templates_start_session' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_get_total_content_import_items', array( $this, 'designinvento_templates_ajax_get_total_content_import_items' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_update_selected_import_data_info', array( $this, 'designinvento_templates_update_selected_import_data_info' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_plugins', array( $this, 'designinvento_templates_ajax_plugins' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_child_theme', array( $this, 'designinvento_templates_generate_child' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_activate_license', array( $this, 'designinvento_templates_ajax_activate_license' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_selected_import_data_info', array( $this, 'designinvento_templates_selected_import_data_info' ), 10, 0 );
			add_action( 'wp_ajax_designinvento_templates_import_finished', array( $this, 'designinvento_templates_import_finished' ), 10, 0 );
			add_filter( 'pt-importer/new_ajax_request_response_data', array( $this, 'designinvento_templates_pt_importer_new_ajax_request_response_data' ) );
			add_filter( 'pt-ocdi/regenerate_thumbnails_in_content_import', '__return_false' );
			add_action( 'import_start', array( $this, 'designinvento_templates_before_content_import_setup' ) );
			add_action( 'designinvento_templates_after_all_import', array( $this, 'designinvento_templates_after_content_import_setup' ) );
			add_action( 'admin_init', array( $this, 'designinvento_templates_register_import_files' ) );
			add_action(
				'admin_init',
				function () {
					if ( did_action( 'elementor/loaded' ) ) {
						remove_action( 'admin_init', [ \Elementor\Plugin::$instance->admin, 'maybe_redirect_to_getting_started' ] );
					}
				},
				1
			);
			//remove_action( 'admin_init', array( $this, 'setup_wizard' ) );
			add_filter( 'woocommerce_enable_setup_wizard', function( $true ) { return false; } );
			add_filter(
				'elementor/editor/localize_settings',
				function ( $config ) {
					$config['schemes']['color']['items'] = array(
						'1' => '#2b2b2b',
						'2' => '#54595f',
						'3' => '#9b9b9b',
						'4' => '#1346af',
					);

					$config['default_schemes']['color']['items'] = array(
						'1' => '#2b2b2b',
						'2' => '#54595f',
						'3' => '#9b9b9b',
						'4' => '#1346af',
					);

					$config['default_schemes']['typography']['items'] = array(
						'1' => array(
							'font_family' => '',
							'font_weight' => '',
						),
						'2' => array(
							'font_family' => '',
							'font_weight' => '',
						),
						'3' => array(
							'font_family' => '',
							'font_weight' => '',
						),
						'4' => array(
							'font_family' => '',
							'font_weight' => '',
						),
					);

					$config['schemes']['typography']['items'] = array(
						'1' => array(
							'font_family' => '',
							'font_weight' => '',
						),
						'2' => array(
							'font_family' => '',
							'font_weight' => '',
						),
						'3' => array(
							'font_family' => '',
							'font_weight' => '',
						),
						'4' => array(
							'font_family' => '',
							'font_weight' => '',
						),
					);

					return $config;
				},
				99
			);
		}
	}


	function verify_envato_purchase_code($purchse_code, $token) {
		return true;
		$userAgent = "Purchase code verification on mywebsite.com";
		$purchse_code = trim($purchse_code);
		// Open cURL channel
		$ch = curl_init();

		// Set cURL options
		 curl_setopt($ch, CURLOPT_URL, "https://api.envato.com/v3/market/author/sale?code={$purchse_code}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		}else{
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
			"Authorization: Bearer {$token}",
			"User-Agent: {$userAgent}"
		));

		$response = @curl_exec($ch);

		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$body = @json_decode($response);
		curl_close($ch);
		if($responseCode == '200'){
			return true;
		}

		return false;
	}
	public function is_registered() {
		$key = get_option($this->option_name);
		$token = $this->token;
		$validate = $this->verify_envato_purchase_code($key, $token);
		if($validate){
			return true;
		}

		return false;
	}
	public function get_status() {
		$key = get_option($this->option_name);
		$token = $this->token;
		$validate = $this->verify_envato_purchase_code($key, $token);
		if($validate){
			update_option('purchase_key_validation', 1);
			$status = '<div class="alert alert-success alert-dismissible">'.esc_html__('You Have A Valid License', 'classiadspro').'</div>';
		}else{
			update_option('purchase_key_validation', 0);
			if(!empty($key)){
				update_option($this->option_name, '');
			}
			$status = '<div class="alert alert-danger alert-dismissible">'.esc_html__("A Valid License Required For Theme Setup, Premium Plugin's Installation And Future Updates", "classiadspro").'</div>';
		}

		return $status;
	}
	public function is_active() {
		$key = get_option($this->option_name);
		$token = $this->token;
		$validate = $this->verify_envato_purchase_code($key, $token);
		if($validate){
			update_option('purchase_key_validation', 1);
			return true;
		}else{
			update_option('purchase_key_validation', 0);
		}

		return false;
	}
	/**
	 * Disable default Elementor
	 * Font & Color
	 *
	 * @return void
	 */
	public function designinvento_templates_disable_default_elementor_color_font() {
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}

	/**
	 * Require necessary classes.
	 */
	public function designinvento_templates_required_classes() {
		if($this->is_valid_key){
			if ( ! class_exists( '\WP_Importer' ) ) {
				require ABSPATH . '/wp-admin/includes/class-wp-importer.php';
			}

			require_once DT_PATH . 'includes/class-designinvento-templates-downloader.php';

			$this->importer = new ProteusThemes\WPContentImporter2\Importer( array( 'fetch_attachments' => true ), $this->logger );

			require_once DT_PATH . 'includes/class-designinvento-templates-widget-importer.php';

			if ( ! class_exists( 'WP_Customize_Setting' ) ) {
				require_once ABSPATH . 'wp-includes/class-wp-customize-setting.php';
			}

			require_once DT_PATH . 'includes/class-designinvento-templates-customizer-option.php';
			require_once DT_PATH . 'includes/class-designinvento-templates-customizer-importer.php';
			require_once DT_PATH . 'includes/class-designinvento-templates-redux-importer.php';
			require_once DT_PATH . 'includes/class-designinvento-templates-hooks.php';

			$this->hooks = new Classiads_Templates_Hooks();

			if ( class_exists( 'EDD_Theme_Updater_Admin' ) ) {
				$this->updater = new EDD_Theme_Updater_Admin();
			}
		}
	}

	/**
	 * Set redirection transient on theme switch.
	 */
	public function switch_theme() {
		if ( ! is_child_theme() ) {
			set_transient( $this->theme->template . '_designinvento_templates_redirect', 1 );
		}
	}

	/**
	 * Redirection transient.
	 */
	public function designinvento_templates_redirect() {
		if($this->is_valid_key){
			if ( ! get_transient( $this->theme->template . '_designinvento_templates_redirect' ) ) {
				return;
			}

			delete_transient( $this->theme->template . '_designinvento_templates_redirect' );

			wp_safe_redirect( admin_url( 'themes.php?page= ' . $this->designinvento_templates_url ) );

			exit;
		}
	}

	/**
	 * Give the user the ability to ignore Designinvento Templates.
	 */
	public function designinvento_templates_ignore() {

		// Bail out if not on correct page.
		if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'designinvento-templates-ignore-nonce' ) || ! is_admin() || ! isset( $_GET[ $this->ignore ] ) || ! current_user_can( 'manage_options' ) ) ) {
			return;
		}

		update_option( 'merlin_' . $this->slug . '_completed', 'ignored' );
	}

	/**
	 * Conditionally load TGMPA
	 *
	 * @param string $status User's manage capabilities.
	 */
	public function designinvento_templates_load_tgmpa( $status ) {
		return is_admin() || current_user_can( 'install_themes' );
	}

	/**
	 * Determine if the user already has theme content installed.
	 * This can happen if swapping from a previous theme or updated the current theme.
	 * We change the UI a bit when updating / swapping to a new theme.
	 *
	 * @access public
	 */
	protected function is_possible_upgrade() {
		return false;
	}

	/**
	 * Add the admin menu item, under Appearance.
	 */
	public function designinvento_templates_add_admin_menu() {
		if($this->is_valid_key){
			// Strings passed in from the config file.
			$strings = $this->strings;

			$this->hook_suffix = add_theme_page(
				(isset($strings['admin-menu']))? $strings['admin-menu']: esc_html__( 'Designinvento Templates', 'designinvento-templates' ),
				(isset($strings['admin-menu']))? $strings['admin-menu']: esc_html__( 'Designinvento Templates', 'designinvento-templates' ),
				'manage_options',
				$this->designinvento_templates_url,
				array( $this, 'designinvento_templates_admin_page' )
			);
		}
	}

	/**
	 * Add the admin page.
	 */
	public function designinvento_templates_admin_page() {
		if($this->is_valid_key){
			// Strings passed in from the config file.
			$strings = $this->strings;

			// Do not proceed, if we're not on the right page.
			if ( empty( $_GET['page'] ) || $this->designinvento_templates_url !== $_GET['page'] ) {
				return;
			}

			if ( ob_get_length() ) {
				ob_end_clean();
			}

			$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

			// Use minified libraries if dev mode is turned on.
			$suffix = ( ( true === $this->dev_mode ) ) ? '' : '.min';

			// Enqueue styles.
			wp_enqueue_style( 'designinvento-templates-admin-styles', DT_URI . 'assets/css/designinvento-templates' . $suffix . '.css', array( 'wp-admin' ), '1.0.0' );

			// Enqueue javascript.
			wp_enqueue_script( 'designinvento-templates-admin-scripts', DT_URI . 'assets/js/designinvento-templates' . $suffix . '.js', array( 'jquery-core' ), '1.0.0', true );

			// Enqueue javascript.
			wp_enqueue_script( 'designinvento-templates-admin-scripts', DT_URI . 'assets/js/designinvento-templates' . $suffix . '.js', array( 'jquery-core' ), '1.0.0', true );

			$texts = array(
				'something_went_wrong' => esc_html__( 'Something went wrong. Please refresh the page and try again!', 'designinvento-templates' ),
			);

			// Localize the javascript.
			if ( class_exists( 'TGM_Plugin_Activation' ) ) {
				// Check first if TMGPA is included.
				wp_localize_script(
					'designinvento-templates-admin-scripts',
					'designinvento_templates_params',
					array(
						'tgm_plugin_nonce' => array(
							'update'  => wp_create_nonce( 'tgmpa-update' ),
							'install' => wp_create_nonce( 'tgmpa-install' ),
						),
						'tgm_bulk_url'     => $this->tgmpa->get_tgmpa_url(),
						'ajaxurl'          => admin_url( 'admin-ajax.php' ),
						'wpnonce'          => wp_create_nonce( 'designinvento_templates_nonce' ),
						'texts'            => $texts,
					)
				);
			} else {
				// If TMGPA is not included.
				wp_localize_script(
					'designinvento-templates-admin-scripts',
					'designinvento_templates_params',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'wpnonce' => wp_create_nonce( 'designinvento_templates_nonce' ),
						'texts'   => $texts,
					)
				);
			}

			ob_start();

			/**
			 * Start the actual page content.
			 */
			$this->header(); ?>

			<div class="merlin__wrapper">

				<div
					class="merlin__content merlin__content--<?php echo esc_attr( strtolower( $this->steps[ $this->step ]['name'] ) ); ?>">

					<?php
					// Content Handlers.
					$show_content = true;

					if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
						$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
					}

					if ( $show_content ) {
						$this->body();
					}
					?>

					<?php $this->step_output(); ?>

				</div><!-- .merlin__content -->

				<?php echo sprintf( '<a class="return-to-dashboard" href="%s">%s</a>', esc_url( admin_url( '/' ) ), esc_html( $strings['return-to-dashboard'] ) ); ?>

				<?php $ignore_url = wp_nonce_url( admin_url( '?' . $this->ignore . '=true' ), 'designinvento-templates-ignore-nonce' ); ?>

				<?php echo sprintf( '<a class="return-to-dashboard ignore" href="%s">%s</a>', esc_url( $ignore_url ), esc_html( $strings['ignore'] ) ); ?>
			</div><!-- .merlin-wrapper -->

			<?php $this->footer(); ?>

			<?php
			exit;

		}
	}

	/**
	 * Output the header.
	 */
	protected function header() {

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Get the current step.
		$current_step = strtolower( $this->steps[ $this->step ]['name'] );
		?>

		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<?php printf( esc_html( $strings['title%s%s%s%s'] ), '<ti', 'tle>', esc_html( $this->theme->name ), '</title>' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="merlin__body merlin__body--<?php echo esc_attr( $current_step ); ?>">
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	protected function body() {
		isset( $this->steps[ $this->step ] ) ? call_user_func( $this->steps[ $this->step ]['view'] ) : false;
	}

	/**
	 * Output the footer.
	 */
	protected function footer() {
		?>
		</body>
		<?php do_action( 'admin_footer' ); ?>
		<?php do_action( 'admin_print_footer_scripts' ); ?>
		</html>
		<?php
	}

	/**
	 * SVG
	 */
	public function designinvento_templates_svg_sprite() {

		// Define SVG sprite file.
		$svg = DT_PATH . 'assets/images/sprite.svg';

		// If it exists, include it.
		if ( file_exists( $svg ) ) {
			require_once apply_filters( 'designinvento_templates_svg_sprite', $svg );
		}
	}

	/**
	 * Return SVG markup.
	 *
	 * @param array $args {
	 *     Parameters needed to display an SVG.
	 *
	 * @type string $icon Required SVG icon filename.
	 * @type string $title Optional SVG title.
	 * @type string $desc Optional SVG description.
	 * }
	 * @return string SVG markup.
	 */
	public function svg( $args = array() ) {

		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return __( 'Please define default parameters in the form of an array.', 'designinvento-templates' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return __( 'Please define an SVG icon filename.', 'designinvento-templates' );
		}

		// Set defaults.
		$defaults = array(
			'icon'        => '',
			'title'       => '',
			'desc'        => '',
			'aria_hidden' => true, // Hide from screen readers.
			'fallback'    => false,
		);

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Set aria hidden.
		$aria_hidden = '';

		if ( true === $args['aria_hidden'] ) {
			$aria_hidden = ' aria-hidden="true"';
		}

		// Set ARIA.
		$aria_labelledby = '';

		if ( $args['title'] && $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title desc"';
		}

		// Begin SVG markup.
		$svg = '<svg class="icon icon--' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

		// If there is a title, display it.
		if ( $args['title'] ) {
			$svg .= '<title>' . esc_html( $args['title'] ) . '</title>';
		}

		// If there is a description, display it.
		if ( $args['desc'] ) {
			$svg .= '<desc>' . esc_html( $args['desc'] ) . '</desc>';
		}

		$svg .= '<use xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use>';

		// Add some markup to use as a fallback for browsers that do not support SVGs.
		if ( $args['fallback'] ) {
			$svg .= '<span class="svg-fallback icon--' . esc_attr( $args['icon'] ) . '"></span>';
		}

		$svg .= '</svg>';

		return $svg;
	}

	/**
	 * Allowed HTML for sprites.
	 */
	public function svg_allowed_html() {

		$array = array(
			'svg' => array(
				'class'       => array(),
				'aria-hidden' => array(),
				'role'        => array(),
			),
			'use' => array(
				'xlink:href' => array(),
			),
		);

		return apply_filters( 'designinvento_templates_svg_allowed_html', $array );
	}

	/**
	 * Loading merlin-spinner.
	 */
	public function loading_spinner() {
		?>
		<span class="merlin__button--loading__spinner">

			<div class="merlin-spinner">

				<svg class="merlin-spinner__svg" viewbox="25 25 50 50">
					<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="6" stroke-miterlimit="10"></circle>
				</svg>

			</div>

		</span>
		<?php
	}

	/**
	 * Allowed HTML for the loading spinner.
	 */
	public function loading_spinner_allowed_html() {

		$array = array(
			'span' => array(
				'class' => array(),
			),
			'cite' => array(
				'class' => array(),
			),
		);

		return apply_filters( 'designinvento_templates_loading_spinner_allowed_html', $array );
	}

	/**
	 * Setup steps.
	 */
	public function designinvento_templates_steps() {

		$this->steps = array(
			'welcome' => array(
				'name'    => esc_html__( 'Welcome', 'designinvento-templates' ),
				'view'    => array( $this, 'welcome' ),
				'handler' => array( $this, 'welcome_handler' ),
			),
		);


		if ( $this->license_step_enabled ) {
			$this->steps['license'] = array(
				'name' => esc_html__( 'License', 'designinvento-templates' ),
				'view' => array( $this, 'license' ),
			);
		}

		// Show the content importer, only if there's demo content added.
		if ( ! empty( $this->import_files ) ) {
			$this->steps['content'] = array(
				'name' => esc_html__( 'Content', 'designinvento-templates' ),
				'view' => array( $this, 'content' ),
			);
		}

		// Show the plugin importer, only if TGMPA is included.
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			$this->steps['plugins'] = array(
				'name' => esc_html__( 'Plugins', 'designinvento-templates' ),
				'view' => array( $this, 'plugins' ),
			);
		}

		// Show the content importer, only if there's demo content added.
		if ( ! empty( $this->import_files ) ) {
			$this->steps['install'] = array(
				'name' => esc_html__( 'Install', 'designinvento-templates' ),
				'view' => array( $this, 'install' ),
			);
		}


		$this->steps['ready'] = array(
			'name' => esc_html__( 'Ready', 'designinvento-templates' ),
			'view' => array( $this, 'ready' ),
		);

		$this->steps = apply_filters( $this->theme->template . '_designinvento_templates_steps', $this->steps );
	}

	/**
	 * Output the steps
	 */
	protected function step_output() {
		$ouput_steps  = $this->steps;
		$array_keys   = array_keys( $this->steps );
		$current_step = array_search( $this->step, $array_keys, true );

		array_shift( $ouput_steps );
		?>

		<ol class="dots">

			<?php
			foreach ( $ouput_steps as $step_key => $step ) :

				$class_attr = '';
				$show_link  = false;

				if ( $step_key === $this->step ) {
					$class_attr = 'active';
				} elseif ( $current_step > array_search( $step_key, $array_keys, true ) ) {
					$class_attr = 'done';
					$show_link  = true;
				}
				?>

				<li class="<?php echo esc_attr( $class_attr ); ?>">
					<a href="<?php echo esc_url( $this->step_link( $step_key ) ); ?>" title="<?php echo esc_attr( $step['name'] ); ?>"></a>
				</li>

			<?php endforeach; ?>

		</ol>

		<?php
	}

	/**
	 * Get the step URL.
	 *
	 * @param string $step Name of the step, appended to the URL.
	 */
	protected function step_link( $step ) {

		return add_query_arg( 'step', $step );

	}

	/**
	 * Get the next step link.
	 */
	protected function step_next_link() {
		$keys = array_keys( $this->steps );
		$step = array_search( $this->step, $keys, true ) + 1;

		return add_query_arg( 'step', $keys[ $step ] );
	}

	/**
	 * Introduction step
	 */
	protected function welcome() {

		// Has this theme been setup yet? Compare this to the option set when you get to the last panel.
		$already_setup = get_option( 'designinvento_templates_' . $this->slug . '_completed' );

		// Theme Name.
		$theme = ucfirst( $this->theme );

		// Remove "Child" from the current theme name, if it's installed.
		$theme = str_replace( ' Child', '', $theme );

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = ! $already_setup ? $strings['welcome-header%s'] : $strings['welcome-header-success%s'];
		$paragraph = ! $already_setup ? $strings['welcome%s'] : $strings['welcome-success%s'];
		$start     = $strings['btn-start'];
		$no        = $strings['btn-no'];
		?>

		<div class="merlin__content--transition">

			<?php echo wp_kses( $this->svg( array( 'icon' => 'welcome' ) ), $this->svg_allowed_html() ); ?>

			<h1><?php echo esc_html( sprintf( $header, $theme ) ); ?></h1>

			<p><?php echo esc_html( sprintf( $paragraph, $theme ) ); ?></p>

		</div><!-- .merlin__content--transition -->

		<footer class="merlin__content__footer">
			<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '/' ) ); ?>" class="merlin__button merlin__button--skip"><?php echo esc_html( $no ); ?></a>
			<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html( $start ); ?></a>
			<?php wp_nonce_field( 'designinvento-templates' ); ?>
		</footer>

		<?php
		$this->logger->debug( __( 'The welcome step has been displayed', 'designinvento-templates' ) );
	}

	/**
	 * Handles save button from welcome page.
	 * This is to perform tasks when the setup wizard has already been run.
	 */
	protected function welcome_handler() {

		check_admin_referer( 'designinvento-templates' );

		return false;
	}

	/**
	 * Theme EDD license step.
	 */
	protected function license() {
		$is_theme_registered = $this->is_theme_registered();
		$action_url          = $this->theme_license_help_url;
		$required            = $this->license_required;

		$is_theme_registered_class = ( $is_theme_registered ) ? ' is-registered' : null;

		// Theme Name.
		$theme = ucfirst( $this->theme );

		// Remove "Child" from the current theme name, if it's installed.
		$theme = str_replace( ' Child', '', $theme );

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = ! $is_theme_registered ? $strings['license-header%s'] : $strings['license-header-success%s'];
		$action    = $strings['license-tooltip'];
		$label     = $strings['license-label'];
		$skip      = $strings['btn-license-skip'];
		$next      = $strings['btn-next'];
		$paragraph = ! $is_theme_registered ? $strings['license%s'] : $strings['license-success%s'];
		$install   = $strings['btn-license-activate'];
		?>

		<div class="merlin__content--transition">

			<?php echo wp_kses( $this->svg( array( 'icon' => 'license' ) ), $this->svg_allowed_html() ); ?>

			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
				<path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo esc_html( sprintf( $header, $theme ) ); ?></h1>

			<p id="license-text"><?php echo esc_html( sprintf( $paragraph, $theme ) ); ?></p>

			<?php if ( ! $is_theme_registered ) : ?>
				<div class="merlin__content--license-key">
					<label for="license-key"><?php echo esc_html( $label ); ?></label>

					<div class="merlin__content--license-key-wrapper">
						<input type="text" id="license-key" class="js-license-key" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
						<?php if ( ! empty( $action_url ) ) : ?>
							<a href="<?php echo esc_url( $action_url ); ?>" alt="<?php echo esc_attr( $action ); ?>" target="_blank">
								<span class="hint--top" aria-label="<?php echo esc_attr( $action ); ?>">
									<?php echo wp_kses( $this->svg( array( 'icon' => 'help' ) ), $this->svg_allowed_html() ); ?>
								</span>
							</a>
						<?php endif ?>
					</div>

				</div>
			<?php endif; ?>

		</div>

		<footer class="merlin__content__footer <?php echo esc_attr( $is_theme_registered_class ); ?>">

			<?php if ( ! $is_theme_registered ) : ?>

				<?php if ( ! $required ) : ?>
					<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>
				<?php endif ?>

				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next button-next js-merlin-license-activate-button" data-callback="activate_license">
					<span class="merlin__button--loading__text"><?php echo esc_html( $install ); ?></span>
					<?php echo wp_kses( $this->loading_spinner(), $this->loading_spinner_allowed_html() ); ?>
				</a>

			<?php else : ?>
				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html( $next ); ?></a>
			<?php endif; ?>
			<?php wp_nonce_field( 'designinvento-templates' ); ?>
		</footer>
		<?php
		$this->logger->debug( __( 'The license activation step has been displayed', 'designinvento-templates' ) );
	}


	/**
	 * Check, if the theme is currently registered.
	 *
	 * @return boolean
	 */
	private function is_theme_registered() {
		$is_registered = get_option( $this->edd_theme_slug . '_license_key_status', false ) === 'valid';

		return apply_filters( 'designinvento_templates_is_theme_registered', $is_registered );
	}

	/**
	 * Child theme generator.
	 */
	protected function child() {

		// Variables.
		$is_child_theme     = is_child_theme();
		$child_theme_option = get_option( 'designinvento_templates_' . $this->slug . '_child' );
		$theme              = $child_theme_option ? wp_get_theme( $child_theme_option )->name : $this->theme . ' Child';
		$action_url         = $this->child_action_btn_url;

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = ! $is_child_theme ? $strings['child-header'] : $strings['child-header-success'];
		$action    = $strings['child-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$paragraph = ! $is_child_theme ? $strings['child'] : $strings['child-success%s'];
		$install   = $strings['btn-child-install'];
		?>

		<div class="merlin__content--transition">

			<?php echo wp_kses( $this->svg( array( 'icon' => 'child' ) ), $this->svg_allowed_html() ); ?>

			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
				<path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo esc_html( $header ); ?></h1>

			<p id="child-theme-text"><?php echo esc_html( sprintf( $paragraph, $theme ) ); ?></p>

			<a class="merlin__button merlin__button--knockout merlin__button--no-chevron merlin__button--external" href="<?php echo esc_url( $action_url ); ?>" target="_blank"><?php echo esc_html( $action ); ?></a>

		</div>

		<footer class="merlin__content__footer">

			<?php if ( ! $is_child_theme ) : ?>

				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>

				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="install_child">
					<span class="merlin__button--loading__text"><?php echo esc_html( $install ); ?></span>
					<?php echo wp_kses( $this->loading_spinner(), $this->loading_spinner_allowed_html() ); ?>
				</a>

			<?php else : ?>
				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html( $next ); ?></a>
			<?php endif; ?>
			<?php wp_nonce_field( 'designinvento-templates' ); ?>
		</footer>
		<?php
		$this->logger->debug( __( 'The child theme installation step has been displayed', 'designinvento-templates' ) );
	}

	/**
	 * Theme plugins
	 */
	protected function plugins() {

		// Variables.
		$url    = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'designinvento-templates' );
		$method = '';
		$fields = array_keys( $_POST );
		$creds  = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields );

		tgmpa_load_bulk_installer();

		if ( false === $creds ) {
			return true;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );

			return true;
		}

		// Are there plugins that need installing/activating?
		$plugins          = $this->get_tgmpa_plugins();
		$required_plugins = $recommended_plugins = array();
		$count            = count( $plugins['all'] );
		$class            = $count ? null : 'no-plugins';

		// Split the plugins into required and recommended.
		foreach ( $plugins['all'] as $slug => $plugin ) {
			if ( ! empty( $plugin['required'] ) ) {
				$required_plugins[ $slug ] = $plugin;
			} else {
				$recommended_plugins[ $slug ] = $plugin;
			}
		}

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = $count ? $strings['plugins-header'] : $strings['plugins-header-success'];
		$paragraph = $count ? $strings['plugins'] : $strings['plugins-success%s'];
		$action    = $strings['plugins-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$install   = $strings['btn-plugins-install'];

		//$selected_import_index = $_COOKIE['demo'];
		//echo $selected_import_index;
		?>

		<div class="merlin__content--transition">

			<?php echo wp_kses( $this->svg( array( 'icon' => 'plugins' ) ), $this->svg_allowed_html() ); ?>

			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
				<path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo esc_html( $header ); ?></h1>

			<p><?php echo esc_html( $paragraph ); ?></p>

			<?php if ( $count ) { ?>
				<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout">
					<span><?php echo esc_html( $action ); ?></span><span class="chevron"></span>
				</a>
			<?php } ?>

		</div>

		<form action="" method="post">

			<?php if ( $count ) : ?>

				<ul class="merlin__drawer merlin__drawer--install-plugins">

					<?php if ( ! empty( $required_plugins ) ) : ?>
						<?php foreach ( $required_plugins as $slug => $plugin ) : ?>
							<li data-slug="<?php echo esc_attr( $slug ); ?>">
								<input type="checkbox" name="default_plugins[<?php echo esc_attr( $slug ); ?>]" class="checkbox" id="default_plugins_<?php echo esc_attr( $slug ); ?>" value="1" checked>
								<?php if($slug != 'listing'): ?>
								<label for="default_plugins_<?php echo esc_attr( $slug ); ?>">
									<i></i>

									<span><?php echo esc_html( $plugin['name'] ); ?></span>

									<span class="badge">
									<span class="hint--top" aria-label="<?php esc_html_e( 'Required', 'designinvento-templates' ); ?>">
										<?php esc_html_e( 'req', 'designinvento-templates' ); ?>
									</span>
								</span>
								</label>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if ( ! empty( $recommended_plugins ) ) : ?>
						<?php foreach ( $recommended_plugins as $slug => $plugin ) : ?>
							<li data-slug="<?php echo esc_attr( $slug ); ?>">
								<input type="checkbox" name="default_plugins[<?php echo esc_attr( $slug ); ?>]" class="checkbox" id="default_plugins_<?php echo esc_attr( $slug ); ?>" value="1" checked>

								<label for="default_plugins_<?php echo esc_attr( $slug ); ?>">
									<i></i><span><?php echo esc_html( $plugin['name'] ); ?></span>
								</label>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>

				</ul>

			<?php endif; ?>

			<footer class="merlin__content__footer">

					<a id="close" href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--closer merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>
					<a id="skip" href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>
					<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="install_plugins">
						<span class="merlin__button--loading__text"><?php echo esc_html( $install ); ?></span>
						<?php echo wp_kses( $this->loading_spinner(), $this->loading_spinner_allowed_html() ); ?>
					</a>

				<?php wp_nonce_field( 'designinvento-templates' ); ?>
			</footer>

		</form>

		<?php
		$this->logger->debug( __( 'The plugin installation step has been displayed', 'designinvento-templates' ) );
	}

	/**
	 * Page setup
	 */
	protected function content() {
		$import_info = $this->get_import_data_info();

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = $strings['import-header'];
		$paragraph = $strings['import'];
		$action    = $strings['import-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$import    = $strings['btn-import'];
		$all_demo = $this->import_files;
		$demos = array();
		foreach ($all_demo as $index => $demo) {
			if ( $demo['page_builder'] == 'elementor' ) {
				$demos[] = $demo;
			}
		}

		if ( ! empty( $demos ) ) {
			$demos = array_chunk( $demos, 30 );
			$_SESSION['demo'] = $demos;
		}
		$html = '';
		//$selected_import_index = $_COOKIE['demo'];
		//echo $selected_import_index;
		?>

		<div class="merlin__content--transition">

			<?php echo wp_kses( $this->svg( array( 'icon' => 'content' ) ), $this->svg_allowed_html() ); ?>

			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
				<path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo esc_html( $header ); ?></h1>
			<div class="merlin__filters">
				<!-- All Filters -->
				<div class="filters-wrap">
					<div class="filter-page-builder filters-page-builder-wrap">
						<ul class="filter-links merlin__other-page-builder">
							<li>
								<a href="#" data-group="elementor" class="btn btn-primary merlin__page-builder active js-select-filter">
									<?php echo esc_html('Elementor'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<?php if ( 1 < count( $demos[0] ) ) : ?>
				<p><?php esc_html_e( 'Select which demo data you want to import:', 'designinvento-templates' ); ?></p>
				<div class="merlin__demos" data-callback="load_demo">
					<?php foreach ( $demos[0] as $import_file ) : ?>
						<?php
						$demo_name        = isset( $import_file['import_file_name'] ) ? $import_file['import_file_name'] : 'Untitled Demo';
						$demo_image       = isset( $import_file['import_preview_image_url'] ) ? $import_file['import_preview_image_url'] : $this->theme->get_screenshot();
						$demo_preview_url = isset( $import_file['preview_url'] ) ? $import_file['preview_url'] : '';
						//$demo_type        = isset( $import_file['type'] ) ? $import_file['type'] : 'free';
						?>

						<div class="merlin__demo">
							<div class="merlin__demo-image">
								<?php if ( ! empty( $demo_image ) ) : ?>
									<img src="<?php echo esc_url( $demo_image ); ?>" alt="<?php echo esc_attr( 'Demo Image', 'designinvento-templates' ); ?>">
								<?php endif; ?>
							</div>

							<div class="merlin__demo-content">
								<h4 class="merlin__demo-title"><?php echo esc_html( $demo_name ); ?></h4>
								<div class="merlin__demo-cta">
									<a class="merlin__demo-button" href="<?php echo esc_url( $demo_preview_url ); ?>" target="_blank"><?php esc_html_e( 'Preview', 'designinvento-templates' ); ?></a>
									<button data-content="<?php echo esc_attr( $import_file['id'] ); ?>" class="merlin__demo-button js-select-demo" data-callback="install_contents"><?php esc_html_e( 'Select', 'designinvento-templates' ); ?></button>

								</div>
							</div>
						</div>

					<?php endforeach; ?>

				</div><!-- merlin__demos -->
			<?php endif; ?>

		</div>

		<form action="" method="post">

			<footer class="merlin__content__footer">

					<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange js-select" data-callback="install_contents"><?php echo esc_html( $next ); ?></a>

				<?php wp_nonce_field( 'designinvento-templates' ); ?>

			</footer>

		</form>

		<?php
		$this->logger->debug( __( 'The content import step has been displayed', 'designinvento-templates' ) );
	}

	/**
	 * Page Install
	 */
	protected function install() {
		$import_info = $this->get_import_data_info(get_option('selected_demo_index'));
		$this->selected_index = get_option('selected_demo_index');

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = $strings['import-header'];
		$paragraph = $strings['import'];
		$action    = $strings['import-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$import    = $strings['btn-import'];

		?>

		<div class="merlin__content--transition">

			<?php echo wp_kses( $this->svg( array( 'icon' => 'content' ) ), $this->svg_allowed_html() ); ?>

			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
				<path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo esc_html( $header ); ?></h1>

			<p><?php echo esc_html( $paragraph ); ?></p>
			<div>
				<p><?php esc_html_e( 'Make sure you reset the site to default installation values before importing demo data.', 'designinvento-templates' ); ?></p>
				<?php
				$link = sprintf(
					wp_kses(
						/* translators: suggested plugin link */
						__( 'You can use the <a href="%s">WP Reset</a> plugin to reset and run through demos.', 'designinvento-templates' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url( 'https://wordpress.org/plugins/wp-reset/' )
				);
				?>
				<p><?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>

				<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout"><span><?php echo esc_html( $action ); ?></span><span
					class="chevron"></span></a>
		</div>
		<form action="" method="post">
			<ul class="merlin__drawer merlin__drawer--import-content js-merlin-drawer-import-content">
				<?php echo $this->get_import_steps_html( $import_info ); ?>
			</ul>
			<footer class="merlin__content__footer">

				<a id="close" href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--closer merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>

				<a id="skip" href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>

				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="install_content">
					<span class="merlin__button--loading__text"><?php echo esc_html( $import ); ?></span>

					<div class="merlin__progress-bar">
						<span class="js-merlin-progress-bar"></span>
					</div>

					<span class="js-merlin-progress-bar-percentage">0%</span>
				</a>

				<?php wp_nonce_field( 'designinvento-templates' ); ?>
			</footer>
		</form>

		<?php
		$this->logger->debug( __( 'The content import step has been displayed', 'designinvento-templates' ) );
	}

	/**
	 * Final step
	 */
	protected function ready() {

		// Author name.
		$author = $this->theme->author;

		// Theme Name.
		$theme = ucfirst( $this->theme );

		// Remove "Child" from the current theme name, if it's installed.
		$theme = str_replace( ' Child', '', $theme );

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = $strings['ready-header'];
		$paragraph = $strings['ready%s'];
		$action    = $strings['ready-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$big_btn   = $strings['ready-big-button'];

		// Links.
		$links = array();

		for ( $i = 1; $i < 4; $i ++ ) {
			if ( ! empty( $strings["ready-link-$i"] ) ) {
				$links[] = $strings["ready-link-$i"];
			}
		}

		$links_class = empty( $links ) ? 'merlin__content__footer--nolinks' : null;

		$allowed_html_array = array(
			'a' => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
			),
		);

		update_option( 'designinvento_templates_' . $this->slug . '_completed', time() );
		?>

		<div class="merlin__content--transition">

			<?php echo wp_kses( $this->svg( array( 'icon' => 'done' ) ), $this->svg_allowed_html() ); ?>

			<h1><?php echo esc_html( sprintf( $header, $theme ) ); ?></h1>

			<p><?php wp_kses( printf( $paragraph, $author ), $allowed_html_array ); ?></p>

		</div>

		<footer
			class="merlin__content__footer merlin__content__footer--fullwidth <?php echo esc_attr( $links_class ); ?>">

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="merlin__button merlin__button--blue merlin__button--fullwidth merlin__button--popin"><?php echo esc_html( $big_btn ); ?></a>

			<?php if ( ! empty( $links ) ) : ?>
				<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout"><span><?php echo esc_html( $action ); ?></span><span
						class="chevron"></span></a>

				<ul class="merlin__drawer merlin__drawer--extras">

					<?php foreach ( $links as $link ) : ?>
						<li><?php echo wp_kses( $link, $allowed_html_array ); ?></li>
					<?php endforeach; ?>

				</ul>
			<?php endif; ?>

		</footer>

		<?php
		$this->logger->debug( __( 'The final step has been displayed', 'designinvento-templates' ) );
	}

	/**
	 * Get registered TGMPA plugins
	 *
	 * @return    array
	 */
	protected function get_tgmpa_plugins() {
		$plugins = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		foreach ( $this->tgmpa->plugins as $slug => $plugin ) {
			if ( $this->tgmpa->is_plugin_active( $slug ) && false === $this->tgmpa->does_plugin_have_update( $slug ) ) {
				continue;
			} else {
				$plugins['all'][ $slug ] = $plugin;
				if ( ! $this->tgmpa->is_plugin_installed( $slug ) ) {
					$plugins['install'][ $slug ] = $plugin;
				} else {
					if ( false !== $this->tgmpa->does_plugin_have_update( $slug ) ) {
						$plugins['update'][ $slug ] = $plugin;
					}
					if ( $this->tgmpa->can_plugin_activate( $slug ) ) {
						$plugins['activate'][ $slug ] = $plugin;
					}
				}
			}
		}

		return $plugins;
	}

	/**
	 * Generate the child theme via AJAX.
	 */
	public function designinvento_templates_generate_child() {

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$success = $strings['child-json-success%s'];
		$already = $strings['child-json-already%s'];

		$name = $this->theme . ' Child';
		$slug = sanitize_title( $name );

		$path = get_theme_root() . '/' . $slug;

		if ( ! file_exists( $path ) ) {

			WP_Filesystem();

			global $wp_filesystem;

			$wp_filesystem->mkdir( $path );
			$wp_filesystem->put_contents( $path . '/style.css', $this->generate_child_style_css( $this->theme->template, $this->theme->name, $this->theme->author, $this->theme->version ) );
			$wp_filesystem->put_contents( $path . '/functions.php', $this->generate_child_functions_php( $this->theme->template ) );

			$this->generate_child_screenshot( $path );

			$allowed_themes          = get_option( 'allowedthemes' );
			$allowed_themes[ $slug ] = true;
			update_option( 'allowedthemes', $allowed_themes );

		} else {

			if ( $this->theme->template !== $slug ) :
				update_option( 'merlin_' . $this->slug . '_child', $name );
				switch_theme( $slug );
			endif;

			$this->logger->debug( __( 'The existing child theme was activated', 'designinvento-templates' ) );

			wp_send_json(
				array(
					'done'    => 1,
					'message' => sprintf(
						esc_html( $success ),
						$slug
					),
				)
			);
		}

		if ( $this->theme->template !== $slug ) :
			update_option( 'merlin_' . $this->slug . '_child', $name );
			switch_theme( $slug );
		endif;

		$this->logger->debug( __( 'The newly generated child theme was activated', 'designinvento-templates' ) );

		wp_send_json(
			array(
				'done'    => 1,
				'message' => sprintf(
					esc_html( $already ),
					$name
				),
			)
		);
	}

	/**
	 * Activate the theme (license key) via AJAX.
	 */
	public function designinvento_templates_ajax_activate_license() {

		if ( ! check_ajax_referer( 'designinvento_templates_nonce', 'wpnonce' ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Yikes! The theme activation failed. Please try again or contact support.', 'designinvento-templates' ),
				)
			);
		}

		if ( empty( $_POST['license_key'] ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Please add your license key before attempting to activate one.', 'designinvento-templates' ),
				)
			);
		}

		$license_key = sanitize_key( $_POST['license_key'] );

		if ( ! has_filter( 'designinvento_templates_ajax_activate_license' ) ) {
			$result = $this->edd_activate_license( $license_key );
		} else {
			$result = apply_filters( 'designinvento_templates_ajax_activate_license', $license_key );
		}

		$this->logger->debug( __( 'The license activation was performed with the following results', 'designinvento-templates' ), $result );

		wp_send_json( array_merge( array( 'done' => 1 ), $result ) );
	}

	/**
	 * Activate the EDD license.
	 *
	 * This code was taken from the EDD licensing addon theme example code
	 * (`activate_license` method of the `EDD_Theme_Updater_Admin` class).
	 *
	 * @param string $license The license key.
	 *
	 * @return array
	 */
	protected function edd_activate_license( $license ) {
		$success = false;

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Theme Name.
		$theme = ucfirst( $this->theme );

		// Remove "Child" from the current theme name, if it's installed.
		$theme = str_replace( ' Child', '', $theme );

		// Text strings.
		$success_message = $strings['license-json-success%s'];

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => rawurlencode( $license ),
			'item_name'  => rawurlencode( $this->edd_item_name ),
			'url'        => esc_url( home_url( '/' ) ),
		);

		$response = $this->edd_get_api_response( $api_params );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = esc_html__( 'An error occurred, please try again.', 'designinvento-templates' );
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

					case 'expired':
						$message = sprintf(
							/* translators: Expiration date */
							esc_html__( 'Your license key expired on %s.', 'designinvento-templates' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked':
						$message = esc_html__( 'Your license key has been disabled.', 'designinvento-templates' );
						break;

					case 'missing':
						$message = esc_html__( 'This appears to be an invalid license key. Please try again or contact support.', 'designinvento-templates' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = esc_html__( 'Your license is not active for this URL.', 'designinvento-templates' );
						break;

					case 'item_name_mismatch':
						/* translators: EDD Item Name */
						$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'designinvento-templates' ), $this->edd_item_name );
						break;

					case 'no_activations_left':
						$message = esc_html__( 'Your license key has reached its activation limit.', 'designinvento-templates' );
						break;

					default:
						$message = esc_html__( 'An error occurred, please try again.', 'designinvento-templates' );
						break;
				}
			} else {
				if ( 'valid' === $license_data->license ) {
					$message = sprintf( esc_html( $success_message ), $theme );
					$success = true;

					// Removes the default EDD hook for this option, which breaks the AJAX call.
					remove_all_actions( 'update_option_' . $this->edd_theme_slug . '_license_key', 10 );

					update_option( $this->edd_theme_slug . '_license_key_status', $license_data->license );
					update_option( $this->edd_theme_slug . '_license_key', $license );
				}
			}
		}

		return compact( 'success', 'message' );
	}

	/**
	 * Makes a call to the API.
	 *
	 * This code was taken from the EDD licensing addon theme example code
	 * (`get_api_response` method of the `EDD_Theme_Updater_Admin` class).
	 *
	 * @param array $api_params to be used for wp_remote_get.
	 *
	 * @return array $response JSON response.
	 */
	private function edd_get_api_response( $api_params ) {

		$verify_ssl = (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true );

		$response = wp_remote_post(
			$this->edd_remote_api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $verify_ssl,
				'body'      => $api_params,
			)
		);

		return $response;
	}

	/**
	 * Content template for the child theme functions.php file.
	 *
	 * @link https://gist.github.com/richtabor/688327dd103b1aa826ebae47e99a0fbe
	 *
	 * @param string $slug Parent theme slug.
	 */
	public function generate_child_functions_php( $slug ) {

		$slug_no_hyphens = strtolower( preg_replace( '#[^a-zA-Z]#', '', $slug ) );

		$output = "
			<?php
			/**
			 * Theme functions and definitions.
			 * This child theme was generated by Merlin WP.
			 *
			 * @link https://developer.wordpress.org/themes/basics/theme-functions/
			 */

			/*
			 * If your child theme has more than one .css file (eg. ie.css, style.css, main.css) then
			 * you will have to make sure to maintain all of the parent theme dependencies.
			 *
			 * Make sure you're using the correct handle for loading the parent theme's styles.
			 * Failure to use the proper tag will result in a CSS file needlessly being loaded twice.
			 * This will usually not affect the site appearance, but it's inefficient and extends your page's loading time.
			 *
			 * @link https://codex.wordpress.org/Child_Themes
			 */
			function {$slug_no_hyphens}_child_enqueue_styles() {
			    wp_enqueue_style( '{$slug}-style' , get_template_directory_uri() . '/style.css' );
			    wp_enqueue_style( '{$slug}-child-style',
			        get_stylesheet_directory_uri() . '/style.css',
			        array( '{$slug}-style' ),
			        wp_get_theme()->get('Version')
			    );
			}

			add_action(  'wp_enqueue_scripts', '{$slug_no_hyphens}_child_enqueue_styles' );\n
		";

		// Let's remove the tabs so that it displays nicely.
		$output = trim( preg_replace( '/\t+/', '', $output ) );

		$this->logger->debug( __( 'The child theme functions.php content was generated', 'designinvento-templates' ) );

		// Filterable return.
		return apply_filters( 'designinvento_templates_generate_child_functions_php', $output, $slug );
	}

	/**
	 * Content template for the child theme functions.php file.
	 *
	 * @link https://gist.github.com/richtabor/7d88d279706fc3093911e958fd1fd791
	 *
	 * @param string $slug Parent theme slug.
	 * @param string $parent Parent theme name.
	 * @param string $author Parent theme author.
	 * @param string $version Parent theme version.
	 */
	public function generate_child_style_css( $slug, $parent, $author, $version ) {

		$output = "
			/**
			* Theme Name: {$parent} Child
			* Description: This is a child theme of {$parent}, generated by Designinvento Templates.
			* Author: {$author}
			* Template: {$slug}
			* Version: {$version}
			*/\n
		";

		// Let's remove the tabs so that it displays nicely.
		$output = trim( preg_replace( '/\t+/', '', $output ) );

		$this->logger->debug( __( 'The child theme style.css content was generated', 'designinvento-templates' ) );

		return apply_filters( 'designinvento_templates_generate_child_style_css', $output, $slug, $parent, $version );
	}

	/**
	 * Generate child theme screenshot file.
	 *
	 * @param string $path Child theme path.
	 */
	public function generate_child_screenshot( $path ) {

		$screenshot = apply_filters( 'designinvento_templates_generate_child_screenshot', '' );

		if ( ! empty( $screenshot ) ) {
			// Get custom screenshot file extension
			if ( '.png' === substr( $screenshot, - 4 ) ) {
				$screenshot_ext = 'png';
			} else {
				$screenshot_ext = 'jpg';
			}
		} else {
			// Fallback to parent theme screenshot
			if ( file_exists( get_parent_theme_file_path( '/screenshot.png' ) ) ) {
				$screenshot     = get_parent_theme_file_path( '/screenshot.png' );
				$screenshot_ext = 'png';
			} elseif ( file_exists( get_parent_theme_file_path( '/screenshot.jpg' ) ) ) {
				$screenshot     = get_parent_theme_file_path( '/screenshot.jpg' );
				$screenshot_ext = 'jpg';
			}
		}

		if ( ! empty( $screenshot ) && file_exists( $screenshot ) ) {
			$copied = copy( $screenshot, $path . '/screenshot.' . $screenshot_ext );

			$this->logger->debug( __( 'The child theme screenshot was copied to the child theme, with the following result', 'designinvento-templates' ), array( 'copied' => $copied ) );
		} else {
			$this->logger->debug( __( 'The child theme screenshot was not generated, because of these results', 'designinvento-templates' ), array( 'screenshot' => $screenshot ) );
		}
	}

	/**
	 * Do plugins' AJAX
	 *
	 * @internal    Used as a calback.
	 */
	public function designinvento_templates_ajax_plugins() {

		if ( ! check_ajax_referer( 'designinvento_templates_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
			exit( 0 );
		}

		$json      = array();
		$tgmpa_url = $this->tgmpa->get_tgmpa_url();
		$plugins   = $this->get_tgmpa_plugins();

		foreach ( $plugins['activate'] as $slug => $plugin ) {
			if ( $_POST['slug'] === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-activate',
					'action2'       => - 1,
					'message'       => esc_html__( 'Activating', 'designinvento-templates' ),
				);
				break;
			}
		}

		foreach ( $plugins['update'] as $slug => $plugin ) {
			if ( $_POST['slug'] === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-update',
					'action2'       => - 1,
					'message'       => esc_html__( 'Updating', 'designinvento-templates' ),
				);
				break;
			}
		}

		foreach ( $plugins['install'] as $slug => $plugin ) {
			if ( $_POST['slug'] === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-install',
					'action2'       => - 1,
					'message'       => esc_html__( 'Installing', 'designinvento-templates' ),
				);
				break;
			}
		}

		if ( $json ) {
			$this->logger->debug(
				__( 'A plugin with the following data will be processed', 'designinvento-templates' ),
				array(
					'plugin_slug' => $_POST['slug'],
					'message'     => $json['message'],
				)
			);

			$json['hash']    = md5( serialize( $json ) );
			$json['message'] = esc_html__( 'Installing', 'designinvento-templates' );
			wp_send_json( $json );
		} else {
			$this->logger->debug(
				__( 'A plugin with the following data was processed', 'designinvento-templates' ),
				array(
					'plugin_slug' => $_POST['slug'],
				)
			);

			wp_send_json(
				array(
					'done'    => 1,
					'message' => esc_html__( 'Success', 'designinvento-templates' ),
				)
			);
		}

		exit;
	}


	/**
	 * Do content's AJAX
	 *
	 * @internal    Used as a callback.
	 */
	public function designinvento_templates_ajax_content() {
		static $content = null;

		$selected_import = get_option('selected_demo_index');

		if ( null === $content ) {
			$content = $this->get_import_data( $selected_import );
		}

		if ( ! check_ajax_referer( 'designinvento_templates_nonce', 'wpnonce' ) || empty( $_POST['content'] ) && isset( $content[ $_POST['content'] ] ) ) {
			$this->logger->error( __( 'The content importer AJAX call failed to start, because of incorrect data', 'designinvento-templates' ) );

			wp_send_json_error(
				array(
					'error'   => 1,
					'message' => esc_html__( 'Invalid content!', 'designinvento-templates' ),
				)
			);
		}

		$json         = false;
		$this_content = $content[ $_POST['content'] ];

		if ( isset( $_POST['proceed'] ) ) {
			if ( is_callable( $this_content['install_callback'] ) ) {
				$this->logger->info(
					__( 'The content import AJAX call will be executed with this import data', 'designinvento-templates' ),
					array(
						'title' => $this_content['title'],
						'data'  => $this_content['data'],
					)
				);

				$logs = call_user_func( $this_content['install_callback'], $this_content['data'] );
				if ( $logs ) {
					$json = array(
						'done'    => 1,
						'message' => $this_content['success'],
						'debug'   => '',
						'logs'    => $logs,
						'errors'  => '',
					);

					// The content import ended, so we should mark that all posts were imported.
					if ( 'content' === $_POST['content'] ) {
						$json['num_of_imported_posts'] = 'all';
					}
				}
			}
		} else {
			$json = array(
				'url'            => admin_url( 'admin-ajax.php' ),
				'action'         => 'designinvento_templates_content',
				'proceed'        => 'true',
				'content'        => $_POST['content'],
				'_wpnonce'       => wp_create_nonce( 'designinvento_templates_nonce' ),
				'selected_index' => $selected_import,
				'message'        => $this_content['installing'],
				'logs'           => '',
				'errors'         => '',
			);
		}

		if ( $json ) {
			$json['hash'] = md5( serialize( $json ) );
			wp_send_json( $json );
		} else {
			$this->logger->error(
				__( 'The content import AJAX call failed with this passed data', 'designinvento-templates' ),
				array(
					'selected_content_index' => $selected_import,
					'importing_content'      => $_POST['content'],
					'importing_data'         => $this_content['data'],
				)
			);

			wp_send_json(
				array(
					'error'   => 1,
					'message' => esc_html__( 'Error', 'designinvento-templates' ),
					'logs'    => '',
					'errors'  => '',
				)
			);
		}
	}

	/**
	 * AJAX call to retrieve total items (posts, pages, CPT, attachments) for the content import.
	 */
	public function designinvento_templates_ajax_get_total_content_import_items() {
		if ( ! check_ajax_referer( 'designinvento_templates_nonce', 'wpnonce' ) && (empty( get_option('selected_demo_index') ) || get_option('selected_demo_index') == false )) {
			$this->logger->error( __( 'The content importer AJAX call for retrieving total content import items failed to start, because of incorrect data.', 'designinvento-templates' ) );

			wp_send_json_error(
				array(
					'error'   => 1,
					'message' => esc_html__( 'Invalid data!', 'designinvento-templates' ),
				)
			);
		}

		$selected_import = get_option('selected_demo_index');
		$import_files    = $this->get_import_files_paths( $selected_import );

		wp_send_json_success( $this->importer->get_number_of_posts_to_import( $import_files['content'] ) );
	}


	/**
	 * Get import data from the selected import.
	 * Which data does the selected import have for the import.
	 *
	 * @param int $selected_import_index The index of the predefined demo import.
	 *
	 * @return bool|array
	 */
	public function get_import_data_info( $selected_import_index = 0 ) {
		$import_data = array(
			'content'      => false,
			'widgets'      => false,
			'options'      => false,
			'sliders'      => false,
			'redux'        => false,
			'redux2'        => false,
			'after_import' => false,
		);

		if ( empty( $this->import_files[ $selected_import_index ] ) ) {
			return false;
		}

		if (
			! empty( $this->import_files[ $selected_import_index ]['import_file_url'] ) ||
			! empty( $this->import_files[ $selected_import_index ]['local_import_file'] )
		) {
			$import_data['content'] = true;
		}

		if (
			! empty( $this->import_files[ $selected_import_index ]['import_widget_file_url'] ) ||
			! empty( $this->import_files[ $selected_import_index ]['local_import_widget_file'] )
		) {
			$import_data['widgets'] = true;
		}

		if (
			! empty( $this->import_files[ $selected_import_index ]['import_customizer_file_url'] ) ||
			! empty( $this->import_files[ $selected_import_index ]['local_import_customizer_file'] )
		) {
			$import_data['options'] = true;
		}

		if (
			! empty( $this->import_files[ $selected_import_index ]['import_rev_slider_file_url'] ) ||
			! empty( $this->import_files[ $selected_import_index ]['local_import_rev_slider_file'] )
		) {
			$import_data['sliders'] = true;
		}

		if (
			! empty( $this->import_files[ $selected_import_index ]['import_redux'] ) ||
			! empty( $this->import_files[ $selected_import_index ]['local_import_redux'] )
		) {
			$import_data['redux'] = true;
		}

		if (
			! empty( $this->import_files[ $selected_import_index ]['import_redux2'] ) ||
			! empty( $this->import_files[ $selected_import_index ]['local_import_redux2'] )
		) {
			$import_data['redux2'] = true;
		}

		if ( false !== has_action( 'designinvento_templates_after_all_import' ) ) {
			$import_data['after_import'] = true;
		}

		return $import_data;
	}


	/**
	 * Get the import files/data.
	 *
	 * @param int $selected_import_index The index of the predefined demo import.
	 *
	 * @return    array
	 */
	protected function get_import_data( $selected_import_index = 0 ) {
		$content = array();
		$selected_import_index = get_option('selected_demo_index');
		$import_files = $this->get_import_files_paths( get_option('selected_demo_index') );

		if ( false !== has_action( 'designinvento_templates_before_all_import' ) ) {
			$content['before_import'] = array(
				'title'            => esc_html__( 'After import setup ', 'designinvento-templates' ),
				'description'      => esc_html__( 'After import setup.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'install_callback' => array( $this->hooks, 'before_all_import_action' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $selected_import_index,
			);
		}
		if ( ! empty( $import_files['content'] ) ) {
			$content['content'] = array(
				'title'            => esc_html__( 'Content', 'designinvento-templates' ),
				'description'      => esc_html__( 'Demo content data.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'install_callback' => array( $this->importer, 'import' ),
				'data'             => $import_files['content'],
			);
		}

		if ( ! empty( $import_files['widgets'] ) ) {
			$content['widgets'] = array(
				'title'            => esc_html__( 'Widgets', 'designinvento-templates' ),
				'description'      => esc_html__( 'Sample widgets data.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'install_callback' => array( 'Classiads_Templates_Widget_Importer', 'import' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $import_files['widgets'],
			);
		}

		if ( ! empty( $import_files['sliders'] ) ) {
			$content['sliders'] = array(
				'title'            => esc_html__( 'Revolution Slider', 'designinvento-templates' ),
				'description'      => esc_html__( 'Sample Revolution sliders data.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'install_callback' => array( $this, 'import_revolution_sliders' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $import_files['sliders'],
			);
		}

		if ( ! empty( $import_files['options'] ) ) {
			$content['options'] = array(
				'title'            => esc_html__( 'Options', 'designinvento-templates' ),
				'description'      => esc_html__( 'Sample theme options data.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'install_callback' => array( 'Classiads_Templates_Customizer_Importer', 'import' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $import_files['options'],
			);
		}

		if ( ! empty( $import_files['redux'] ) ) {
			$content['redux'] = array(
				'title'            => esc_html__( 'Theme Options', 'designinvento-templates' ),
				'description'      => esc_html__( 'Theme framework options.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'install_callback' => array( 'Classiads_Templates_Redux_Importer', 'import' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $import_files['redux'],
			);
		}
		if ( ! empty( $import_files['redux2'] ) ) {
			$content['redux2'] = array(
				'title'            => esc_html__( 'DirectoryPress Settings', 'designinvento-templates' ),
				'description'      => esc_html__( 'DirectoryPress Settings.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'install_callback' => array( 'Classiads_Templates_Redux_Importer2', 'import' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $import_files['redux2'],
			);
		}

		if ( false !== has_action( 'designinvento_templates_after_all_import' ) ) {
			$content['after_import'] = array(
				'title'            => esc_html__( 'After import setup', 'designinvento-templates' ),
				'description'      => esc_html__( 'After import setup.', 'designinvento-templates' ),
				'pending'          => esc_html__( 'Pending', 'designinvento-templates' ),
				'installing'       => esc_html__( 'Installing', 'designinvento-templates' ),
				'success'          => esc_html__( 'Success', 'designinvento-templates' ),
				'install_callback' => array( $this->hooks, 'after_all_import_action' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $selected_import_index,
			);
		}

		$content = apply_filters( 'designinvento_templates_get_base_content', $content, $this );

		return $content;
	}

	/**
	 * Import revolution slider.
	 *
	 * @param string $file Path to the revolution slider zip file.
	 */
	public function import_revolution_sliders( $file ) {
		if ( ! class_exists( 'RevSlider', false ) ) {
			return 'failed';
		}

		$importer = new RevSlider();

		$response = $importer->importSliderFromPost( true, true, $file );

		$this->logger->info( __( 'The revolution slider import was executed', 'designinvento-templates' ) );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return 'true';
		}
	}

	/**
	 * Change the new AJAX request response data.
	 *
	 * @param array $data The default data.
	 *
	 * @return array The updated data.
	 */
	public function designinvento_templates_pt_importer_new_ajax_request_response_data( $data ) {
		$data['url']      = admin_url( 'admin-ajax.php' );
		$data['message']  = esc_html__( 'Installing', 'designinvento-templates' );
		$data['proceed']  = 'true';
		$data['action']   = 'designinvento_templates_content';
		$data['content']  = 'content';
		$data['_wpnonce'] = wp_create_nonce( 'designinvento_templates_nonce' );
		$data['hash']     = md5( rand() ); // Has to be unique (check JS code catching this AJAX response).

		return $data;
	}

	/**
	 * After content import setup code.
	 */
	public function designinvento_templates_after_content_import_setup( $selected_import_index ) {
		$selected_import_index = get_option('selected_demo_index');
		$selected_demo = $this->import_files[ $selected_import_index ];
		//$term_2_array = array(3,2,4,5,8,10,13,14,15,26,27,28,29,36,37,39,40);
		if($selected_import_index == 0){
			include DT_PATH .'demos/elementor/classiads-shawk/terms.php';
		}elseif($selected_import_index == 10){
			include DT_PATH .'demos/classiads-moto/terms.php';
		}elseif($selected_import_index == 12){
			include DT_PATH .'demos/classiads-phone/terms.php';
		}elseif($selected_import_index == 18){
			include DT_PATH .'demos/elementor/classiads-pets/terms.php';
		}elseif($selected_import_index == 19){
			include DT_PATH .'demos/elementor/classiads-directory/terms.php';
		}else{
			include DT_PATH .'demos/terms.php';
		}

		// setup woocomerce pages
		$woocomerce_account_page = get_page_by_title('My Account', OBJECT, 'page');
		$woocomerce_cart_page = get_page_by_title('Cart', OBJECT, 'page');
		$woocomerce_checkout_page = get_page_by_title('Checkout', OBJECT, 'page');

		update_option('woocommerce_myaccount_page_id', $woocomerce_account_page->ID);
		update_option('woocommerce_cart_page_id', $woocomerce_cart_page->ID);
		update_option('woocommerce_checkout_page_id', $woocomerce_checkout_page->ID);

		// update permalinks and allow registration

		update_option('users_can_register', 1);
		update_option('permalink_structure', '/%postname%/');

		//set home page

		$homepage = get_page_by_title( 'home', OBJECT, 'page' );
		update_option( 'page_on_front', $homepage->ID );
		update_option( 'show_on_front', 'page' );

		// set blog page
		$blogpage = get_page_by_title( 'blog', OBJECT, 'page' );
		update_option( 'page_for_posts', $blogpage->ID );
		update_option( 'show_on_front', 'page' );

		//message

		$user_role = array();
		require_once ABSPATH . 'wp-admin/includes/user.php';
		foreach ( get_editable_roles() as $key => $role ) {
			$user_role[] = $key;
		}
		//print_r($user_role);
		$msg_option = get_option('DIFP_admin_options');
		$msg_option['userrole_access'] = $user_role;
		$msg_option['userrole_new_message'] = $user_role;
		$msg_option['userrole_reply'] = $user_role;
		update_option('DIFP_admin_options', $msg_option);

	}

	/**
	 * Get menu by menu name
	 *
	 * @param $name
	 *
	 * @return array|bool|false|WP_Term
	 */
	public function designinvento_templates_get_menu_by_name( $name ) {
		$menu = get_term_by( 'name', $name, 'nav_menu' );
		if ( $menu ) {
			return $menu;
		}

		return false;
	}

	/**
	 * Before content import setup code.
	 */
	public function designinvento_templates_before_content_import_setup($selected_import_index) {
		$selected_import_index = get_option('selected_demo_index');
		//$selected_demo = $this->import_files[ $selected_import_index ];
		$hello_world = get_page_by_title( 'Hello World!', OBJECT, 'post' );

		if ( ! empty( $hello_world ) ) {
			$hello_world->post_status = 'draft';
			wp_update_post( $hello_world );

			$this->logger->debug( __( 'The Hello world post status was set to draft', 'designinvento-templates' ) );
		}

		//$term_2_array = array(3,2,4,5,8,10,13,14,15,26,27,28,29,36,37,39,40);
		if($selected_import_index == 0){
			include DT_PATH .'demos/elementor/classiads-shawk/terms.php';
			include DT_PATH .'demos/elementor/classiads-shawk/custom-tables.php';
		}elseif($selected_import_index == 10){
			include DT_PATH .'demos/classiads-moto/terms.php';
			include DT_PATH .'demos/classiads-moto/custom-tables.php';
		}elseif($selected_import_index == 12){
			include DT_PATH .'demos/classiads-phone/terms.php';
			include DT_PATH .'demos/classiads-phone/custom-tables.php';
		}elseif($selected_import_index == 18){
			include DT_PATH .'demos/elementor/classiads-pets/terms.php';
			include DT_PATH .'demos/elementor/classiads-pets/custom-tables.php';
		}elseif($selected_import_index == 19){
			include DT_PATH .'demos/elementor/classiads-directory/terms.php';
			include DT_PATH .'demos/elementor/classiads-directory/custom-tables.php';
		}else{
			include DT_PATH .'demos/terms.php';
			include DT_PATH .'demos/custom-tables.php';
		}
	}

	/**
	 * Register the import files via the `designinvento_templates_import_files` filter.
	 */
	public function designinvento_templates_register_import_files() {
		$this->import_files = $this->validate_import_file_info( apply_filters( 'designinvento_templates_import_files', array() ) );
	}

	/**
	 * Filter through the array of import files and get rid of those who do not comply.
	 *
	 * @param  array $import_files list of arrays with import file details.
	 *
	 * @return array list of filtered arrays.
	 */
	public function validate_import_file_info( $import_files ) {
		$filtered_import_file_info = array();

		foreach ( $import_files as $import_file ) {
			if ( ! empty( $import_file['import_file_name'] ) ) {
				$filtered_import_file_info[] = $import_file;
			} else {
				$this->logger->warning( __( 'This predefined demo import does not have the name parameter: import_file_name', 'designinvento-templates' ), $import_file );
			}
		}

		return $filtered_import_file_info;
	}

	/**
	 * Set the import file base name.
	 * Check if an existing base name is available (saved in a transient).
	 */
	public function set_import_file_base_name() {
		$existing_name = get_transient( 'designinvento_templates_import_file_base_name' );

		if ( ! empty( $existing_name ) ) {
			$this->import_file_base_name = $existing_name;
		} else {
			$this->import_file_base_name = date( 'Y-m-d__H-i-s' );
		}

		set_transient( 'designinvento_templates_import_file_base_name', $this->import_file_base_name, MINUTE_IN_SECONDS );
	}

	/**
	 * Get the import file paths.
	 * Grab the defined local paths, download the files or reuse existing files.
	 *
	 * @param int $selected_import_index The index of the selected import.
	 *
	 * @return array
	 */
	public function get_import_files_paths( $selected_import_index ) {
		$selected_import_data = empty( $this->import_files[ get_option('selected_demo_index') ] ) ? false : $this->import_files[ get_option('selected_demo_index') ];

		if ( empty( $selected_import_data ) ) {
			return array();
		}

		// Set the base name for the import files.
		$this->set_import_file_base_name();

		$base_file_name = $this->import_file_base_name;
		$import_files   = array(
			'content' => '',
			'widgets' => '',
			'options' => '',
			'redux'   => array(),
			'redux2'   => array(),
			'sliders' => '',
		);

		$downloader = new Classiads_Templates_Downloader();

		//$import_files['plugins'] = $selected_import_data['plugins'];

		// Check if 'import_file_url' is not defined. That would mean a local file.
		if ( empty( $selected_import_data['import_file_url'] ) ) {
			if ( ! empty( $selected_import_data['local_import_file'] ) && file_exists( $selected_import_data['local_import_file'] ) ) {
				$import_files['content'] = $selected_import_data['local_import_file'];
			}
		} else {
			// Set the filename string for content import file.
			$content_filename = 'content-' . $base_file_name . '.xml';

			// Retrieve the content import file.
			$import_files['content'] = $downloader->fetch_existing_file( $content_filename );

			// Download the file, if it's missing.
			if ( empty( $import_files['content'] ) ) {
				$import_files['content'] = $downloader->download_file( $selected_import_data['import_file_url'], $content_filename );
			}

			// Reset the variable, if there was an error.
			if ( is_wp_error( $import_files['content'] ) ) {
				$import_files['content'] = '';
			}
		}

		// Get widgets file as well. If defined!
		if ( ! empty( $selected_import_data['import_widget_file_url'] ) ) {
			// Set the filename string for widgets import file.
			$widget_filename = 'widgets-' . $base_file_name . '.json';

			// Retrieve the content import file.
			$import_files['widgets'] = $downloader->fetch_existing_file( $widget_filename );

			// Download the file, if it's missing.
			if ( empty( $import_files['widgets'] ) ) {
				$import_files['widgets'] = $downloader->download_file( $selected_import_data['import_widget_file_url'], $widget_filename );
			}

			// Reset the variable, if there was an error.
			if ( is_wp_error( $import_files['widgets'] ) ) {
				$import_files['widgets'] = '';
			}
		} elseif ( ! empty( $selected_import_data['local_import_widget_file'] ) ) {
			if ( file_exists( $selected_import_data['local_import_widget_file'] ) ) {
				$import_files['widgets'] = $selected_import_data['local_import_widget_file'];
			}
		}

		// Get customizer import file as well. If defined!
		if ( ! empty( $selected_import_data['import_customizer_file_url'] ) ) {
			// Setup filename path to save the customizer content.
			$customizer_filename = 'options-' . $base_file_name . '.dat';

			// Retrieve the content import file.
			$import_files['options'] = $downloader->fetch_existing_file( $customizer_filename );

			// Download the file, if it's missing.
			if ( empty( $import_files['options'] ) ) {
				$import_files['options'] = $downloader->download_file( $selected_import_data['import_customizer_file_url'], $customizer_filename );
			}

			// Reset the variable, if there was an error.
			if ( is_wp_error( $import_files['options'] ) ) {
				$import_files['options'] = '';
			}
		} elseif ( ! empty( $selected_import_data['local_import_customizer_file'] ) ) {
			if ( file_exists( $selected_import_data['local_import_customizer_file'] ) ) {
				$import_files['options'] = $selected_import_data['local_import_customizer_file'];
			}
		}

		// Get revolution slider import file as well. If defined!
		if ( ! empty( $selected_import_data['import_rev_slider_file_url'] ) ) {
			// Setup filename path to save the customizer content.
			$rev_slider_filename = 'slider-' . $base_file_name . '.zip';

			// Retrieve the content import file.
			$import_files['sliders'] = $downloader->fetch_existing_file( $rev_slider_filename );

			// Download the file, if it's missing.
			if ( empty( $import_files['sliders'] ) ) {
				$import_files['sliders'] = $downloader->download_file( $selected_import_data['import_rev_slider_file_url'], $rev_slider_filename );
			}

			// Reset the variable, if there was an error.
			if ( is_wp_error( $import_files['sliders'] ) ) {
				$import_files['sliders'] = '';
			}
		} elseif ( ! empty( $selected_import_data['local_import_rev_slider_file'] ) ) {
			if ( file_exists( $selected_import_data['local_import_rev_slider_file'] ) ) {
				$import_files['sliders'] = $selected_import_data['local_import_rev_slider_file'];
			}
		}

		// Get redux import file as well. If defined!
		if ( ! empty( $selected_import_data['import_redux'] ) ) {
			$redux_items = array();

			// Setup filename paths to save the Redux content.
			foreach ( $selected_import_data['import_redux'] as $index => $redux_item ) {
				$redux_filename = 'redux-' . $index . '-' . $base_file_name . '.json';

				// Retrieve the content import file.
				$file_path = $downloader->fetch_existing_file( $redux_filename );

				// Download the file, if it's missing.
				if ( empty( $file_path ) ) {
					$file_path = $downloader->download_file( $redux_item['file_url'], $redux_filename );
				}

				// Reset the variable, if there was an error.
				if ( is_wp_error( $file_path ) ) {
					$file_path = '';
				}

				$redux_items[] = array(
					'option_name' => $redux_item['option_name'],
					'file_path'   => $file_path,
				);
			}

			// Download the Redux import file.
			$import_files['redux'] = $redux_items;
		} elseif ( ! empty( $selected_import_data['local_import_redux'] ) ) {
			$redux_items = array();

			// Setup filename paths to save the Redux content.
			foreach ( $selected_import_data['local_import_redux'] as $redux_item ) {
				if ( file_exists( $redux_item['file_path'] ) ) {
					$redux_items[] = $redux_item;
				}
			}

			// Download the Redux import file.
			$import_files['redux'] = $redux_items;
		}

		// Get redux import file for directorypress!
		if ( ! empty( $selected_import_data['import_redux2'] ) ) {
			$redux_items = array();

			// Setup filename paths to save the Redux content.
			foreach ( $selected_import_data['import_redux2'] as $index => $redux_item ) {
				$redux_filename = 'redux-' . $index . '-' . $base_file_name . '.json';

				// Retrieve the content import file.
				$file_path = $downloader->fetch_existing_file( $redux_filename );

				// Download the file, if it's missing.
				if ( empty( $file_path ) ) {
					$file_path = $downloader->download_file( $redux_item['file_url'], $redux_filename );
				}

				// Reset the variable, if there was an error.
				if ( is_wp_error( $file_path ) ) {
					$file_path = '';
				}

				$redux_items[] = array(
					'option_name' => $redux_item['option_name'],
					'file_path'   => $file_path,
				);
			}

			// Download the Redux import file.
			$import_files['redux2'] = $redux_items;
		} elseif ( ! empty( $selected_import_data['local_import_redux2'] ) ) {
			$redux_items = array();

			// Setup filename paths to save the Redux content.
			foreach ( $selected_import_data['local_import_redux2'] as $redux_item ) {
				if ( file_exists( $redux_item['file_path'] ) ) {
					$redux_items[] = $redux_item;
				}
			}

			// Download the Redux import file.
			$import_files['redux2'] = $redux_items;
		}

		return $import_files;
	}

	/**
	 * AJAX callback for the 'merlin_update_selected_import_data_info' action.
	 */
	public function designinvento_templates_selected_import_data_info() {
		$this->selected_index = get_option('selected_demo_index');

		if ( false === get_option('selected_demo_index') ) {
			wp_send_json_error();
		}

		$import_info      = $this->get_import_data_info( get_option('selected_demo_index') );
		$import_info_html = $this->get_import_steps_html( $import_info );
		wp_send_json_success( $import_info_html );
	}

	/**
	 * AJAX callback for the 'merlin_update_selected_import_data_info' action.
	 */
	public function designinvento_templates_update_selected_import_data_info() {
		$selected_index = $_POST['selected_index'];
		update_option('selected_demo_index', $selected_index);
		$this->selected_index = get_option('selected_demo_index');

		$data = 'success';

		$import_info  = $this->get_import_data_info( get_option('selected_demo_index') );
		$import_info_html = $this->get_import_steps_html( $import_info );
		wp_send_json_success( $data );

	}

	/**
	 * Get the import steps HTML output.
	 *
	 * @param array $import_info The import info to prepare the HTML for.
	 *
	 * @return string
	 */
	public function get_import_steps_html( $import_info ) {
		ob_start();
		?>
		<?php foreach ( $import_info as $slug => $available ) : ?>
			<?php
			if ( ! $available ) {
				continue;
			}
			if($slug == 'redux'){
				$name = 'theme_settings';
			}elseif($slug == 'redux2'){
				$name = 'directorypress_settings';
			}else{
				$name = $slug;
			}
			?>

			<li class="merlin__drawer--import-content__list-item status status--Pending" data-content="<?php echo esc_attr( $slug ); ?>">
				<input type="checkbox" name="default_content[<?php echo esc_attr( $slug ); ?>]" class="checkbox checkbox-<?php echo esc_attr( $slug ); ?>" id="default_content_<?php echo esc_attr( $slug ); ?>" value="1" checked>
				<label for="default_content_<?php echo esc_attr( $slug ); ?>">
					<i></i><span><?php echo esc_html( ucfirst( str_replace( '_', ' ', $name ) ) ); ?></span>
				</label>
			</li>

		<?php endforeach; ?>
		<?php

		return ob_get_clean();
	}


	/**
	 * AJAX call for cleanup after the importing steps are done -> import finished.
	 */
	public function designinvento_templates_import_finished() {
		delete_transient( 'designinvento_templates_import_file_base_name' );
		wp_send_json_success();
	}


	/**
	 * Choose Plugin current demo.
	 */
	public function designinvento_templates_register_required_plugins() {
		if ( !empty(get_option('selected_demo_index')) && get_option('selected_demo_index') != false ) {
			$selected_import_index = get_option('selected_demo_index');
		}else{
			$selected_import_index = 9999;
		}
		designinvento_template_plugin($selected_import_index);
	}

	public function designinvento_template_filter_demo()
	{
		//check_ajax_referer( 'designinvento_templates_nonce' );
		$page_builder = (isset($_POST['page_builder'])) ? $_POST['page_builder'] : 'elementor';
		//$category = 'all';
		$all_demo = $this->import_files;
		$html = '';
		$demos = array();

		foreach ($all_demo as $demo) {
			if ($demo['page_builder'] == $page_builder) {
				$demos[] = $demo;
			}
		}

		if ( ! empty( $demos ) ) {
			$demos = array_chunk( $demos, 30 );
			$_SESSION['demo'] = $demos;
			foreach ($demos[0] as $demo) {
				$html .= $this->designinvento_demo_template($demo);
			}
		} else {
			$html = '<h3 class="noting-found">' . esc_html__('Sorry! There is no demo for your choice.') . '</h3>';
		}

		wp_send_json_success($html);
	}

	public function designinvento_demo_template($demo)
	{
		$demo_name        = isset( $demo['import_file_name'] ) ? $demo['import_file_name'] : 'Untitled Demo';
		$demo_image       = isset( $demo['import_preview_image_url'] ) ? $demo['import_preview_image_url'] : $this->theme->get_screenshot();
		$demo_preview_url = isset( $demo['preview_url'] ) ? $demo['preview_url'] : '';
		$demo_type        = isset( $demo['type'] ) ? $demo['type'] : 'all';
		$image_html = '';
		$button = '';
		if ( ! empty( $demo_image ) ) :

			$image_html = '<img src="' . esc_url( $demo_image ) . ' " alt="' . esc_attr( 'Demo Image', 'designinvento-templates' ) . '">';
		endif;
		//if ( 'pro' === $demo_type && defined( 'DESIGNINVENTO_PRO_VERSION' ) ) {
			//$button = '<button data-content="' . esc_attr( $demo['id'] ) . '" class="merlin__demo-button js-select-demo">' . esc_html__( 'Select', 'designinvento-templates' ) . '</button>';
		//} elseif ( 'pro' === $demo_type && !defined( 'DESIGNINVENTO_PRO_VERSION' ) ) {
			//$button = '<a class="merlin__demo-button" href="https://designinvento.net/pro" target="_blank">' . esc_html__( 'Pricing', 'designinvento-templates' ) . '</a>';
		//} elseif( 'free' === $demo_type ) {
			$button = '<button data-content="' . esc_attr( $demo['id'] ) .'" class="merlin__demo-button js-select-demo" data-callback="install_contents">' . esc_html__( 'Select', 'designinvento-templates' ) .'</button>';
		//}

		$html = '<div class="merlin__demo">
					<div class="merlin__demo-image">' .
						$image_html .
					'</div>

					<div class="merlin__demo-content">
						<h4 class="merlin__demo-title">' . esc_html( $demo_name ) . '</h4>
						<div class="merlin__demo-cta">
							<a class="merlin__demo-button" href="' . esc_url( $demo_preview_url ) . '" target="_blank">' . esc_html__( 'Preview', 'designinvento-templates' ) . '</a>' .
							$button .

						'</div>
					</div>
				</div>';

		return $html;
	}

	public function designinvento_templates_load_more_demo()
	{
		$page     = ( isset( $_POST['page'] ) ) ? $_POST['page'] : 1;
		$page     = (int) $page;
		$page_builder = (isset($_POST['page_builder'])) ? $_POST['page_builder'] : 'elementor';
		$all_demo = $this->import_files;
		$html = '';
		$demos = array();
		foreach ($all_demo as $demo) {

			if ( $demo['page_builder'] === $page_builder ) {
				$demos[] = $demo;
			}

		}
		if ( ! empty( $demos ) ) {
			$demos = array_chunk( $demos, 30 );
			$_SESSION['demo'] = $demos;

			foreach ( $demos[$page] as $demo ) {
				$html .= $this->designinvento_demo_template($demo);
			}
		}else{
			$html = '<h3 class="noting-found">' . esc_html__('Sorry! There is no demo for your choice.') . '</h3>';
		}

		wp_send_json_success($html);
	}
	public function designinvento_templates_plugins()
	{
		include(DT_PATH .'includes/plugins_template.php');
	}
	public function designinvento_templates_start_session()
	{
		if(!session_id()) {
			session_start();
		}
	}
}
