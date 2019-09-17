<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.chapteragency.com
 * @since      1.0.0
 *
 * @package    Fbf_Rsp_Generator
 * @subpackage Fbf_Rsp_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fbf_Rsp_Generator
 * @subpackage Fbf_Rsp_Generator/admin
 * @author     Kevin Price-Ward <kevin.price-ward@chapteragency.com>
 */
class Fbf_Rsp_Generator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    private $option_name = 'fbf_rsp_generator';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fbf_Rsp_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fbf_Rsp_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fbf-rsp-generator-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fbf_Rsp_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fbf_Rsp_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fbf-rsp-generator-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
     * Register menu page
     *
     * @since 1.0.0
     */
    public function add_menu_page()
    {
        $this->plugin_screen_hook_sufix = add_menu_page(
            __( 'RSP Rules Generator', 'fbf-rsp-generator' ),
            __( 'RSP Generator', 'fbf-rsp-generator' ),
            'manage_options',
            $this->plugin_name,
            [$this, 'display_options_page'],
            'dashicons-admin-tools'
        );
	}

    /**
     * Register settings page
     */
    public function register_settings()
    {
        // Add a General section
        add_settings_section(
            $this->option_name . '_general',
            __( 'General', 'fbf-rsp-generator' ),
            [$this, $this->option_name . '_general_cb'],
            $this->plugin_name
        );
        add_settings_field(
            $this->option_name . '_min_stock',
            __( 'Minimum Stock', 'fbf-rsp-generator' ),
            [$this, $this->option_name . '_min_stock_cb'],
            $this->plugin_name,
            $this->option_name . '_general',
            ['label_for' => $this->option_name . '_min_stock']
        );
        register_setting( $this->plugin_name, $this->option_name . '_min_stock', [$this, 'fbf_rsp_generator_validate_min_stock'] );
    }

    public function fbf_rsp_generator_validate_min_stock($input)
    {
        $option = get_option($this->option_name . '_min_stock');
        $validated = sanitize_text_field($input);
        if($validated !== $input){
            $type = 'error';
            $message = __('Input was not valid', 'fbf-rsp-generator');
            $validated = $option;
        }else{
            $validated = intval($validated);
            if(!$validated){
                $type = 'error';
                $message = __('Input was not a number', 'fbf-rsp-generator');
                $validated = $option;
            }else{
                $type = 'updated';
                $message = __('Settings updated', 'fbf-rsp-generator');
            }
        }
        add_settings_error(
            $this->option_name . '_min_stock',
            esc_attr('settings_updated'),
            $message,
            $type
        );
        return $validated;
    }

    /**
     * Render the text for the general section
     *
     * @since  1.0.0
     */
    public function fbf_rsp_generator_general_cb() {
        echo '<p>' . __( 'Please change the settings accordingly.', 'fbf-rsp-generator' ) . '</p>';
    }

    /**
     * Render the file name input for this plugin
     *
     * @since  1.0.9
     */
    public function fbf_rsp_generator_min_stock_cb() {
        $min_stock = get_option( $this->option_name . '_min_stock' );
        echo '<input type="text" name="' . $this->option_name . '_min_stock' . '" id="' . $this->option_name . '_file' . '" value="' . $min_stock . '"> ';
    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_options_page() {
        include_once 'partials/fbf-rsp-generator-admin-display.php';
    }

    /**
     * Add a rule
     */
    public function fbf_rsp_generator_add_rule()
    {
        wp_redirect(get_admin_url() . 'admin.php?page=' . $this->plugin_name . '&fbf_rsp_status=error&fbf_rsp_message=lskjfsdkf');
    }

    /**
     * Admin notices
     */
    public function fbf_rsp_generator_admin_notices()
    {
        if(isset($_REQUEST['fbf_rsp_status'])) {
            printf('<div class="notice notice-%s is-dismissible">', $_REQUEST['fbf_rsp_status']);
            printf('<p>%s</p>', $_REQUEST['fbf_rsp_message']);
            echo '</div>';
        }
    }
}
