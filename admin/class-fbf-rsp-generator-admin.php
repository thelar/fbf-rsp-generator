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
#[\AllowDynamicProperties]
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
     * The taxonomies for which we allow rules to be created
     *
     * @since 1.0.0
     * @access private
     * @var array
     */
    private $taxonomies = ['pa_tyre-type', 'pa_tyre-size', 'pa_tyre-profile', 'pa_tyre-width', 'pa_brand-name', 'pa_model-name'];

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
        add_settings_field(
            $this->option_name . '_flat_fee',
            __( 'Flat fee (£)', 'fbf-rsp-generator' ),
            [$this, $this->option_name . '_flat_fee_cb'],
            $this->plugin_name,
            $this->option_name . '_general',
            ['label_for' => $this->option_name . '_flat_fee']
        );
        add_settings_field(
            $this->option_name . '_fitting_cost',
            __( 'Fitting cost (£)', 'fbf-rsp-generator' ),
            [$this, $this->option_name . '_fitting_cost_cb'],
            $this->plugin_name,
            $this->option_name . '_general',
            ['label_for' => $this->option_name . '_fitting_cost']
        );
        register_setting( $this->plugin_name, $this->option_name . '_min_stock', [$this, 'fbf_rsp_generator_validate_min_stock'] );
        register_setting( $this->plugin_name, $this->option_name . '_flat_fee', [$this, 'fbf_rsp_generator_validate_flat_fee'] );
        register_setting( $this->plugin_name, $this->option_name . '_fitting_cost', [$this, 'fbf_rsp_generator_validate_fitting_cost'] );
    }

    public function fbf_rsp_generator_validate_min_stock($input)
    {
        $option = get_option($this->option_name . '_min_stock');
        $validated = sanitize_text_field($input);
        if($validated !== $input){
            $type = 'error';
            $message = __('Min Stock was not valid', 'fbf-rsp-generator');
            $validated = $option;
        }else{
            $validated = intval($validated);
            if(!$validated){
                $type = 'error';
                $message = __('Min Stock was not a number', 'fbf-rsp-generator');
                $validated = $option;
            }else{
                $type = 'updated';
                $message = __('Min Stock updated', 'fbf-rsp-generator');
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

    public function fbf_rsp_generator_validate_flat_fee($input)
    {
        $option = get_option($this->option_name . '_flat_fee');
        $validated = sanitize_text_field($input);
        if($validated !== $input){
            $type = 'error';
            $message = __('Flat Fee was not valid', 'fbf-rsp-generator');
            $validated = $option;
        }else{
            $validated = floatval($validated);
            if(!$validated){
                $type = 'error';
                $message = __('Flat Fee was not a number', 'fbf-rsp-generator');
                $validated = $option;
            }else{
                $type = 'updated';
                $message = __('Flat Fee updated', 'fbf-rsp-generator');
            }
        }
        add_settings_error(
            $this->option_name . '_flat_fee',
            esc_attr('settings_updated'),
            $message,
            $type
        );
        return number_format($validated, 2);
    }

    public function fbf_rsp_generator_validate_fitting_cost($input)
    {
        $option = get_option($this->option_name . '_fitting_cost');
        $validated = sanitize_text_field($input);
        if($validated !== $input){
            $type = 'error';
            $message = __('Fitting cost was not valid', 'fbf-rsp-generator');
            $validated = $option;
        }else{
            $validated = floatval($validated);
            if(!$validated){
                $type = 'error';
                $message = __('Fitting cost was not a number', 'fbf-rsp-generator');
                $validated = $option;
            }else{
                $type = 'updated';
                $message = __('Fitting cost updated', 'fbf-rsp-generator');
            }
        }
        add_settings_error(
            $this->option_name . '_fitting_cost',
            esc_attr('settings_updated'),
            $message,
            $type
        );
        return number_format($validated, 2);
    }

    /**
     * Render the text for the general section
     *
     * @since  1.0.0
     */
    public function fbf_rsp_generator_general_cb() {
        echo '<p>' . __( 'Please make changes to the RSP Rules settings below. (Settings apply to Tyres only)', 'fbf-rsp-generator' ) . '</p>';
    }

    /**
     * Render the min stock input for this plugin
     *
     * @since  1.0.0
     */
    public function fbf_rsp_generator_min_stock_cb() {
        $min_stock = get_option( $this->option_name . '_min_stock' );
        echo '<input type="text" name="' . $this->option_name . '_min_stock' . '" id="' . $this->option_name . '_file' . '" value="' . $min_stock . '"> ';
    }

    /**
     * Render the flat fee input for this plugin
     *
     * @since  1.0.0
     */
    public function fbf_rsp_generator_flat_fee_cb() {
        $flat_fee = get_option( $this->option_name . '_flat_fee' );
        echo '<input type="text" name="' . $this->option_name . '_flat_fee' . '" id="' . $this->option_name . '_file' . '" value="' . $flat_fee . '"> ';
    }

    /**
     * Render the flat fee input for this plugin
     *
     * @since  1.0.0
     */
    public function fbf_rsp_generator_fitting_cost_cb() {
        $fitting_cost = get_option( $this->option_name . '_fitting_cost' );
        echo '<input type="text" name="' . $this->option_name . '_fitting_cost' . '" id="' . $this->option_name . '_file' . '" value="' . $fitting_cost . '"> ';
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
        $is_rule_amount_valid = $this->is_rule_amount_valid($_REQUEST[$this->option_name . '_rule_amount'], $_REQUEST[$this->option_name . '_rule_amount_pound'], $_REQUEST[$this->option_name . '_price_match'], $_REQUEST[$this->option_name . '_price_match_addition']);
        foreach($this->taxonomies as $tax){
            $val = $_REQUEST[$this->option_name . '_rule_tax_' . $tax];
            if(isset($val) && !empty($val)){
                $rules[] = [
                    'taxonomy' => $tax,
                    'term' => $_REQUEST[$this->option_name . '_rule_tax_' . $tax]
                ];
            }
        }
        if($is_rule_name_valid->is_valid && $is_rule_amount_valid->is_valid){
            //Here if valid
            $status = 'success';
            $message = urlencode('<strong>Rule added</strong>');
        }else{
            $status = 'error';
            $message = sprintf('%s%s%s', !$is_rule_name_valid->is_valid?$is_rule_name_valid->message:'', !$is_rule_name_valid->is_valid&&!$is_rule_amount_valid->is_valid?urlencode('<br>'):'', !$is_rule_amount_valid->is_valid?$is_rule_amount_valid->message:'');
        }

        if($status=='success'){
            $table_name = $wpdb->prefix . 'fbf_rsp_rules';
            $items_table_name = $wpdb->prefix . 'fbf_rsp_rule_items';
            $sql = $wpdb->get_row("SELECT MAX(sort_order) AS so FROM $table_name");
            if($sql!==false){
                $so = $sql->so;

                $insert_array = [
                    'name' => $is_rule_name_valid->value,
                    'created' => current_time('mysql', 1),
                    'sort_order' => $so + 1
                ];
                if($is_rule_amount_valid->value==='yes'){
                    // It's a price match
                    $insert_array['amount'] = null;
                    $insert_array['price_match'] = 1;
                    if($_REQUEST[$this->option_name . '_price_match_addition']){
                        $insert_array['price_match_addition'] = sanitize_text_field($_REQUEST[$this->option_name . '_price_match_addition']);
                    }
                }else{
                    if($_REQUEST[$this->option_name . '_rule_amount']){
                        $insert_array['amount'] = sanitize_text_field($_REQUEST[$this->option_name . '_rule_amount']);
                        $insert_array['is_pc'] = 1;
                    }else if($_REQUEST[$this->option_name . '_rule_amount_pound']){
                        $insert_array['amount'] = sanitize_text_field($_REQUEST[$this->option_name . '_rule_amount_pound']);
                        $insert_array['is_pc'] = 0;
                    }
                    /*$insert_array['amount'] = $is_rule_amount_valid->value;
                    $insert_array['price_match'] = 0;*/
                }

                $insert = $wpdb->insert(
                    $table_name,
                    $insert_array
                );
                if($insert===false){
                    $status = 'error';
                    $message = urlencode('<strong>Database errors</strong> - rule could not be inserted');
                }else{
                    $insert_id = $wpdb->insert_id;
                    if(isset($rules) && !empty($rules)){
                        foreach($rules as $rule){
                            $insert_item_id = $wpdb->insert(
                                $items_table_name,
                                [
                                    'rule_id' => $insert_id,
                                    'taxonomy' => $rule['taxonomy'],
                                    'term' => $rule['term']
                                ]
                            );
                            if($insert_item_id===false){
                                $status = 'error';
                                $message = urlencode('<strong>Database errors</strong> - rule item could not be inserted');
                                break;
                            }
                        }
                    }
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

    private function is_rule_amount_valid($rule_amount_value, $rule_amount_value_pound, $price_match, $price_match_fa)
    {
        if($price_match==='yes'){ // Here if Price matched is ticked - we aren't worried about any values for PC or Amount only fixed amount to add to Price match
            $validated_price_match_fa = sanitize_text_field($price_match_fa);
            if(empty($price_match_fa)){
                return (object)[
                    'is_valid' => true,
                    'message' => urlencode('<strong>Rule added</strong> - here is where we will add the rule'),
                    'value' => $price_match
                ];
            }else if($validated_price_match_fa===$price_match_fa && is_numeric($price_match_fa) && $price_match_fa > 0 && $price_match_fa < 100){
                return (object)[
                    'is_valid' => true,
                    'message' => urlencode('<strong>Rule added</strong> - here is where we will add the rule'),
                    'value' => $price_match
                ];
            }else{
                return (object)[
                    'is_valid' => false,
                    'message' => urlencode('<strong>Validation failed</strong> - please enter a valid fixed amount to add to price match')
                ];
            }

        }

        $validated_pc = sanitize_text_field($rule_amount_value);
        $validated_pound = sanitize_text_field($rule_amount_value_pound);
        if(!empty($rule_amount_value)){
            if($validated_pc===$rule_amount_value && is_numeric($validated_pc) && $validated_pc > 0 && $validated_pc < 100){
                return (object)[
                    'is_valid' => true,
                    'message' => urlencode('<strong>Rule added</strong> - here is where we will add the rule'),
                    'value' => $validated_pc
                ];
            }else{
                return (object)[
                    'is_valid' => false,
                    'message' => urlencode('<strong>Validation failed</strong> - please enter a valid percentage amount')
                ];
            }
        }else{
            if(!empty($validated_pound)){
                if($validated_pound===$rule_amount_value_pound && is_numeric($validated_pound) && $validated_pound > 0){
                    return (object)[
                        'is_valid' => true,
                        'message' => urlencode('<strong>Rule added</strong> - here is where we will add the rule'),
                        'value' => $validated_pound
                    ];
                }else{
                    return (object)[
                        'is_valid' => false,
                        'message' => urlencode('<strong>Validation failed</strong> - please enter a valid amount')
                    ];
                }
            }else{
                return (object)[
                    'is_valid' => false,
                    'message' => urlencode('<strong>Validation failed</strong> - please enter a valid percentage or amount')
                ];
            }
        }
        /*if($validated_pc!==$rule_amount_value || !is_numeric($validated_pc) || $validated_pc <= 0 || $validated_pc >= 100 ){
            if($price_match==='yes'){
                return (object)[
                    'is_valid' => true,
                    'message' => urlencode('<strong>Rule added</strong> - here is where we will add the rule'),
                    'value' => $price_match
                ];
            }else{
                return (object)[
                    'is_valid' => false,
                    'message' => urlencode('<strong>Validation failed</strong> - please enter a valid rule amount')
                ];
            }
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
                    'value' => $validated_pc
                ];
            }
        }*/
    }

    /**
     * Print rule rows
     */
    public function print_rule_rows()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fbf_rsp_rules';
        $item_table_name = $wpdb->prefix . 'fbf_rsp_rule_items';
        $sql = "SELECT * FROM $table_name ORDER BY sort_order";
        $rows = $wpdb->get_results($sql);
        $html = "";
        if($rows){
            foreach($rows as $row){
                //Get items here
                $item_sql = "SELECT * from $item_table_name WHERE rule_id = '$row->id'";
                $items = $wpdb->get_results($item_sql);
                $items_a = [];
                if($items!==false && !empty($items)){
                    foreach($items as $item){
                        $items_a[$item->taxonomy] = $item->term;
                    }
                }


                $html.= sprintf('<tr data-id="%s">', $row->id);
                $html.= sprintf('<td class="row-title">%s</td>', esc_attr($row->name));
                foreach($this->taxonomies as $taxonomy){
                    if(isset($items_a[$taxonomy])){
                        $term = get_term_by('slug', $items_a[$taxonomy], $taxonomy);
                        $html.= sprintf('<td style="text-align: center;">%s</td>', $term->name);
                    }else{
                        $html.= '<td style="text-align: center;">Any</td>';
                    }
                }
                $price_match_addition = '';
                if($row->price_match){
                    if($row->price_match_addition){
                        $price_match_addition = ' (+ &pound;' . $row->price_match_addition . ')';
                    }
                }
                if($row->amount){
                    if($row->is_pc){
                        $amt = esc_attr($row->amount) . '%';
                    }else{
                        $amt = '&pound;' . esc_attr($row->amount);
                    }
                }else{
                    $amt = '-';
                }
                $html.= sprintf('<td style="text-align: center;">%s</td>', $row->price_match?'Yes'.$price_match_addition:'-');
                $html.= sprintf('<td style="text-align: center;">%s</td>', $amt);
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

    /**
     * Display select dropdowns
     */
    private function display_selects(Array $attributes)
    {
        $html = "";
        foreach($attributes as $attribute){
            $taxonomy = get_taxonomy($attribute);
            $html.= sprintf('<tr><th scope="row"><label for="%1$s_rule_tax_%2$s">%3$s</label></th><td><select name="%1$s_rule_tax_%2$s" id="%1$s_rule_tax_%2$s" class="%1$s_tax_rule_select">', $this->option_name, $attribute, $taxonomy->label);
            $terms = get_terms([
                'taxonomy' => $attribute,
                'hide_empty' => false,
            ]);
            $html.= '<option value="">Any</option>';
            foreach($terms as $term){
                $html.= sprintf('<option value="%1$s">%2$s</option>', $term->slug, $term->name);
            }
            $html.= '</select></td></tr>';
        }
        return $html;
    }

    private function print_taxonomy_headings()
    {
        $html = '';
        foreach($this->taxonomies as $taxonomy){
            $taxonomy = get_taxonomy($taxonomy);
            $html.= sprintf('<th style="text-align: center;">%s</th>', $taxonomy->label);
        }
        return $html;
    }

    public static function fbf_rsp_generator_generate_rules()
    {
        //Construct the rules array first:
        $rules = [];
        global $wpdb;
        $rules_table = $wpdb->prefix . 'fbf_rsp_rules';
        $rule_items_table = $wpdb->prefix . 'fbf_rsp_rule_items';

        $sql = "SELECT name, price_match, price_match_addition, is_pc, amount, taxonomy, term FROM $rules_table as rules LEFT JOIN $rule_items_table AS items ON rules.id = items.rule_id ORDER BY sort_order ASC";
        //$sql = "SELECT * FROM $rules_table";
        $rules_select = $wpdb->get_results($sql);
        if(!empty($rules_select)){
            foreach ($rules_select as $row) {
                $rules[$row->name]['amount'] = $row->amount;
                $rules[$row->name]['is_pc'] = $row->is_pc;
                $rules[$row->name]['price_match'] = $row->price_match;
                if($row->price_match_addition){
                    $rules[$row->name]['price_match_addition'] = $row->price_match_addition;
                }
                if(isset($row->term)){
                    $rules[$row->name]['rules'][$row->taxonomy] = $row->term;
                }else{
                    $rules[$row->name]['rules'] = null;
                }
            }
        }
        return $rules;
    }
}
