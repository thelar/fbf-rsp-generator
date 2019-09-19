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
        wp_enqueue_script( 'jquery-ui-sortable' );

        $ajax_params = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce($this->option_name),
        );

        wp_localize_script( $this->plugin_name, 'ajax_object', $ajax_params);

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
        global $wpdb;
        $is_rule_name_valid = $this->is_rule_name_valid($_REQUEST[$this->option_name . '_rule_name']);
        $is_rule_amount_valid = $this->is_rule_amount_valid($_REQUEST[$this->option_name . '_rule_amount']);
        if($is_rule_name_valid->is_valid && $is_rule_amount_valid->is_valid){
            //Here if valid
            $status = 'success';
            $message = urlencode('<strong>Rule added</strong> - at least it will be when code is written');
        }else{
            $status = 'error';
            $message = sprintf('%s%s%s', !$is_rule_name_valid->is_valid?$is_rule_name_valid->message:'', !$is_rule_name_valid->is_valid&&!$is_rule_amount_valid->is_valid?urlencode('<br>'):'', !$is_rule_amount_valid->is_valid?$is_rule_amount_valid->message:'');
        }

        if($status=='success'){
            $table_name = $wpdb->prefix . 'fbf_rsp_rules';
            $sql = $wpdb->get_row("SELECT MAX(sort_order) AS so FROM $table_name");
            if($sql!==false){
                $so = $sql->so;

                $insert_id = $wpdb->insert(
                    $table_name,
                    [
                        'name' => $is_rule_name_valid->value,
                        'amount' => $is_rule_amount_valid->value,
                        'created' => current_time('mysql', 1),
                        'sort_order' => $so + 1
                    ]
                );
                if($insert_id===false){
                    $status = 'error';
                    $message = urlencode('<strong>Database errors</strong> - rule could not be inserted');
                }
            }else{
                $status = 'error';
                $message = urlencode('<strong>Database errors</strong> - sort order not retrieved');
            }

        }

        wp_redirect(get_admin_url() . 'admin.php?page=' . $this->plugin_name . '&fbf_rsp_status=' . $status . '&fbf_rsp_message=' . $message);
    }

    /**
     * Delete rule
     */
    public function fbf_rsp_generator_delete_rule()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fbf_rsp_rules';
        $rule_id = $_REQUEST[$this->option_name . '_rule_id'];
        $delete = $wpdb->delete(
            $table_name,
            [
                'id' => $rule_id
            ]
        );
        if($delete!==false){
            $status = 'success';
            $message = urlencode('<strong>Rule deleted</strong>');
        }else{
            $status = 'error';
            $message = urlencode('<strong>Could not delete rule</strong>');
        }
        wp_redirect(get_admin_url() . 'admin.php?page=' . $this->plugin_name . '&fbf_rsp_status=' . $status . '&fbf_rsp_message=' . $message);
    }

    private function is_rule_name_valid($rule_name_value)
    {
        $validated_rule_name = sanitize_text_field($rule_name_value);
        if($validated_rule_name!==$rule_name_value){
            return (object)[
                'is_valid' => false,
                'message' => urlencode('<strong>Validation failed</strong> - please enter a valid rule name')
            ];
        }else{
            if(empty($rule_name_value)){
                return (object)[
                    'is_valid' => false,
                    'message' => urlencode('<strong>Name required</strong> - please enter a value for rule name')
                ];
            }else{
                return (object)[
                    'is_valid' => true,
                    'message' => urlencode('<strong>Rule added</strong> - here is where we will add the rule'),
                    'value' => $validated_rule_name
                ];
            }
        }
    }

    private function is_rule_amount_valid($rule_amount_value)
    {
        $validated = sanitize_text_field($rule_amount_value);
        if($validated!==$rule_amount_value || !is_numeric($validated) || $validated <= 0 || $validated >= 100 ){
            return (object)[
                'is_valid' => false,
                'message' => urlencode('<strong>Validation failed</strong> - please enter a valid rule amount')
            ];
        }else{
            if(empty($rule_amount_value)){
                return (object)[
                    'is_valid' => false,
                    'message' => urlencode('<strong>Validation failed</strong> - please enter a value for rule amount')
                ];
            }else{
                return (object)[
                    'is_valid' => true,
                    'message' => urlencode('<strong>Rule added</strong> - here is where we will add the rule'),
                    'value' => $validated
                ];
            }
        }
    }

    /**
     * Print rule rows
     */
    public function print_rule_rows()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fbf_rsp_rules';
        $sql = "SELECT * FROM $table_name ORDER BY sort_order";
        $rows = $wpdb->get_results($sql);
        $html = "";
        if($rows){
            foreach($rows as $row){
                $html.= sprintf('<tr data-id="%s">', $row->id);
                $html.= sprintf('<td class="row-title">%s</td>', esc_attr($row->name));
                $html.= sprintf('<td>%s</td>', esc_attr($row->amount));
                $html.= sprintf('<td><form action="%s" method="post" class="fbf-rsp-generator-delete-rule-form"><input type="hidden" name="action" value="fbf_rsp_generator_delete_rule"/><input type="hidden" name="%s" value="%s"/><button type="submit" class="no-styles fbf-rsp-generator-delete-rule">Delete</button></form></td>', admin_url('admin-post.php'), $this->option_name . '_rule_id', $row->id);
                $html.= '</tr>';
            }
        }
        return $html;

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

    /**
     * Ajax function for sorting rows
     */
    public function sort_rule_rows()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fbf_rsp_rules';
        $sorted = $_POST['sorted'];
        $resp = [];
        for($i=0;$i<count($sorted);$i++){
            $update = $wpdb->update(
                $table_name,
                [
                    'sort_order' => $i
                ],
                [
                    'id' => $sorted[$i]
                ]
            );
            $resp[$sorted[$i]] = $update;
        }
        echo json_encode($resp);
        die();
    }
}
