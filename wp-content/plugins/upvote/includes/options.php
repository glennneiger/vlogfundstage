<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Plugin Options
 *
 * Handles to plugin options
 *
 * @since Upvote 1.0
 **/
?>
<div class="wrap">
	<h1><?php _e('Upvote Options','upvote');?></h1>
    <?php settings_errors(); //Setting Updated Message ?>
    <form method="post" action="options.php">
    	<?php settings_fields( 'upvote_plugin_options' );
			$upvote_options = get_option('upvote_options'); ?>
            <!-- beginning of the general settings meta box -->
            <div id="upvote-general" class="post-box-container">
                <div class="metabox-holder">	
                    <div class="meta-box-sortables ui-sortable">
                        <div id="general" class="postbox">	
                            <div class="handlediv" title="<?php _e( 'Click to toggle', 'upvote' ); ?>"><br /></div>
            
                            <!-- general settings box title -->
                            <h3 class="hndle">
                                <span style='vertical-align: top;'><?php _e( 'General Options', 'upvote' ); ?></span>
                            </h3>
        
                            <div class="inside">
                                <table class="form-table">
                                    <tr valign="top">
                                        <th><?php _e('Guest Vote','upvote');?></th>
                                        <td><input type="checkbox" name="upvote_options[enable_guest]" id="upvote_options_enable_guest" value="1" <?php checked(1, $upvote_options['enable_guest']);?>/><label for="upvote_options_enable_guest"><?php _e('Enable');?></label><br />
                                            <p class="description"><?php _e('Enable guest voting based on IP address.','upvote');?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Email Optin','upvote');?></th>
                                        <td><input type="checkbox" name="upvote_options[enable_email_optin]" id="upvote_options_enable_email_optin" value="1" <?php checked(1, $upvote_options['enable_email_optin']);?>/><label for="upvote_options_enable_email_optin"><?php _e('Enable');?></label><br />
                                            <p class="description"><?php _e('Enable email optin, if users is guest user then it will ask for email subscription.','upvote');?><br /><?php _e('<strong>Note:</strong> Only work if guest vote enabled.','upvote');?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Email Popup Triggered','upvote');?></th>
                                        <td><input type="number" class="regular-text" name="upvote_options[email_popup_trigger]" id="upvote_options_email_popup_trigger" value="<?php echo esc_attr( $upvote_options['email_popup_trigger'] );?>"/>
                                            <p class="description"><?php _e('Enter number of votes after email popup should be triggered.','upvote');?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Allow users to continue to vote','upvote');?></th>
                                        <td><input type="checkbox" name="upvote_options[enable_voting_after_subscribe]" id="upvote_options_enable_voting_after_subscribe" value="1" <?php checked(1, $upvote_options['enable_voting_after_subscribe']);?>/><label for="upvote_options_enable_voting_after_subscribe"><?php _e('Enable');?></label><br />
                                            <p class="description"><?php _e('Allow users to continue to vote after the pop up has been triggered/users subscribed.','upvote');?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Email Optin Title','upvote');?></th>
                                        <td><input type="text" class="regular-text" name="upvote_options[email_optin_title]" id="upvote_options_email_optin_title" value="<?php echo esc_attr( $upvote_options['email_optin_title'] );?>"/>
                                            <p class="description"><?php _e('Email optin modal popup title.','upvote');?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Name Field Label','upvote');?></th>
                                        <td><input type="text" class="regular-text" name="upvote_options[email_optin_name_label]" id="upvote_options_email_optin_name_label" value="<?php echo esc_attr( $upvote_options['email_optin_name_label'] );?>"/>
                                            <p class="description"><?php _e('Name field label text for email optin popup.','upvote');?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Email Field Label','upvote');?></th>
                                        <td><input type="text" class="regular-text" name="upvote_options[email_optin_email_label]" id="upvote_options_email_optin_email_label" value="<?php echo esc_attr( $upvote_options['email_optin_email_label'] );?>"/>
                                            <p class="description"><?php _e('Email field label text for email optin popup.','upvote');?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Subscribed Message','upvote');?></th>
                                        <td><textarea class="regular-text" name="upvote_options[email_sub_message]" id="upvote_options_email_sub_message"><?php echo esc_attr( $upvote_options['email_sub_message'] );?></textarea>
                                            <p class="description"><?php _e('Email field label text for email optin popup.','upvote');?></p>
                                        </td>
                                    </tr>
                                </table>
                            </div><!-- .inside -->
                        </div><!-- #general -->
                    </div><!-- .meta-box-sortables ui-sortable -->
                </div><!-- .metabox-holder -->
            </div><!-- #upvote-general -->
            <div id="upvote-mailchimp" class="post-box-container">
                <div class="metabox-holder">	
                    <div class="meta-box-sortables ui-sortable">
                        <div id="mailchimp" class="postbox">	
                            <div class="handlediv" title="<?php _e( 'Click to toggle', 'upvote' ); ?>"><br /></div>
            
                            <!-- general settings box title -->
                            <h3 class="hndle">
                                <span style='vertical-align: top;'><?php _e( 'MailChimp Options', 'upvote' ); ?></span>
                            </h3>
        
                            <div class="inside">
                                <table class="form-table">
                                	<tr>
                                        <th><?php _e('Status','upvote');?></th>
                                        <?php if( !empty( $upvote_options['mailchimp_api_key'] ) ) : //Check APi Key Set  
                                            try { $MailChimp = new MailChimp( $upvote_options['mailchimp_api_key'] ); //Check Success ?>
												<td><span style="background-color:#32cd32; color:#fff; font-weight:bold; padding:0 5px 2px;"><?php _e('CONNECTED','upvote');?></span></td>
                                            <?php } catch(Exception $e){ //Error ?>
                                                <td><span style="background-color:gray; color:#fff; font-weight:bold; padding:0 5px 2px;"><?php _e('NOT CONNECTED','upvote');?></span></td>
                                        <?php }
										else : //Else ?>
											<td><span style="background-color:gray; color:#fff; font-weight:bold; padding:0 5px 2px;"><?php _e('NOT CONNECTED','upvote');?></span></td>
                                        <?php endif; //Endif ?>
                                    </tr>
									<tr>
                                        <th><?php _e('MailChimp API Key','upvote');?></th>
                                        <td><input type="text" class="regular-text" name="upvote_options[mailchimp_api_key]" id="upvote_options_mailchimp_api_key" value="<?php echo upvote_obfuscate_string( $upvote_options['mailchimp_api_key'] );?>"/>
                                            <p class="description"><?php printf( '%1$s <a target="_blank" href="https://admin.mailchimp.com/account/api">%2$s</a>', __('API key for connecting with your MailChimp account.','upvote'), __('Get your API key here.','upvote'));?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<th><?php _e('Lists','upvote');?></th>
                                    		<?php if( !empty( $upvote_options['mailchimp_api_key'] ) ) : //Check APi Key Set  
												try { $MailChimp = new MailChimp( $upvote_options['mailchimp_api_key'] ); //Check Success
													if( $lists = $MailChimp->get('lists') ) : //Check Lists Exists ?>
                                                    <td><?php //echo '<pre>';print_r($lists);
														$lists = array_shift( $lists );														
														$savedlist = (array) $upvote_options['mailchimp_lists'];
														foreach( $lists as $list ) : //Loop to list ?>
															<input type="checkbox" name="upvote_options[mailchimp_lists][]" id="upvote_options_mailchimp_list_<?php echo $list['id'];?>" value="<?php echo $list['id'];?>" <?php checked(1, in_array($list['id'],$savedlist));?>/>
															<label for="upvote_options_mailchimp_list_<?php echo $list['id'];?>"><?php echo $list['name'];?></label><br>
                                                        <?php endforeach; //Endforeach ?>
                                                        <p class="description"><?php _e('Use NAME & EMAIL fields for your list to working properly','upvote');?></p>
                                                    </td>
												<?php endif; //Endif
											} catch(Exception $e){ ?>
                                            	<td colspan="2"><span style="background-color:gray; color:#fff; font-weight:bold; padding:0 5px 2px;"><?php echo $e->getMessage();?></span></td>
									<?php 	}
									 endif; //Endif ?>
                                </table>	
                            </div><!-- .inside -->
                        </div><!-- #mailchimp -->
                    </div><!-- .meta-box-sortables ui-sortable -->
                </div><!-- .metabox-holder -->
            </div><!-- #upvote-mailchimp -->
        <?php submit_button( __('Save Changes','upvote') ); ?>
    </form>
</div><!--/.wrap-->