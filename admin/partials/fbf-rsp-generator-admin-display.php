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

    <h2>Add rules:</h2>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
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
                        <label for="fbf_rsp_generator_rule_amount">Amount (%)</label>
                    </th>
                    <td>
                        <input type="text" name="fbf_rsp_generator_rule_amount" id="fbf_rsp_generator_rule_amount" value="">
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" value="Add rule" class="button-primary">
    </form>

</div>
