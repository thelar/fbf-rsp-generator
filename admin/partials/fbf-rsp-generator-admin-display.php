<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.chapteragency.com
 * @since      1.0.0
 *
 * @package    Fbf_Rsp_Generator
 * @subpackage Fbf_Rsp_Generator/admin/partials
 */
?>

<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <form method="post" action="options.php">
        <?php
        settings_errors();
        settings_fields( $this->plugin_name );
        do_settings_sections( $this->plugin_name );
        submit_button();
        ?>
    </form>

    <hr/>

    <h2>Add new rule</h2>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-bottom: 25px;">
        <input type="hidden" name="action" value="fbf_rsp_generator_add_rule">
        <table class="form-table" id="fbf-rsp-generator-add-rule-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="fbf_rsp_generator_rule_name">Rule name</label>
                    </th>
                    <td>
                        <input type="text" name="fbf_rsp_generator_rule_name" id="fbf_rsp_generator_rule_name" value="">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="fbf_rsp_generator_price_match">Price match</label>
                    </th>
                    <td>
                        <div>
                            <input type="checkbox" value="yes" id="fbf_rsp_generator_price_match" name="fbf_rsp_generator_price_match"/>
                            <label for="fbf_rsp_generator_price_match">Tick to price match against lowest competitor (Competitor Monitor)</label>
                        </div>
                        <div style="margin-top: 0.5em; display: none;" id="fbf_rsp_generator_price_match_addition_row">
                            <input type="text" id="fbf_rsp_generator_price_match_addition" name="fbf_rsp_generator_price_match_addition" style="width: 100px;"/>
                            <label for="fbf_rsp_generator_price_match_addition">Enter a fixed amount to <strong>ADD</strong> onto Price match</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="fbf_rsp_generator_rule_amount">Amount (%)</label>
                    </th>
                    <td>
                        <input type="text" name="fbf_rsp_generator_rule_amount" id="fbf_rsp_generator_rule_amount" value=""><br/>
                        <p><strong>OR</strong> (note - only enter a % amount OR a £ amount)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="fbf_rsp_generator_rule_amount_pound">Amount (£)</label>
                    </th>
                    <td>
                        <input type="text" name="fbf_rsp_generator_rule_amount_pound" id="fbf_rsp_generator_rule_amount_pound" value="">
                    </td>
                </tr>
                <?php echo $this->display_selects($this->taxonomies); ?>
            </tbody>
        </table>
        <input type="submit" value="Add rule" class="button-primary">
    </form>

    <hr/>

    <h2>Current rules</h2>

    <table class="widefat" id="fbf-rsp-generator-rule-table" style="margin-bottom: 25px;">
        <thead>
            <tr>
                <th class="row-title"><?php esc_attr_e( 'Rule name', $this->plugin_name ); ?></th>
                <?php echo $this->print_taxonomy_headings(); ?>
                <th style="text-align: center;"><?php esc_attr_e( 'Price Match', $this->plugin_name ); ?></th>
                <th style="text-align: center;"><?php esc_attr_e( 'Amount (&pound; or %)', $this->plugin_name ); ?></th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $this->print_rule_rows(); ?>
        </tbody>
    </table>

</div>
