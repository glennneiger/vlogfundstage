<?php

namespace OTGS\Toolset\CRED\Controller\Forms\Shared\PageExtension;

/**
 * Form save metabox extension.
 * 
 * @since 2.1
 * @todo Review this HTML layout, FGS
 * @todo Review this $delete_link, FGS
 */
class Save {

    /**
     * Generate the Sve metabox.
     *
     * @param object $form
     * @param array $callback_args
     * 
     * @since 2.1
     */
    public function print_metabox_content( $form, $callback_args = array() ) {
        $delete_link = do_shortcode("[cred_delete_post_link class='submitdelete deletion' text='Move to Trash' action='delete' message='" . __("Are you sure you want to delete this form?", "wp-cred") . "' message_show='1']");
        ?>
        <div class="submitbox" id="submitpost">
            <div id="major-publishing-actions">
                <div id="delete-action">
                    <?php echo $delete_link; ?>
                </div>
                
                <div id="publishing-action">
                <span class="spinner"></span>
                    <input name="save" type="submit" class="button button-primary button-large" value="<?php (get_current_screen()->id == "cred-form" ? esc_attr_e("Save Post Form", 'wp-cred') : esc_attr_e("Save User Form", 'wp-cred')); ?>">
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php
   }
}