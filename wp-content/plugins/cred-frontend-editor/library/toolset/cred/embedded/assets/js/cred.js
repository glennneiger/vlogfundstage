(function (window, $, settings, utils, gui, mvc, undefined) {
    // uses WordPress 3.3+ features of including jquery-ui effects

    // oonstants
    var KEYCODE_ENTER = 13, KEYCODE_ESC = 27, PREFIX = '_cred_cred_prefix_',
        PAD = '\t', NL = '\r\n';

    // private properties
    var form_id = 0,
        settingsPage = null,
        form_name = '',
        field_data = null,
        CodeMirrorEditors = {},
        // used for MV framework, bindings and interaction
        _credModel, _credView;

    var cred_media_buttons,
        cred_popup_boxes,
        checkButtonTimer
    ;

    // auxilliary functions
    var aux = {

            _originalCredAutogenerateUsernameScaffold: "",
            _originalCredAutogenerateNicknameScaffold: "",
            _originalCredAutogeneratePasswordScaffold: "",

            updateAutogenerateScaffolds: function () {
                jQuery("#cred_autogenerate_username_scaffold").prop("checked", aux._originalCredAutogenerateUsernameScaffold);
                jQuery("#cred_autogenerate_nickname_scaffold").prop("checked", aux._originalCredAutogenerateNicknameScaffold);
                jQuery("#cred_autogenerate_password_scaffold").prop("checked", aux._originalCredAutogeneratePasswordScaffold);

            },
            getLoader: function () {
                return jQuery('#cred_ajax_loader_small_id');
            },
            isWPMLScaffoldSelected: function () {
                return jQuery('#cred_include_wpml_scaffold').is(':checked');
            },
            isCaptchaScaffoldSelected: function () {
                return jQuery('#cred_include_captcha_scaffold').is(':checked');
            },
            reloadUserFields: function () {
                var $autogenerateUsernameIsChecked = jQuery('#cred_autogenerate_username_scaffold').first();
                if (typeof($autogenerateUsernameIsChecked) !== 'undefined') {
                    $autogenerateUsernameIsChecked = $autogenerateUsernameIsChecked.is(':checked');
                }
                var $autogenerateNickNameIsChecked = jQuery('#cred_autogenerate_nickname_scaffold').first();
                if (typeof($autogenerateNickNameIsChecked) !== 'undefined') {
                    $autogenerateNickNameIsChecked = $autogenerateNickNameIsChecked.is(':checked');
                }
                var $autogeneratePasswordIsChecked = jQuery('#cred_autogenerate_password_scaffold').first();
                if (typeof($autogeneratePasswordIsChecked) !== 'undefined') {
                    $autogeneratePasswordIsChecked = $autogeneratePasswordIsChecked.is(':checked');
                }

                if (typeof _busy === 'undefined' || !_busy) {
                    _busy = true;
                    aux.getLoader().show();
                    aux.disableScaffold();

                    var role = aux.getUserRole();
                    var type_form = aux.getFormType();

                    aux.updateAutogenerateFields();

                    $.ajax({
                        url: self.route('/Forms/getUserFields'),
                        timeout: 10000,
                        type: 'POST',
                        data: 'role=' + role + '&type_form=' + type_form + '&ag_pass=' + $autogeneratePasswordIsChecked + '&ag_uname=' + $autogenerateUsernameIsChecked + '&ag_nname=' + $autogenerateNickNameIsChecked + '&_wpnonce=' + cred_settings._cred_wpnonce,
                        dataType: 'json',
                        success: function (resp) {
                            aux.onLoad(resp);
                            aux.genUserScaffold("reload");
                        },
                        complete: function () {
                            _busy = false;
                            aux.enableScaffold();
                            aux.getLoader().hide();
                        }
                    });
                }
            },
            updateAutogenerateFields: function () {
                if (jQuery('#cred_autogenerate_username_scaffold').length) {
                    var $credAutogenereateUsername = jQuery('#cred_autogenerate_username_scaffold').first();
                    $credAutogenereateUsername = $credAutogenereateUsername.is(':checked');
                }
                if (jQuery('#cred_autogenerate_nickname_scaffold').length) {
                    var $credAutogenerateNickname = jQuery('#cred_autogenerate_nickname_scaffold').first();
                    $credAutogenerateNickname = $credAutogenerateNickname.is(':checked');
                }
                if (jQuery('#cred_autogenerate_password_scaffold').length) {
                    var $credAutogeneratePassword = jQuery('#cred_autogenerate_password_scaffold').first();
                    $credAutogeneratePassword = $credAutogeneratePassword.is(':checked');
                }

                aux._originalCredAutogenerateUsernameScaffold = typeof($credAutogenereateUsername) !== 'undefined' ? $credAutogenereateUsername : cred_settings.autogenerate_username_scaffold;
                aux._originalCredAutogenerateNicknameScaffold = typeof($credAutogenerateNickname) !== 'undefined' ? $credAutogenerateNickname : cred_settings.autogenerate_nickname_scaffold;
                aux._originalCredAutogeneratePasswordScaffold = typeof($credAutogeneratePassword) !== 'undefined' ? $credAutogeneratePassword : cred_settings.autogenerate_password_scaffold;
            },
            disableScaffold: function () {
                jQuery('[name="_cred[form][user_role][]"]').prop("disabled", "disabled");
                jQuery('#cred-scaffold-insert').hide();
                jQuery('#cred_autogenerate_username_scaffold').prop("disabled", "disabled");
                jQuery('#cred_autogenerate_nickname_scaffold').prop("disabled", "disabled");
                jQuery('#cred_autogenerate_password_scaffold').prop("disabled", "disabled");
            },
            enableScaffold: function () {
                jQuery('[name="_cred[form][user_role][]"]').prop("disabled", false);
                jQuery('#cred-scaffold-insert').show();
                jQuery('#cred_autogenerate_username_scaffold').prop("disabled", false);
                jQuery('#cred_autogenerate_nickname_scaffold').prop("disabled", false);
                jQuery('#cred_autogenerate_password_scaffold').prop("disabled", false);
            },
            isUserForm: function () {
                return jQuery('[name="_cred[form][user_role][]"]').length > 0 && jQuery('[name="_cred[form][user_role][]"]').is(':visible');
            },
            getFormType: function () {
                return jQuery('input[name="_cred[form][type]"]:checked').val();
            },
            getUserRole: function () {
                var user_role = ('new' === aux.getFormType()) ? jQuery('#cred_form_user_role').val() : jQuery('.roles_checkboxes:checked').val();
                if (typeof user_role == 'undefined') {
                    user_role = "";
                }
                return user_role;
            },
            checkCredFormType: function () {
                jQuery('.cred_field_add_code').hide();

                var is_user_form = aux.isUserForm();
                var $type_form_selector = jQuery('input[name="_cred[form][type]"]');

                if ($type_form_selector) {
                    if (is_user_form) {
                        aux.reloadUserFields();
                    }

                    var credFormTypeSelected = aux.getFormType();
                    if ('new' == credFormTypeSelected) {
                        jQuery('.cred_field_add_code').show();

                        jQuery('.cred_notification_field_only_if_changed input[type=checkbox]').attr('disabled', 'disabled');
                        jQuery('.cred_notification_field_only_if_changed').hide();
                        if (jQuery('.when_submitting_form_text').length) {
                            jQuery('.when_submitting_form_text').html('When a new user is created by this form');
                        }

                        if (is_user_form) {
                            jQuery('#cred_form_user_role').change(function () {
                                aux.reloadUserFields();
                            });
                        }

                        if (jQuery('#cred_post_status') && jQuery('#cred_post_status').length > 0) {
                            jQuery("#cred_post_status option").each(function () {
                                if ('original' === jQuery(this).val()) {
                                    jQuery(this).remove();
                                }
                            });
                        }
                    } else {
                        credFieldAddCodeButtons();

                        jQuery('.cred_notification_field_only_if_changed').show();
                        jQuery('.cred_notification_field_only_if_changed input[type=checkbox]').removeAttr('disabled');
                        if (jQuery('.when_submitting_form_text').length) {
                            jQuery('.when_submitting_form_text').html('When a user is updated by this form');
                        }

                        if (is_user_form) {
                            aux.reloadUserFields();
                            jQuery('#cred_form_user_role').change(function () {
                            });
                        }

                        if (jQuery('#cred_post_status') && jQuery('#cred_post_status').length > 0
                            && jQuery("#cred_post_status option[value='original']").length <= 0) {
                            jQuery($__my_option).appendTo("#cred_post_status");
                        }
                    }
                }
            },
            makeResizable: function ($textarea) {
                var interv, dragging = false, id = $textarea.attr('id');
                // simulate codemirror resize based on wp handler resize
                var resizeCodemirror = function () {
                    if (CodeMirrorEditors[id]) {
                        CodeMirrorEditors[id].setSize(jQuery('#wp-content-wrap').width() - 4, $textarea.height());
                    }
                };

                // late init to have the element
                utils.waitUntilElement('#content-resize-handle', function () {
                    jQuery('#content-resize-handle').on('mousedown', function () {
                        if (!dragging) {
                            dragging = true;
                            interv = setInterval(resizeCodemirror, 100);
                        }
                    });
                    jQuery(document).on('mouseup', function () {
                        if (dragging) {
                            clearInterval(interv);
                            dragging = false;
                        }
                    });

                    jQuery(window).resize(function () {
                        resizeCodemirror();
                    });
                });
            },
            makeAreaResizable: function ($textarea) {
                // resizable textarea#content
                (function () {
                    var isDragging = false, id = $textarea.attr('id');
                    // simulate codemirror resize based on wp handler resize
                    var resizeCodemirror = function () {
                        if (CodeMirrorEditors[id]) {
                            CodeMirrorEditors[id].setSize(jQuery('#wp-content-wrap').width() - 4, $textarea.height());
                        }
                    };

                    var offset = null, el;
                    // No point for touch devices
                    if (!$textarea.length || 'ontouchstart' in window)
                        return;

                    function dragging(e) {
                        $textarea.height(Math.max(50, offset + e.pageY) + 'px');
                        resizeCodemirror();
                        return false;
                    }

                    function endDrag(e) {
                        var height;

                        $textarea.focus();
                        jQuery(document).unbind('mousemove', dragging).unbind('mouseup', endDrag);

                        height = parseInt($textarea.css('height'), 10);

                        // sanity check
                        if (height && height > 50 && height < 5000)
                            utils.setUserSetting('cred_settings', id + '_size', height);
                    }

                    $textarea.css('resize', 'none');
                    el = $textarea.closest('.cred-editor-wrap').find('.cred-content-resize-handle');
                    el.on('mousedown', function (e) {
                        offset = $textarea.height() - e.pageY;
                        $textarea.blur();
                        jQuery(document).mousemove(dragging).mouseup(endDrag);
                        return false;
                    });
                })();
            },
			enableEditorCodemirror: function() {
				// Enable CodeMirror
				// @todo apply the setting about CM editor height autoresize: expand or fixed.
				var $_metabox = jQuery('#content').closest('.postbox'),
					_metabox_closed = false,
					_metabox_display = false;

				if ($_metabox.hasClass('closed') || 'none' == $_metabox.css('display')) {
					_metabox_closed = true;
					$_metabox.removeClass('closed');
				}
				if ('none' == $_metabox.css('display')) {
					_metabox_display = 'none';
					$_metabox.css('display', 'block');
				}

				CodeMirrorEditors['content'] = CodeMirror.fromTextArea(document.getElementById("content"), {
					mode: 'myshortcodes',
					tabMode: "indent",
					lineWrapping: true,
					lineNumbers: true
				});
				
				//Add more space for buttons
				jQuery("#wp-content-editor-tools").css("padding-top", "7px");
				jQuery(".wp-media-buttons").css("position", "inherit");
				jQuery("#wp-content-wrap").addClass("postarea-withborder");
				jQuery("#post-status-info").addClass("postarea-withborder");

				// needed for scrolling
				var height = Math.min(5000, Math.max(50, getUserSetting('ed_size')));
				if (getUserSetting('ed_size') === '') {
					height = 300;
				}
				jQuery('#content').css('resize', 'none').height(height + 'px');
				CodeMirrorEditors['content'].setSize(jQuery('#wp-content-wrap').width() - 2, height < 250 ? 250 : height);

				if ('none' == _metabox_display) {
					$_metabox.css('display', 'none');
				}
				if (_metabox_closed) {
					$_metabox.addClass('closed');
				}

				// WP 4.3 compat: make sure the #content is hidden
				jQuery('#content').height('-20px').css('padding', '0px');
				
				// Enable Quicktags in Codemirror
				var content_qt_instance = _.findWhere( window.QTags.instances, { id: "content" } );
				if ( undefined !== content_qt_instance ) {
					WPV_Toolset.add_qt_editor_buttons( content_qt_instance, CodeMirrorEditors["content"] );
				}
				
				return true;
			},
            enableExtraCodeMirror: function () {
                // if codemirror activated, enable syntax highlight
                if (window.CodeMirror) {
                    var $css_ed = jQuery("#cred-extra-css-editor"), $js_ed = jQuery("#cred-extra-js-editor"), height;
                    if ($css_ed && $css_ed[0]) {
                        utils.swapEl($css_ed, function () {
                            CodeMirrorEditors['cred-extra-css-editor'] = CodeMirror.fromTextArea($css_ed[0], {
                                mode: "css",
                                tabMode: "indent",
                                lineWrapping: true,
                                lineNumbers: true
                            });
                            // needed for scrolling
                            height = Math.min(5000, Math.max(50, utils.getUserSetting('cred_settings', 'cred-extra-css-editor_size', 300)));
                            $css_ed.css('resize', 'none').height(height + 'px');
                            CodeMirrorEditors['cred-extra-css-editor'].setSize(null, height);
                            aux.makeAreaResizable($css_ed);
                        });
                        $css_ed.hide();
                    }
                    if ($js_ed && $js_ed[0]) {
                        utils.swapEl($js_ed, function () {
                            CodeMirrorEditors['cred-extra-js-editor'] = CodeMirror.fromTextArea($js_ed[0], {
                                mode: "javascript",
                                tabMode: "indent",
                                lineWrapping: true,
                                lineNumbers: true
                            });
                            // needed for scrolling
                            height = Math.min(5000, Math.max(50, utils.getUserSetting('cred_settings', 'cred-extra-js-editor_size', 300)));
                            $js_ed.css('resize', 'none').height(height + 'px');
                            CodeMirrorEditors['cred-extra-js-editor'].setSize(null, height);
                            aux.makeAreaResizable($js_ed);
                        });
                        $js_ed.hide();
                    }
                }
            },
            genScaffold: function () {
                if (cred_settings._current_page == 'cred-user-form') {
                    return aux.genUserScaffold("scaffold");
                }
                var response = field_data;

                if (!response || !response.post_fields) {
                    gui.Popups.alert({message: cred_settings.locale.form_types_not_set, class: 'cred-dialog'});
                    return false;
                }

                var includeWPML = false;
                if (aux.isWPMLScaffoldSelected()) {
                    includeWPML = true;
                }

                var form_name_1 = jQuery('#title').val();
                var form_id_1 = jQuery('#post_ID').val();

                var scaffold_content = jQuery('#cred-scaffold-area');
                var groups_out = '';
                var groups = {};
                var counter = 0;
                for (var group_field in response.groups) {
                    if (response.groups.hasOwnProperty(group_field)) {
                        counter++;
                        var fields = response.groups[group_field];
                        groups[group_field] = fields;
                        fields = fields.split(',');
                        groups_out += aux.groupOutput(group_field, fields, response.groups_conditions, response.custom_fields, form_id_1, form_name_1, includeWPML, PAD) + NL;
                    }
                }

                var taxs_out = '';
                if (parseInt(response.taxonomies_count, 10) > 0) {
                    for (var taxonomy_field in response.taxonomies) {
                        if (response.taxonomies.hasOwnProperty(taxonomy_field)) {
                            taxs_out += aux.taxOutput(response.taxonomies[taxonomy_field], form_id_1, form_name_1, includeWPML, '') + NL + NL;
                        }
                    }
                }
                var parents_out = '';
                if (parseInt(response.parents_count, 10) > 0) {
                    for (var parent_field in response.parents) {
                        if (response.parents.hasOwnProperty(parent_field)) {
                            parents_out += aux.fieldOutput(response.parents[parent_field], form_id_1, form_name_1, includeWPML, '',
                                    // extra params
                                    'date', 'desc', 0,
                                    false, 'No Parent', '-- Select ' + response.parents[parent_field].data.post_type + ' --', response.parents[parent_field].data.post_type + ' must be selected') + NL;
                        }
                    }
                }
                // add fields
                var out = '';
                if ('minimal' == _credModel.get('[form][theme]')) { // bypass script and other styles added to form, minimal
                    out += "[credform class='cred-form cred-keep-original']" + NL + NL;
                } else {
                    out += "[credform class='cred-form']" + NL + NL;
                }
                out += PAD + aux.shortcode(response.form_fields['form_messages']) + NL + NL;
                out += aux.fieldOutput(response.post_fields['post_title'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                if (response.post_fields['post_content'].supports) {
                    out += aux.fieldOutput(response.post_fields['post_content'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }
                if (response.post_fields['post_excerpt'].supports) {
                    out += aux.fieldOutput(response.post_fields['post_excerpt'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }
                if (response.extra_fields['_featured_image'].supports) {
                    out += aux.fieldOutput(response.extra_fields['_featured_image'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }

                out += groups_out;
                if (parseInt(response.taxonomies_count, 10) > 0) {
                    out += aux.groupOutputContent('taxonomies', 'Taxonomies', taxs_out, PAD) + NL + NL;
                }
                if (parseInt(response.parents_count, 10) > 0) {
                    out += aux.groupOutputContent('parents', 'Parents', parents_out, PAD) + NL + NL;
                }
                if (aux.isCaptchaScaffoldSelected()) {
                    if (response.extra_fields['recaptcha']['private_key'] != '' && response.extra_fields['recaptcha']['public_key'] != '') {
                        //out += PAD + '<div class="cred-field cred-field-recaptcha">' + aux.shortcode(resp.extra_fields['recaptcha']) + '</div>' + NL + NL;
                        out += PAD + '<div class="form-group">' + aux.shortcode(response.extra_fields['recaptcha']) + '</div>' + NL;
                    } else {
                        jQuery('#cred_include_captcha_scaffold').attr("checked", false);
                        alert('Captcha keys are empty !');
                    }
                }
                out += PAD + aux.shortcode(response.form_fields['form_submit']) + NL + NL;
                out += '[/credform]' + NL;

                out = out.replace(/\n\s*\n/g, NL);

                scaffold_content.val(out);
                return true;
            },
            genUserScaffold: function (referrer) {
                if (referrer == 'button') {
                    var credFormUserRole = aux.getUserRole();
                    if (aux.getFormType() == 'new' &&
                        (credFormUserRole == 'undefined' ||
                        credFormUserRole == null ||
                        credFormUserRole == '')) {
                        gui.Popups.alert({message: cred_settings.locale.invalid_user_role, class: 'cred-dialog'});
                        return false;
                    }
                }

                var response = field_data;

                if (!response || !response.user_fields) {
                    gui.Popups.alert({message: cred_settings.locale.form_user_not_set, class: 'cred-dialog'});
                    return false;
                }

                var includeWPML = false;
                if (aux.isWPMLScaffoldSelected()) {
                    includeWPML = true;
                }

                var form_name_1 = jQuery('#title').val();
                var form_id_1 = jQuery('#post_ID').val();

                var scaffold_content = jQuery('#cred-scaffold-area');
                var groups_out = '';
                var groups = {};
                var counter = 0;
                for (var group_field in response.groups) {
                    if (response.groups.hasOwnProperty(group_field)) {
                        counter++;
                        var fields = response.groups[group_field];
                        groups[group_field] = fields;
                        fields = fields.split(',');
                        groups_out += aux.groupOutput(group_field, fields, response.groups_conditions, response.custom_fields, form_id_1, form_name_1, includeWPML, PAD) + NL;
                    }
                }

                var taxs_out = '';
                if (parseInt(response.taxonomies_count, 10) > 0) {
                    for (var taxonomy_field in response.taxonomies) {
                        if (response.taxonomies.hasOwnProperty(taxonomy_field)) {
                            taxs_out += aux.taxOutput(response.taxonomies[taxonomy_field], form_id_1, form_name_1, includeWPML, '') + NL;
                        }
                    }
                }
                var parents_out = '';
                if (parseInt(response.parents_count, 10) > 0) {
                    for (var parent_field in response.parents) {
                        if (response.parents.hasOwnProperty(parent_field)) {
                            parents_out += aux.fieldOutput(response.parents[parent_field], form_id_1, form_name_1, includeWPML, '',
                                    // extra params
                                    'date', 'desc', 0,
                                    false, 'No Parent', '-- Select ' + response.parents[parent_field].data.post_type + ' --', response.parents[parent_field].data.post_type + ' must be selected') + NL;
                        }
                    }
                }
                // add fields
                var out = '';
                if ('minimal' == _credModel.get('[form][theme]')) {
                    // bypass script and other styles added to form, minimal
                    out += "[creduserform class='cred-user-form cred-keep-original']" + NL + NL;
                } else {
                    out += "[creduserform class='cred-user-form']" + NL + NL;
                }
                out += PAD + aux.shortcode(response.form_fields['form_messages']) + NL + NL;
                // The following fields might not be rendered because they are autogenerated
                // Avoid the empty extra lines if that is the case
                if (response.user_fields['user_login']) {
                    out += aux.fieldOutput(response.user_fields['user_login'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }
                if (response.user_fields['user_pass']) {
                    out += aux.fieldOutput(response.user_fields['user_pass'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }
                if (response.user_fields['user_pass2']) {
                    out += aux.fieldOutput(response.user_fields['user_pass2'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }
                if (response.user_fields['user_email']) {
                    out += aux.fieldOutput(response.user_fields['user_email'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }
                if (response.user_fields['nickname']) {
                    out += aux.fieldOutput(response.user_fields['nickname'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }

                for (var f in response.custom_fields) {
                    if (response.custom_fields.hasOwnProperty(f)) {
                        if (response.custom_fields[f].meta_key == 'description' && response.custom_fields[f].post_type == 'user' && response.custom_fields[f].name == 'Biographical Info') {
                        } else {
                            out += aux.fieldOutput(response.custom_fields[f], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                        }
                    }
                }

                if (response.extra_fields['_featured_image'].supports) {
                    out += aux.fieldOutput(response.extra_fields['_featured_image'], form_id_1, form_name_1, includeWPML, PAD) + NL + NL;
                }

                out += groups_out;
                if (parseInt(response.taxonomies_count, 10) > 0) {
                    out += aux.groupOutputContent('taxonomies', 'Taxonomies', taxs_out, PAD) + NL + NL;
                }
                if (parseInt(response.parents_count, 10) > 0) {
                    out += aux.groupOutputContent('parents', 'Parents', parents_out, PAD) + NL + NL;
                }
                if (jQuery('#cred_include_captcha_scaffold').is(':checked')) {
                    if (response.extra_fields['recaptcha']['private_key'] != '' && response.extra_fields['recaptcha']['public_key'] != '') {
                        //out += PAD + '<div class="cred-field cred-field-recaptcha">' + aux.shortcode(resp.extra_fields['recaptcha']) + '</div>' + NL + NL;
                        out += PAD + '<div class="form-group">' + aux.shortcode(response.extra_fields['recaptcha']) + '</div>' + NL;
                    } else {
                        jQuery('#cred_include_captcha_scaffold').attr("checked", false);
                        alert('Captcha keys are empty !');
                    }
                }
                out += PAD + aux.shortcode(response.form_fields['form_submit']) + NL + NL;
                out += '[/creduserform]' + NL;

                out = out.replace(/\n\s*\n/g, NL);

                scaffold_content.val(out);
                return true;
            },
            onLoad: function (response) {
				if ( response == null ) {
					return;
				}
                var data = null, content_item, content_item_inside;
                var box_content = jQuery('#cred-shortcodes-box-inner');
                box_content.empty();

                // save data for future refernce
                field_data = response;
                var post_response = "";

                if (response.form_fields && parseInt(response.form_fields_count) > 0) {
                    content_item = jQuery('<div class="cred-accordeon-item"><a href="javascript:;" class="cred-fields-group-heading">' + cred_settings.locale.form_fields + '</a></div>');
                    content_item_inside = jQuery('<div class="cred-accordeon-item-inside"></div>');
                    content_item.append(content_item_inside);
                    box_content.append(content_item);
                    post_response = response.form_fields;
                    for (var response_field in post_response) {
                        if (post_response.hasOwnProperty(response_field)) {
                            data = jQuery("<a href='javascript:;' class='button cred_field_add' title='" + post_response[response_field].description + "'>" + post_response[response_field].name + "</a>");
                            data.data('field', post_response[response_field]);
                            content_item_inside.append(data);
                        }
                    }
                }
                if (response.post_fields && parseInt(response.post_fields_count) > 0) {
                    content_item = jQuery('<div class="cred-accordeon-item"><a href="javascript:;" class="cred-fields-group-heading">' + cred_settings.locale.post_fields + '</a></div>');
                    content_item_inside = jQuery('<div class="cred-accordeon-item-inside"></div>');
                    content_item.append(content_item_inside);
                    box_content.append(content_item);
                    post_response = response.post_fields;
                    for (var response_field in post_response) {
                        if (post_response.hasOwnProperty(response_field)) {
                            data = jQuery("<a href='javascript:;' class='button cred_field_add' title='" + post_response[response_field].description + "'>" + post_response[response_field].name + "</a>");
                            data.data('field', post_response[response_field]);
                            content_item_inside.append(data);
                        }
                    }
                }
                if (response.custom_fields && parseInt(response.custom_fields_count) > 0) {
                    content_item = jQuery('<div class="cred-accordeon-item"><a href="javascript:;" class="cred-fields-group-heading">' + cred_settings.locale.custom_fields + '</a></div>');
                    content_item_inside = jQuery('<div class="cred-accordeon-item-inside"></div>');
                    content_item.append(content_item_inside);
                    box_content.append(content_item);
                    post_response = response.custom_fields;
                    for (var response_field in post_response) {
                        if (post_response.hasOwnProperty(response_field)) {
                            data = jQuery("<a href='javascript:;' class='button cred_field_add' title='" + post_response[response_field].description + "'>" + post_response[response_field].name + "</a>");
                            data.data('field', post_response[response_field]);
                            content_item_inside.append(data);
                        }
                    }
                }
                if (response.taxonomies && parseInt(response.taxonomies_count) > 0) {
                    content_item = jQuery('<div class="cred-accordeon-item"><a href="javascript:;" class="cred-fields-group-heading">' + cred_settings.locale.taxonomy_fields + '</a></div>');
                    content_item_inside = jQuery('<div class="cred-accordeon-item-inside"></div>');
                    content_item.append(content_item_inside);
                    box_content.append(content_item);
                    post_response = response.taxonomies;
                    for (var response_field in post_response) {
                        if (post_response.hasOwnProperty(response_field)) {
                            post_response[response_field].taxonomy = true;
                            data = jQuery("<a href='javascript:;' class='button cred_field_add'>" + post_response[response_field].label + "</a>");
                            data.data('field', post_response[response_field]);
                            content_item_inside.append(data);
                            if (post_response[response_field].hierarchical) {
                                post_response[response_field].aux = {
                                    master_taxonomy: post_response[response_field].name,
                                    name: post_response[response_field].name + '_add_new',
                                    add_new_taxonomy: true
                                };
                                data = jQuery("<a href='javascript:;' class='button cred_field_add'>" + post_response[response_field].label + ' Add New' + "</a>");
                                data.data('field', post_response[response_field].aux);
                                content_item_inside.append(data);
                            } else {
                                post_response[response_field].aux = {
                                    master_taxonomy: post_response[response_field].name,
                                    name: post_response[response_field].name + '_popular',
                                    popular: true
                                };
                                data = jQuery("<a href='javascript:;' class='button cred_field_add'>" + post_response[response_field].label + ' Popular' + "</a>");
                                data.data('field', post_response[response_field].aux);
                                content_item_inside.append(data);
                            }
                        }
                    }
                }
                if (response.parents && parseInt(response.parents_count) > 0) {
                    content_item = jQuery('<div class="cred-accordeon-item"><a href="javascript:;" class="cred-fields-group-heading">' + cred_settings.locale.parent_fields + '</a></div>');
                    content_item_inside = jQuery('<div class="cred-accordeon-item-inside"></div>');
                    content_item.append(content_item_inside);
                    box_content.append(content_item);
                    post_response = response.parents;
                    for (var response_field in post_response) {
                        if (post_response.hasOwnProperty(response_field)) {
                            data = jQuery("<a href='javascript:;' class='button cred_field_add' title='" + post_response[response_field].description + "'>" + post_response[response_field].name + "</a>");
                            data.data('field', post_response[response_field]);
                            content_item_inside.append(data);
                        }
                    }
                }
                if (response.extra_fields && parseInt(response.extra_fields_count) > 0) {
                    content_item = jQuery('<div class="cred-accordeon-item"><a href="javascript:;" class="cred-fields-group-heading">' + cred_settings.locale.extra_fields + '</a></div>');
                    content_item_inside = jQuery('<div class="cred-accordeon-item-inside"></div>');
                    content_item.append(content_item_inside);
                    box_content.append(content_item);
                    post_response = response.extra_fields;
                    var disabled_fields = [];
                    for (var response_field in post_response) {
                        if (post_response.hasOwnProperty(response_field)) {
                            if (!post_response[response_field].disabled) {
                                data = jQuery("<a href='javascript:;' class='button cred_field_add' title='" + post_response[response_field].description + "'>" + post_response[response_field].name + "</a>");
                                data.data('field', post_response[response_field]);
                                content_item_inside.append(data);
                            } else {
                                data = jQuery("<div class='cred_disabled_container'><a href='javascript:;' class='button cred_field_disabled' disabled='disabled'>" + post_response[response_field].name + "</a><span class='cred-field-disabled-reason'>" + post_response[response_field].disabled_reason + "</span></div>");
                                data.data('field', post_response[response_field]);
                                // add them at the end
                                disabled_fields.push(data);
                            }
                        }
                    }
                    for (var i = 0; i < disabled_fields.length; i++) {
                        content_item_inside.append(disabled_fields[i]);
                    }
                }
            }
            ,
            shortcode: function (field, extra) {
                /* use underscores in shortcodes and not hyphens anymore,
                 try to keep compatibility */
                var field_out = '';
                var post_type = '';
                var value = " value=''";
                if (field && field.slug) {
                    if (field.post_type) {
                        post_type = " post='" + field.post_type + "'";
                    }
                    if (field.value) {
                        value = " value='" + field.value + "'";
                    }
                    // add url parameter
                    if (!field.taxonomy && !field.is_parent && 'form_messages' != field.type) {
                        value += " urlparam=''";
                    }
                    if (field.type == 'image' || field.type == 'file') {
                        var max_width = (extra && extra.max_width) ? extra.max_width : false;
                        var max_height = (extra && extra.max_height) ? extra.max_height : false;
                        if (max_width && !isNaN(max_width)) {
                            value += " max_width='" + max_width + "'";
                        }
                        if (max_height && !isNaN(max_height)) {
                            value += " max_height='" + max_height + "'";
                        }
                    }
                    if (field.is_parent) {
                        var parent_order = (extra && extra.parent_order) ? extra.parent_order : false;
                        var parent_ordering = (extra && extra.parent_ordering) ? extra.parent_ordering : false;
                        var parent_results = (extra && extra.parent_max_results) ? extra.parent_max_results : false;
                        var required = (extra && extra.required) ? extra.required : false;
                        var select_parent_text = (extra && extra.select_parent_text) ? extra.select_parent_text : '';
                        var validate_parent_text = (extra && extra.validate_parent_text) ? extra.validate_parent_text : false;
                        if (parent_results !== false && !isNaN(parent_results)) {
                            value += " max_results='" + parent_results + "'";
                        }
                        if (parent_order) {
                            value += " order='" + parent_order + "'";
                        }
                        if (parent_ordering) {
                            value += " ordering='" + parent_ordering + "'";
                        }
                        if (required) {
                            value += " required='" + required.toString() + "'";
                        }

                        if (select_parent_text === '') {
                            value += " select_text='" + cred_settings.locale.default_select_text + "'";
                        } else {
                            value += " select_text='" + select_parent_text + "'";
                        }
                        if (required && validate_parent_text !== false) {
                            value += " validate_text='" + validate_parent_text + "'";
                        }
                    }
                    if (field.type == 'select' && !field.is_parent) {
                        value += " select_text='" + cred_settings.locale.default_select_text + "'";
                    }
                    if (field.type == 'textfield' ||
                        field.type == 'textarea' ||
                        field.type == 'wysiwyg' ||
                        field.type == 'date' ||
                        field.type == 'phone' ||
                        field.type == 'url' ||
                        field.type == 'numeric' ||
                        field.type == 'email') {
                        var readonly = (extra && extra.readonly) ? extra.readonly : false;
                        var escape = (extra && extra.escape) ? extra.escape : false;
                        var placeholder = (extra && extra.placeholder) ? extra.placeholder : false;
                        if (readonly) {
                            value += " readonly='" + readonly.toString() + "'";
                        }
                        if (escape) {
                            value += " escape='" + escape.toString() + "'";
                        }
                        if (placeholder && '' != placeholder) {
                            value += " placeholder='" + placeholder + "'";
                        }
                    }
                    if (field.type == 'checkbox' ||
                        field.type == 'checkboxes' ||
                        field.type == 'radio' ||
                        field.type == 'form_messages' ||
                        field.type == 'audio' ||
                        field.type == 'video' ||
                        field.type == 'image' ||
                        field.type == 'file' ||
                        field.type == 'wysiwyg' ||
                        field.type == 'form_submit'
                    ) {
						if (field.type == 'checkbox') {
							// Checkbox fields does not support a class attribute yet
                            //value += " class=''";
                        }
						if (field.type == 'checkboxes') {
                            // Checkboxes fields does not support a class attribute yet
                            //value += " class=''";
                        }
						if (field.type == 'radio') {
                            // Radio fields does not support a class attribute yet
                            //value += " class=''";
                        }
                        if (field.type == 'form_messages') {
                            value += " class='alert alert-warning'";
                        }
                        if (field.type == 'form_submit') {
                            value += " class='btn btn-primary btn-lg'";
                        }
                    } else {
                        value += " class='form-control'";
                    }

                    if (field.type != 'form_messages') {
                        value += " output='bootstrap'";
                    }

                    field_out = "[cred_field field='" + field.slug + "'" + post_type + value + "]";
                }
                if (field && field.taxonomy) {
                    if (field.hierarchical) {
						// Hierarchical taxonomy fields are displayed as default as checkboxes, which do no support a class attribute yet
                        field_out = "[cred_field field='" + field.name + "' display='checkbox' output='bootstrap']";
                    } else {
                        field_out = "[cred_field field='" + field.name + "' class='form-control' output='bootstrap']";
                    }
                }
                if (field && field.popular) {
                    field_out = "[cred_field field='" + field.name + "' taxonomy='" + field.master_taxonomy + "' type='show_popular']";
                }
                if (field && field.add_new_taxonomy) {
                    field_out = "[cred_field field='" + field.name + "' taxonomy='" + field.master_taxonomy + "' type='add_new']";
                }
                return field_out;
            }
            ,
            fieldOutput: function (field, form_id, form_name, WPML, pad) {
                if (!pad) {
                    pad = '';
                }
                var field_out = [];
                var post_type = '';
                var value = '';
                var label = '';
                WPML = WPML || false;

                if (field) {
                    field_out.push(pad + '<div class="form-group">');
                    if ('checkbox' != field.type) {
                        if (WPML) {
                            field_out.push(pad + PAD + "<label>[wpml-string context='" + cred_settings._current_page + "-" + form_name + "-" + form_id + "' name='" + field.name + "']" + field.name + "[/wpml-string]</label>");
                        } else {
                            if (field.name == 'Post Description') {
                                field.name = 'Post Content';
                            }
                            if (field.name == 'Post Name') {
                                field.name = 'Post Title';
                            }
                            // .replace(/\\(.)/mg, "$1") removes extra slashes added by WP (e.g. on apostrophes)
                            field_out.push(pad + PAD + '<label>' + field.name.replace(/\\(.)/mg, "$1") + '</label>');
                        }
                    }
                    var args = [field];
                    if (arguments.length == 5) {
                        args = args.concat(Array.prototype.slice.call(arguments, 6));
                    } else {
                        args = args.concat(Array.prototype.slice.call(arguments, 5));
                    }
                    field_out.push(pad + PAD + aux.shortcode.apply(null, args));
                    field_out.push(pad + '</div>');
                }
                return field_out.join(NL);
            }
            ,
            groupOutput: function (group, fields, conditions, obj, form_id, form_name, WPML, pad) {
                if (!pad) {
                    pad = '';
                }
                var group_out = [];
                var group_conditional = false,
                    group_conditiona_string = '';
                if (conditions.hasOwnProperty(group)) {
                    group_conditional = true;
                    group_conditiona_string = conditions[group];
                }
                if (group_conditional) {
                    group_out.push(pad + "[cred_show_group if=\"" + group_conditiona_string + "\"  mode='fade-slide']");
                }

                for (var ii = 0; ii < fields.length; ii++) {
                    if (obj[fields[ii]] && obj[fields[ii]]._cred_ignore) {
                        continue;
                    }
                    group_out.push(aux.fieldOutput(obj[fields[ii]], form_id, form_name, WPML, pad) + NL);
                }

                if (group_conditional) {
                    group_out.push(pad + '[/cred_show_group]');
                }
                return group_out.join(NL);
            }
            ,
            groupOutputContent: function (slug, group_name, content, pad) {
                pad = '';
                var group_out = [];
                var lines = content.split(NL);
                for (var i = 0; i < lines.length; i++) {
                    lines[i] = pad + PAD + lines[i];
                }
                content = lines.join(NL);
                group_out.push(content);
                return group_out.join(NL);
            }
            ,
            taxOutput: function (tax, form_id, form_name, WPML, pad) {
                WPML = WPML || false;
                if (!pad)
                    pad = '';
                var tax_out = [];
                tax_out.push('<div class="form-group">');

                if (WPML) {
                    tax_out.push(pad + PAD + "<label>[wpml-string context='" + cred_settings._current_page + "-" + form_name + "-" + form_id + "' name='" + tax.label + "']" + tax.label + "[/wpml-string]</label>");
                } else {
                    tax_out.push(pad + PAD + '<label>' + tax.label + '</label>');
                }
                tax_out.push(pad + PAD + aux.shortcode(tax));
                tax_out.push(pad + PAD + aux.shortcode(tax.aux));
                tax_out.push(pad + '</div>');
                return tax_out.join(NL);
            }
            ,
            formPreview: function () {
                if (cred_settings._current_page == 'cred-user-form') {
                    return aux.userFormPreview();
                }
                var data = utils.getContent(jQuery('#content'));

                if ('' == $.trim(data))
                    return false;

                var post_type = _credModel.get('[post][post_type]'), //jQuery('#cred_post_type').val();
                    form_type = _credModel.get('[form][type]'), //jQuery('#cred_form_type').val();
                    css_to_use = _credModel.get('[form][theme]'), //jQuery('input[name="_cred[form][theme]"]:checked').val();
                    extra_css = utils.getContent(jQuery('#cred-extra-css-editor')),
                    extra_js = utils.getContent(jQuery('#cred-extra-js-editor')),
                    previewForm, previewPopup, id, target, action;

                if (!post_type || post_type == '' || !form_type || form_type == '') {
                    gui.Popups.alert({message: cred_settings.locale.form_types_not_set, class: 'cred-dialog'});
                    return false;
                }

                id = jQuery('#post_ID').val();
                target = 'CRED_Preview_' + id;
                action = cred_settings.homeurl + 'index.php?cred_form_preview=' + id;

                previewPopup = window.open('', target, "status=0,title=0,height=600,width=800,scrollbars=1,resizable=1");
                if (previewPopup) {
                    previewForm = jQuery("<form style='display:none' name='cred_form_preview_form' method='post' target='" + target + "' action='" + action + "'><input type='hidden' name='" + PREFIX + "form_css_to_use' value='" + css_to_use + "' /><input type='hidden' name='" + PREFIX + "form_preview_post_type' value='" + post_type + "' /><input type='hidden' name='" + PREFIX + "form_preview_form_type' value='" + form_type + "' /><textarea  name='" + PREFIX + "form_preview_content'>" + utils.stripslashes(data) + "</textarea><textarea  name='" + PREFIX + "extra_css_to_use'>" + extra_css + "</textarea><textarea  name='" + PREFIX + "extra_js_to_use'>" + extra_js + "</textarea></form>");
                    // does not work in IE(9), so add it to current doc and submit
                    jQuery(document.body).append(previewForm);
                    previewForm.submit();
                    // remove it after a while
                    setTimeout(function () {
                        previewForm.remove();
                    }, 1000);

                } else {
                    gui.Popups.alert({message: cred_settings.locale.enable_popup_for_preview, class: 'cred-dialog'});
                }

                return false;
            }
            ,
            userFormPreview: function () {
                var data = utils.getContent(jQuery('#content'));

                if ('' == $.trim(data))
                    return false;

                var post_type = _credModel.get('[post][post_type]'), //jQuery('#cred_post_type').val();
                    form_type = _credModel.get('[form][type]'), //jQuery('#cred_form_type').val();
                    css_to_use = _credModel.get('[form][theme]'), //jQuery('input[name="_cred[form][theme]"]:checked').val();
                    extra_css = utils.getContent(jQuery('#cred-extra-css-editor')),
                    extra_js = utils.getContent(jQuery('#cred-extra-js-editor')),
                    previewForm, previewPopup, id, target, action;


                id = jQuery('#post_ID').val();
                target = 'CRED_Preview_' + id;
                action = cred_settings.homeurl + 'index.php?cred_user_form_preview=' + id;

                previewPopup = window.open('', target, "status=0,title=0,height=600,width=800,scrollbars=1,resizable=1");
                if (previewPopup) {
                    post_type = 'user';
                    previewForm = jQuery("<form style='display:none' name='cred_user_form_preview_form' method='post' target='" + target + "' action='" + action + "'><input type='hidden' name='" + PREFIX + "form_css_to_use' value='" + css_to_use + "' /><input type='hidden' name='" + PREFIX + "form_preview_post_type' value='" + post_type + "' /><input type='hidden' name='" + PREFIX + "form_preview_form_type' value='" + form_type + "' /><textarea  name='" + PREFIX + "form_preview_content'>" + utils.stripslashes(data) + "</textarea><textarea  name='" + PREFIX + "extra_css_to_use'>" + extra_css + "</textarea><textarea  name='" + PREFIX + "extra_js_to_use'>" + extra_js + "</textarea></form>");
                    // does not work in IE(9), so add it to current doc and submit
                    jQuery(document.body).append(previewForm);
                    previewForm.submit();
                    // remove it after a while
                    setTimeout(function () {
                        previewForm.remove();
                    }, 1000);

                } else
                    gui.Popups.alert({message: cred_settings.locale.enable_popup_for_preview, class: 'cred-dialog'});

                return false;
            }
			,
			setPopupPosition: function( $button, $popupBox ) {
				
				var isRtl = ( $button.closest( '.trl' ).length > 0 ),
					distanceRight = jQuery( window ).width() - ( $button.offset().left + $button.width() ),
					distanceLeft = $button.offset().left;
				
				if (
					distanceLeft < 450 
					&& distanceRight < 450
				) {
					var leftPosition = 210,
						boxWidth = 450;
					if ( distanceLeft < 220 ) {
						leftPosition = distanceLeft - 10;
					}
					if ( leftPosition + 450  + 10 > jQuery( window ).width() ) {
						boxWidth = jQuery( window ).width() - leftPosition - 10;
					}
					$popupBox.css( {'left':'-' + leftPosition  + 'px', 'right':'auto', 'width': boxWidth + 'px'} );
				} else if ( isRtl ) {
					if ( distanceLeft < 450 ) {
						$popupBox.css( {'left':'0', 'right':'auto', 'width':'450px'} );
					} else {
						$popupBox.css( {'left':'auto', 'right':'0', 'width':'450px'} );
					}
				} else {
					if ( distanceRight < 450 ) {
						$popupBox.css( {'left':'auto', 'right':'0', 'width':'450px'} );
					} else {
						$popupBox.css( {'left':'0', 'right':'auto', 'width':'450px'} );
					}
				}
				
			}
        }
    ;

    // public methods / properties
    var self = {
        // add the extra Modules as part of main CRED Module
        app: utils,
        gui: gui,
        mvc: mvc,
        settings: settings,
        route: function (path, params, raw) {
            return utils.route('cred', cred_settings.ajaxurl, path, params, raw);
        },
        isEmail: function (email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        },
        getFormFields: function ($area) {
            if (!field_data)
                return [];

            var content = utils.getContent($area).replace(/[\n\r]+/g, ' '), // normalize, remove \n
                fields = field_data,
                //generic field must be persist !
                patterns = [
                    // custom fields
                    {
                        rx: /\[cred[\-_]field\b[^\[\]]*field=[\"\']([\d\w\-_]+)[\"\'][^\[\]]*value=[\"\']([\w\-\[\]=\s]+)?[\"\'][^\[\]]*?\]/g,
                        field: 1,
                        type: false,
                        generic: false
                    },
                    // generic fields variation 1
                    {
                        rx: /\[cred\-generic\-field\b([^\[\]]*?)\](.+?)\[\/cred\-generic\-field\]/g,
                        atts: 1,
                        content: 2,
                        generic: true
                    },
                    // generic fields variation 2
                    {
                        rx: /\[cred_generic_field\b([^\[\]]*?)\](.+?)\[\/cred_generic_field\]/g,
                        atts: 1,
                        content: 2,
                        generic: true
                    }

                ],
                returned_fields = [], pat, patl, match, match2, name, field, type, repetitive, generic, persistent,
                field_type_rxp = /field=[\"\']([\d\w\-_]+?)[\"\'][^\[\]]*?\btype=[\"\']([\d\w\-_]+?)[\"\']/,
                type_field_rxp = /type=[\"\']([\d\w\-_]+?)[\"\'][^\[\]]*?\bfield=[\"\']([\d\w\-_]+?)[\"\']/,
                persist_rxp = /["']persist["']\s*\:\s*(\d)/,
                generic_field_type_rxp = /["']generic_type["']\s*\:\s*["']([\w_]+)["']/
            ;

            //compatibility for cred user forms
            if (!fields.post_fields && fields.user_fields) {
                fields.post_fields = fields.user_fields;
            }

            // parse content
            for (pat = 0, patl = patterns.length; pat < patl; pat++) {
                if (patterns[pat].generic) {
                    while (match = patterns[pat].rx.exec(content)) {
                        field = false;
                        type = false;
                        name = field;
                        repetitive = false;
                        generic = true;
                        persistent = false;

                        var
                            match_field_type = (patterns[pat].atts && match[patterns[pat].atts]) ? field_type_rxp.exec(match[patterns[pat].atts]) : false,
                            match_type_field = (patterns[pat].atts && match[patterns[pat].atts]) ? type_field_rxp.exec(match[patterns[pat].atts]) : false,
                            match_persist = (patterns[pat].content && match[patterns[pat].content]) ? persist_rxp.exec(match[patterns[pat].content]) : false,
                            match_generic_field_type = (patterns[pat].content && match[patterns[pat].content]) ? generic_field_type_rxp.exec(match[patterns[pat].content]) : false
                        ;

                        //console.log(match);
                        //console.log(match[patterns[pat].atts]);
                        //console.log(match[patterns[pat].content]);

                        if (match_field_type) {
                            field = match_field_type[1];
                            type = match_field_type[2];
                            name = field;
                            //console.log(match_field_type);
                        } else if (match_type_field) {
                            type = match_type_field[1];
                            field = match_type_field[2];
                            name = field;
                            //console.log(match_type_field);
                        } else {
                            //console.log('continue');
                            continue;
                        }

                        if (match_persist && match_persist[1] && '1' == match_persist[1]) {
                            persistent = true;
                        } else {
                            persistent = false;
                        }

                        if (match_generic_field_type && match_generic_field_type[1] && '' != match_generic_field_type[1]) {
                            type = match_generic_field_type[1];
                        }

                        //console.log(match_persist);

                        returned_fields.push({
                            name: field,
                            field: field,
                            type: type,
                            repetitive: repetitive,
                            persistent: persistent,
                            generic: generic
                        });
                    }
                } else {
                    while (match = patterns[pat].rx.exec(content)) {
                        field = false;
                        type = false;
                        name = field;
                        repetitive = false;
                        generic = false;
                        persistent = true;

                        field = (false !== patterns[pat].field && match[patterns[pat].field]) ? match[patterns[pat].field] : false;
                        type = (false !== patterns[pat].type && match[patterns[pat].type]) ? match[patterns[pat].type] : false;
                        name = field;
                        repetitive = false;
                        generic = false;
                        persistent = true;

                        if (field) {
                            if (fields.post_fields[field]) {
                                type = fields.post_fields[field]['type'];
                                if (
                                    fields.post_fields[field]['data'] &&
                                    fields.post_fields[field]['data']['repetitive'] &&
                                    fields.post_fields[field]['data']['repetitive'] == '1'
                                )
                                    repetitive = true;
                                if (fields.post_fields[field]['plugin_type_prefix'])
                                    field = fields.post_fields[field]['plugin_type_prefix'] + name;
                                returned_fields.push({
                                    name: name,
                                    field: field,
                                    type: type,
                                    repetitive: repetitive,
                                    persistent: persistent,
                                    generic: generic
                                });
                            } else if (fields.custom_fields[field]) {
                                type = fields.custom_fields[field]['type'];
                                if (
                                    fields.custom_fields[field]['data'] &&
                                    fields.custom_fields[field]['data']['repetitive'] &&
                                    fields.custom_fields[field]['data']['repetitive'] == '1'
                                )
                                    repetitive = true;
                                if (fields.custom_fields[field]['plugin_type_prefix'])
                                    field = fields.custom_fields[field]['plugin_type_prefix'] + name;
                                returned_fields.push({
                                    name: name,
                                    field: field,
                                    type: type,
                                    repetitive: repetitive,
                                    persistent: persistent,
                                    generic: generic
                                });
                            } else if (fields.parents[field]) {
                                type = fields.parents[field]['type'];
                                if (
                                    fields.parents[field]['data'] &&
                                    fields.parents[field]['data']['repetitive'] &&
                                    fields.parents[field]['data']['repetitive'] == '1'
                                )
                                    repetitive = true;
                                if (fields.parents[field]['plugin_type_prefix'])
                                    field = fields.parents[field]['plugin_type_prefix'] + name;
                                returned_fields.push({
                                    name: name,
                                    field: field,
                                    type: type,
                                    repetitive: repetitive,
                                    persistent: persistent,
                                    generic: generic
                                });
                            } else if (fields.taxonomies[field]) {
                                type = 'taxonomy';
                                if (fields.taxonomies[field]['plugin_type_prefix'])
                                    field = fields.taxonomies[field]['plugin_type_prefix'] + name;
                                returned_fields.push({
                                    name: name,
                                    field: field,
                                    type: type,
                                    repetitive: repetitive,
                                    persistent: persistent,
                                    generic: generic
                                });
                            }
                        }
                    }
                }
            }

            return returned_fields;
        },
        doCheck: function (step) {
            var is_form_settings = ((!step || (step && step == 2)) && jQuery('input[name="_cred[form][type]"]').is(':visible'));
            var type_form = aux.getFormType();
            if (is_form_settings && !type_form) {
                gui.Popups.alert({message: cred_settings.locale.invalid_form_type, class: 'cred-dialog'});
                return false;
            }

            var user_role = aux.getUserRole();
            var is_user_form = ((!step || (step && step == 2)) && aux.isUserForm());
            if (is_user_form && type_form == 'new' && (user_role == null || user_role == '')) {
                gui.Popups.alert({message: cred_settings.locale.invalid_user_role, class: 'cred-dialog'});
                return false;
            }

            // title check
            var title = jQuery('#title').val();
            if (/[\#\@\[\]\'\"\!\/\\]/g.test(title) || title.length <= 0) {
                gui.Popups.alert({message: cred_settings.locale.invalid_title, class: 'cred-dialog'});
                return false;
            }

            //validate notifications emails
            var email_fields = jQuery('.notification-sender-email');
            for (var field in email_fields) {
                if (isNaN(field))
                    break;
                if (jQuery(email_fields[field]).val() != "" && !this.isEmail(jQuery(email_fields[field]).val())) {
                    gui.Popups.alert({
                        message: cred_settings.locale.invalid_notification_sender_email,
                        class: 'cred-dialog',
                        callback: function () {
                            jQuery(email_fields[field]).closest('.cred_notification_settings_panel').show();
                            jQuery(email_fields[field]).focus();
                        }
                    });
                    return false;
                }
            }

            return true;
        },
        getContents: function () {
            return {
                'content': utils.getContent(jQuery('#content')),
                'cred-extra-css-editor': utils.getContent(jQuery('#cred-extra-css-editor')),
                'cred-extra-js-editor': utils.getContent(jQuery('#cred-extra-js-editor'))
            };
        },
        getFieldData: function () {
            return field_data;
        },
        getCodeMirror: function () {
            return CodeMirrorEditors['content'];
        },
        getCSSCodeMirror: function () {
            return CodeMirrorEditors['cred-extra-css-editor'];
        },
        getJSCodeMirror: function () {
            return CodeMirrorEditors['cred-extra-js-editor'];
        },
        getModel: function () {
            return _credModel
        },
        getView: function () {
            return _credView
        },
        forms: function () {
            var doinit = true,
                firstLoad = true,
                $_post = jQuery('#post'),
                formtypediv = jQuery('#credformtypediv'),
                posttypediv = jQuery('#credposttypediv'),
                contentdiv = jQuery('#credformcontentdiv'),
                accessmessdiv = jQuery('#accessmessagesdiv'),
                postdivrich = jQuery('#postdivrich'),
                extracssdiv = jQuery('#credextracssdiv'),
                extrajsdiv = jQuery('#credextrajsdiv'),
                messagesdiv = jQuery('#credmessagesdiv'),
                notificationdiv = jQuery('#crednotificationdiv');


            // init model with current form data (one model for all data)
            _credModel = new mvc.Model('_cred', window._credFormData);
            // can use multiple views per same model
            _credView = new mvc.View('cred', _credModel, {
                init: function () {

                    aux.checkCredFormType();
                    jQuery('input[name="_cred[form][type]"]').bind('change', function () {
                        aux.checkCredFormType();
                    });

                    var view = this,
                        model = this._model;

                    // assume View is valid initially
                    view.isValid = true;
					
					aux.enableEditorCodemirror();

                    view.action('styleForm', function ($el, data) {
                            var codemirror = utils.isCodeMirror(jQuery('#content'));
                            if (codemirror) {
                                codemirror.focus();
                                codemirror.execCommand('selectAll');
                                var content = utils.getContent(jQuery('#content')).replace(/[\n\r]+/g, '%%NL%%'); // normalize, remove \n
                                // remove class "cred-keep-original", if exists
                                content = content.replace(/\[credform([^\]]*) class="(.*?)[\s]*?cred-keep-original(.*?)"([^\]]*)\]/gi, '[credform$1 class="$2$3"$4]');

                                if ('minimal' == $el.val()) {
                                    // add class attribute if not exists
                                    content = content.replace(/\[credform(((?!class="(.*?)")[^\]])*)\]/gi, '[credform$1 class=""]');
                                    // add class "cred-keep-original"
                                    content = content.replace(/\[credform([^\]]*) class="(.*?)"([^\]]*)\]/gi, '[credform$1 class="%%SPLIT%%$2 cred-keep-original%%SPLIT%%"$3]').split('%%SPLIT%%');
                                    content[1] = $.trim(content[1]);
                                    content = content.join('');
                                }

                                utils.InsertAtCursor(jQuery('#content'), content.replace(/%%NL%%/g, NL));

                            }
                        })
                        .action('refreshFormFields', function ($el, data) {
                            refreshFromFormFields();
                            gui.Popups.flash({
                                message: cred_settings.locale.refresh_done,
                                class: 'cred-dialog'
                            });
                        })
                        .action('validateSection', function ($el, data) {
                            if ($el[0] && data && undefined !== data.validationResult)
                                $el[0].__isCredValid = data.validationResult;
                        })
                        .action('fadeSlide', function ($el, data) {
                            if (!data.bind)
                                return;
                            data = data.bind;
                            if (data['domRef'])
                                $el = jQuery(data['domRef']);

                            if (data['condition']) {
                                data['condition'] = model.eval(data['condition']);
                                if (undefined !== data['condition']) {
                                    (data['condition'])
                                        ? $el.slideFadeDown('slow')
                                        : $el.slideFadeUp('slow');
                                }
                            } else
                                $el.slideFadeDown('slow', 'quintEaseOut');
                        })
                        .action('fadeIn', function ($el, data) {
                            if (!data.bind)
                                return;
                            data = data.bind;
                            if (data['domRef'])
                                $el = jQuery(data['domRef']);

                            if (data['condition']) {
                                data['condition'] = model.eval(data['condition']);
                                if (undefined !== data['condition'])
                                    (data['condition'])
                                        ? $el.stop().fadeIn('slow')
                                        : $el.stop().fadeOut('slow', function () {
                                        jQuery(this).hide();
                                    });
                            } else
                                $el.stop().fadeIn('slow');
                        })
                        // custom confirm box
                        .func('confirm', function (msg, callback) {
                            gui.Popups.confirm({
                                message: msg,
                                class: 'cred-dialog',
                                buttons: [cred_settings.locale.Yes, cred_settings.locale.No],
                                primary: cred_settings.locale.Yes,
                                callback: function (button) {
                                    if ($.isFunction(callback)) {
                                        if (button == cred_settings.locale.Yes)
                                            callback.call(view, true);
                                        else
                                            callback.call(view, false);
                                    }
                                }
                            });
                        })
                        // add another hook when model changes
                        .event('model:change', function (e, data) {

                            if ('[post][post_type]' == data.key) {
                                aux.getLoader().show();
                                jQuery('#cred_autogenerate_username_scaffold').prop("disabled", "disabled");
                                jQuery('#cred_autogenerate_nickname_scaffold').prop("disabled", "disabled");
                                jQuery('#cred_autogenerate_password_scaffold').prop("disabled", "disabled");

                                if (cred_settings._current_page == 'cred-user-form') {
                                    function start_role_call() {
                                        setTimeout(function () {
                                            var role = aux.getUserRole();
                                            if (role == null) {
                                                return;
                                            }
                                            if (role !== "") {
                                                aux.updateAutogenerateFields();
                                                aux.reloadUserFields();
                                            } else {
                                                start_role_call();
                                            }
                                        }, 3000);
                                    }

                                    start_role_call();
                                } else {
                                    $.ajax({
                                        url: self.route('/Forms/getPostFields'),
                                        timeout: 10000,
                                        type: 'POST',
                                        data: 'post_type=' + data.value + '&_wpnonce=' + cred_settings._cred_wpnonce,
                                        dataType: 'json',
                                        success: function (resp) {
                                            // load and dispatch event of fields loaded
                                            aux.onLoad(resp);
                                            utils.dispatch('cred.fieldsLoaded');
                                            aux.getLoader().hide();
                                        }
                                    });
                                }
                            } else if (/^\[notification\]\[notifications\]\[\d+\]/.test(data.key)) {
                                var match = /^\[notification\]\[notifications\]\[(\d+)\]$/.exec(data.key);
                                if (match) {
                                    // activate tinyMCE for notification body
                                    if (window.tinyMCEPreInit) {
                                        var newId = 'credmailbody' + match[1];

                                        if (typeof (window.tinyMCE) == 'object' && window.tinyMCEPreInit.mceInit['credmailbody__i__']) {
                                            // notification is removed
                                            if (window.tinyMCEPreInit.mceInit[newId]) {
                                                try {
                                                    if (window.tinyMCE.get(newId)) {
                                                        window.tinyMCE.execCommand('mceFocus', false, newId);
                                                        window.tinyMCE.execCommand('mceRemoveControl', false, newId);
                                                    }
                                                    delete window.tinyMCEPreInit.mceInit[newId];
                                                } catch (e) {
                                                }
                                            } else {
                                                window.tinyMCEPreInit.mceInit[newId] = window.tinyMCE.extend({}, window.tinyMCEPreInit.mceInit['credmailbody__i__']);

                                                setTimeout(function(){
                                                    quicktags(tinyMCEPreInit.qtInit[newId]);
                                                    jQuery("#" + newId + "-tmce").click();
                                                    jQuery('#qt_'+ newId +'_toolbar').hide();
                                                }, 300);

                                                for (var att in {'body_class': 1, 'elements': 1}) {
                                                    if ( window.tinyMCEPreInit.mceInit[newId][att] ){
                                                        window.tinyMCEPreInit.mceInit[newId][att] = window.tinyMCEPreInit.mceInit[newId][att].replace(/__i__/g, match[1]);
                                                    }
                                                }

                                                // init tinyMCE on new dynamic area
                                                try {
                                                    // prevents border right of tinyMCE editor to vanish and break editor's look
                                                    window.tinyMCEPreInit.mceInit[newId].width = '99.9%';
                                                    var ed = new tinymce.Editor(newId, window.tinyMCEPreInit.mceInit[newId], tinymce.EditorManager);




                                                    ed.on('mousedown', function (e) {
                                                        if (this.id){
                                                            window.wpActiveEditor = window.wpcfActiveEditor = this.id.slice(3, -5);
                                                        }

                                                    });
                                                    ed.render();
                                                } catch (e) {
                                                }
                                            }
                                        }
                                        if (window.tinyMCEPreInit.qtInit && window.tinyMCEPreInit.qtInit['credmailbody__i__']) {
                                            // notification is removed
                                            if (window.tinyMCEPreInit.qtInit[newId]) {
                                                try {
                                                    delete window.tinyMCEPreInit.qtInit[newId];
                                                } catch (e) {
                                                }
                                            } else {
                                                window.tinyMCEPreInit.qtInit[newId] = $.extend({}, window.tinyMCEPreInit.qtInit['credmailbody__i__']);
                                                for (var att in {'id': 1}) {
                                                    if (window.tinyMCEPreInit.qtInit[newId][att])
                                                        window.tinyMCEPreInit.qtInit[newId][att] = window.tinyMCEPreInit.qtInit[newId][att].replace(/__i__/g, match[1]);
                                                }

                                                if (typeof (window.tinyMCE) != 'object') {
                                                    var el = window.tinyMCEPreInit.qtInit[newId].id;
                                                    document.getElementById('wp-' + el + '-wrap').onmousedown = function () {
                                                        window.wpActiveEditor = window.wpcfActiveEditor = this.id.slice(3, -5);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    // enable/disable some placeholders
                                    var match = /^\[notification\]\[notifications\]\[(\d+)\]\[event\]\[type\]$/.exec(data.key);
                                    if (match) {
                                        var placeholders = view.getElements('#cred_mail_subject_placeholders-' + match[1])
                                            .find('a.cred_field_add_code')
                                            .add(
                                                view.getElements('#cred_mail_body_placeholders-' + match[1])
                                                    .find('a.cred_field_add_code')
                                            );
                                        if ('form_submit' == data.value) {
                                            placeholders.filter(function () {
                                                var val = jQuery(this).data('value');
                                                if ($.inArray(val, ['%%FORM_DATA%%', '%%POST_PARENT_TITLE%%', '%%POST_PARENT_LINK%%']) > -1)
                                                    return true;
                                                return false;
                                            }).prop('disabled', false).show();
                                        } else {
                                            placeholders.filter(function () {
                                                var val = jQuery(this).data('value');
                                                if ($.inArray(val, ['%%FORM_DATA%%', '%%POST_PARENT_TITLE%%', '%%POST_PARENT_LINK%%']) > -1)
                                                    return true;
                                                return false;
                                            }).prop('disabled', true).hide();
                                        }
                                        utils.dispatch('cred.notificationEventChanged', data.value, placeholders)
                                    }
                                }
                            }
                            // display validation messages per section
                            validateView();
                        })
                        // add another hook when view changes
                        .event('view:change', function (e, data) {
                            if (data.el && data.bind) {
                                if (data.el.hasClass('cred-notification-add-button')) {
                                }
                            }

                            // display validation messages per section
                            validateView();
                        });

                    function validateView() {
                        // display validation messages per section
                        view.isValid = true;
                        // use caching here
                        view.getElements('.cred_validation_section').each(function () {
                            var $this = jQuery(this);
                            isValid = true;

                            $this.find('input, select, textarea').each(function () {
                                var $this2 = jQuery(this);
                                if (undefined !== $this2[0].__isCredValid) {
                                    if (!$this2[0].__isCredValid)
                                        isValid = false;
                                }
                            });

                            $this.find('input').each(function () {
                                if (jQuery(this).val() == 'author' && jQuery(this).is(':checked')) {
                                    isValid = true;
                                }
                            });

                            if (!isValid) {
                                view.isValid = false;
                                //Attention: added delay action for preventing loss of click after change event processing (case of notifications alert messages)
                                jQuery('.cred-notification.cred-error.cred-section-validation-message', $this).delay(100).show(0);
                            } else {
                                //Attention: added delay action for preventing loss of click after change event processing (case of notifications alert messages)
                                jQuery('.cred-notification.cred-error.cred-section-validation-message', $this).delay(100).hide(0);
                            }
                        });
                    }

                    //Load fields when adding new notification
                    jQuery('#cred-notification-add-button').on('click', function() {
                        refreshFromFormFields();
                    });
                    //Load fields when notifications are loaded
                    jQuery('.cred-notification-edit').on('click', function() {
                        refreshFromFormFields();
                    });

                    // setup some handlers
                    function refreshFromFormFields() {
                        if (!(field_data && (field_data.post_fields || field_data.user_fields)))
                            return;

                        var fields = self.getFormFields();

                        var _persistent_mail_fields = [],
                            _persistent_user_id_fields = [],
                            _persistent_text_fields = [],
                            _persistent_checkbox_fields = [],
                            _persistent_select_fields = [],
                            _all_persistent_meta_fields = [];

                        for (var i = 0, l = fields.length; i < l; i++) {
                            if (fields[i].persistent) {
                                _all_persistent_meta_fields.push({value: fields[i].field, label: fields[i].name});
                                if (fields[i].type == 'mail' || fields[i].type == 'email')
                                    _persistent_mail_fields.push({value: fields[i].field, label: fields[i].name});

                                if (fields[i].type == 'user_id')
                                    _persistent_user_id_fields.push({value: fields[i].field, label: fields[i].name});

                                if (
                                    (-1 == $.inArray(fields[i].field, ['post_title', 'post_content', 'post_excerpt'])) &&
                                    (
                                        fields[i].type == 'text'
                                        || fields[i].type == 'textfield'
                                        || fields[i].type == 'numeric'
                                        || fields[i].type == 'integer'
                                    )
                                )
                                    _persistent_text_fields.push({value: fields[i].field, label: fields[i].name});

                                if (
                                    (fields[i].type == 'select' || fields[i].type == 'radio')
                                )
                                    _persistent_select_fields.push({value: fields[i].field, label: fields[i].name});

                                if (
                                    (fields[i].type == 'checkboxes' || fields[i].type == 'checkbox')
                                )
                                    _persistent_checkbox_fields.push({value: fields[i].field, label: fields[i].name});
                            }
                        }

                        //console.log('UpdatedFormFields');
                        //console.log(fields);

                        // update model
                        model
                            .set('[_persistent_mail_fields]', _persistent_mail_fields)
                            .set('[_persistent_user_id_fields]', _persistent_user_id_fields)
                            .set('[_persistent_text_fields]', _persistent_text_fields)
                            .set('[_persistent_select_fields]', _persistent_select_fields)
                            .set('[_all_persistent_meta_fields]', [].concat(_all_persistent_meta_fields)).trigger('change');
                    }

                    // add custom events callbacks
                    utils.attach('cred.fieldsLoaded cred.insertField cred.insertScaffold', refreshFromFormFields);
                    utils.attach('cred.wizardFinished', function () {
                        // lets refresh the view
                        //view.trigger('change');
                        // let's force resize of CodeMirrors
                        for (codemirror in CodeMirrorEditors) {
                            if (codemirror == 'content') {
                                var $_metabox = jQuery('#' + codemirror).closest('.postbox');
                                if (!$_metabox.hasClass('closed') && 'none' != $_metabox.css('display'))
                                    CodeMirrorEditors[codemirror].setSize(jQuery('#' + codemirror).width() + 18, jQuery('#' + codemirror).height());
                            }
                        }

                        // trigger scroll event to fix toolbar buttons being shown in wrong spot on chrome.
                        var _scroll_y = jQuery(window).scrollTop();
                        if (_scroll_y === 0) {
                            jQuery("html, body").animate({scrollTop: 1});
                        } else {
                            jQuery("html, body").animate({scrollTop: 0});
                        }

                        jQuery('#credformcontentdiv').removeClass('closed');

                        aux.checkCredFormType();

                    });

                    // handle tooltips with pointers
                    $_post
                        .on('click', '.js-cred-tip-link > i', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            var $this = jQuery(this), $el = $this.parent();

                            if ($this.hasClass('active')) {
                                $this[0]._pointer && $this[0]._pointer.pointer('close');
                                return;
                            }

                            $this.addClass('active');
                            // GUI framework handles pointers now
                            $this[0]._pointer = gui.Popups.pointer($el, {
                                message: jQuery($el.data('pointer-content')).html(),
                                class: 'cred-pointer',
                                callback: function () {
                                    //$this[0]._pointer=null;
                                    $this.removeClass('active');
                                }
                            });
                        })
                        .on('click', '.cred_field_add_code', function (e) {
                            e.stopPropagation();
                            e.preventDefault();
                            var $el = jQuery(this), $to = jQuery($el.data('area')), v = $el.data('value');
                            utils.InsertAtCursor($to, v);
                            $el.closest('.cred-popup-box').__hide();
                            $el.closest('.cred-icon-button').css('z-index', 1);
                            return false;
                        })
                        .on('paste keyup change', '.js-test-notification-to', function (e) {
                            //e.preventDefault();
                            var $el = jQuery(this), val = $el.val(), $but = jQuery($el.data('sendbutton'));

                            if ('' == val) {
                                //$but.prop('disabled', true);
                                $but.attr('disabled', 'disabled');
                            } else {
                                //$but.prop('disabled', false);
                                $but.removeAttr('disabled');
                            }
                        })
                        .on('click', '.js-send-test-notification', function (e) {

                            e.preventDefault();

                            var $el = jQuery(this),
                                xhr = null, notification = {}, data,
                                to = $.trim(jQuery($el.data('addressfield')).val()),
                                cancel = jQuery($el.data('cancelbutton')),
                                loader = jQuery($el.data('loader')),
                                resultsCont = jQuery($el.data('results')).empty().hide(),
                                form_id = jQuery('#post_ID').val(), fromCancel = false
                            ;

                            // nowhere to send
                            if ('' == to)
                                return false;

                            var doFinish = function () {
                                if (xhr) {
                                    xhr.abort();
                                    xhr = false;
                                }
                                cancel.unbind('click', doFinish);
                                $el.removeAttr('disabled');
                            };

                            var editor_id = 'credmailbody' + $el.data('notification');
                            if (typeof (window.tinyMCE) == 'object' && window.tinyMCEPreInit.mceInit[editor_id]) {
                                var editor = window.tinyMCE.get(editor_id);
                                if (editor) {
                                    var content = editor.getContent();
                                    model.set('[notification][notifications][' + $el.data('notification') + '][mail][body]', content, true);
                                }
                            }

                            notification = $.extend(notification, model.get('[notification][notifications][' + $el.data('notification') + ']'));
                            delete notification['event'];
                            notification['to']['type'] = ['specific_mail'];
                            notification['to']['specific_mail']['address'] = to;
                            //console.log(notification);
                            data = {
                                'cred_test_notification_data': notification,
                                'cred_form_id': form_id
                            };

                            // send it
                            //console.log('sending..');
                            cancel.unbind('click', doFinish).bind('click', doFinish);
                            $el.attr('disabled', 'disabled');
                            resultsCont.html('sending test notification to &quot;' + to + '&quot; ..').show();
                            loader.show();

                            xhr = $.ajax(self.route('/Forms/testNotification'), {
                                data: data,
                                dataType: 'json',
                                type: 'POST',
                                success: function (result) {
                                    if (result.error) {
                                        resultsCont.html('<div class="cred-error">' + result.error + '</div>');
                                    } else {
                                        resultsCont.html('<div class="cred-success">' + result.success + '</div>');
                                    }
                                    resultsCont.hide().fadeIn('slow');
                                    loader.hide();
                                    xhr = false;
                                    doFinish();
                                    //console.log(result);
                                },
                                error: function (xhr1) {
                                    loader.hide();
                                    resultsCont.empty().hide();

                                    gui.Popups.alert({
                                        message: 'AJAX Request failed!<br /><br />Response Code: ' + xhr1.status + '<br /><br />Response Message: ' + xhr1.responseText,
                                        class: 'cred-dialog'
                                    });

                                    xhr = false;
                                    doFinish();
                                }
                            });
                        });

                    // handle Preview button
                    jQuery('#cred-preview-button a').unbind('click').bind('click', function (event) {
                        event.preventDefault();
                        return aux.formPreview();
                    });

                    //handle new preview button

                    jQuery('.cred-preview-button').unbind('click').bind('click', function (event) {
                        event.preventDefault();
                        return aux.formPreview();
                    });

                    /**
                     * _do_submit_compatibility
                     *
                     * Compatibility solutions before submitting the CRED edit page form.
                     *
                     * @since 1.8.1
                     */

                    var _do_submit_compatibility = function () {

                        /**
                         * ACF compatibility
                         *
                         * ACF hijacks the post form submit, performs some validation, and then it should submit the form, but it does not
                         * Because of that, we need to disable the ACF validation on CRED forms edit pages.
                         *
                         * @since 1.8.1
                         */

                        acf = (typeof acf != 'undefined') ? acf : {};
                        acf.validation = acf.validation || {};
                        acf.validation.active = 0;

                    };


                    var _do_submit = function () {
                        var form_name_1 = jQuery('#title').val();
                        if ($.trim(form_name_1) == '') {
                            gui.Popups.alert({message: cred_settings.locale.set_form_title, class: 'cred-dialog'});
                            return false;
                        }

                        if (jQuery('input[name="_cred[form][type]"]') && jQuery('input[name="_cred[form][type]"]').length > 0) {
                            var form_form_type = aux.getFormType();
                            if ($.trim(form_form_type) == '') {
                                gui.Popups.alert({
                                    message: cred_settings.locale.post_type_missing,
                                    class: 'cred-dialog'
                                });
                                return false;
                            }
                        }

                        var is_user_form = aux.isUserForm();
                        var type_user_form = aux.getFormType();
                        var cred_form_user_role = aux.getUserRole();
                        if (is_user_form &&
                            type_user_form == 'new' &&
                            (cred_form_user_role == null ||
                            cred_form_user_role == '')) {
                            gui.Popups.alert({message: cred_settings.locale.invalid_user_role, class: 'cred-dialog'});
                            return false;
                        }

                        if (jQuery('#cred_post_type') && jQuery('#cred_post_type').length > 0) {
                            var form_post_type = jQuery('#cred_post_type').val();
                            if ($.trim(form_post_type) == '') {
                                gui.Popups.alert({
                                    message: cred_settings.locale.post_type_missing,
                                    class: 'cred-dialog'
                                });
                                return false;
                            }
                        }

                        if (jQuery('#cred_post_status') && jQuery('#cred_post_status').length > 0) {
                            var form_post_status = jQuery('#cred_post_status').val();
                            if ($.trim(form_post_status) == '') {
                                gui.Popups.alert({
                                    message: cred_settings.locale.post_status_missing,
                                    class: 'cred-dialog'
                                });
                                return false;
                            }
                        }

                        if (jQuery('#cred_form_success_action') && jQuery('#cred_form_success_action').length > 0) {
                            var form_post_action = jQuery('#cred_form_success_action').val();
                            if ($.trim(form_post_action) == '') {
                                gui.Popups.alert({
                                    message: cred_settings.locale.post_action_missing,
                                    class: 'cred-dialog'
                                });
                                return false;
                            }
                        }

                        if (self.doCheck()) {
                            var nbox = jQuery('#crednotificationdiv');

                            // notification metabox is closed
                            if (nbox.hasClass('closed')) {
                                // open it and save it
                                //nbox.children('.handlediv').trigger('click');
                                nbox.removeClass('closed');

                                // make view re-validate
                                view.forceValidation();
                                validateView();

                                //console.log(view.isValid);
                                if (view.isValid) {
                                    // close it and save it
                                    //nbox.children('.handlediv').trigger('click');
                                    nbox.addClass('closed');
                                }

                                //return false;
                            }

                            if (!view.isValid)
                                $_post.append('<input style="display:none" type="hidden" id="_cred_form_not_valid" name="_cred[validation][fail]" value="1" />');
                            else
                                jQuery('#_cred_form_not_valid').remove();
                            return true;
                        }
                        return false;
                    };

                    $_post.bind('submit', function (event) {
                        _do_submit_compatibility();
                        return _do_submit();
                    });

                    var $cred_shortcodes_box = jQuery('#cred-shortcodes-box');
                    // setup other interaction handlers and  initialize fields show/hide according to values
                    $cred_shortcodes_box.on('click', 'a.cred-fields-group-heading', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        var thisinside = jQuery(this).next();
                        jQuery('#cred-shortcodes-box-inner .cred-accordeon-item').removeClass('cred-accordeon-item-active') // remove active class from
                        jQuery(this).closest('.cred-accordeon-item').addClass('cred-accordeon-item-active') // add .active class to parent .cred-fields-group-heading;
                        jQuery('#cred-shortcodes-box-inner .cred-accordeon-item-inside').not(thisinside).stop(false).slideUp('fast');
                        thisinside.stop(false).slideDown({duration: 'slow'});
                    });

                    $cred_shortcodes_box.on('click', 'a.cred_field_add', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        var el = jQuery(this);
                        var data = el.data('field');
                        var shortcode;
                        // remove all popups
                        $_post.find('.additional_field_options_popup').remove();
                        if (data.slug == 'credform' || data.slug == 'creduserform') {
                            var prefix = data.slug == 'creduserform' ? 'cred-user-form' : 'cred-form';
                            //shortcode='['+data.slug+']\n[/'+data.slug+']';
                            if ('minimal' == _credModel.get('[form][theme]')) // bypass script and other styles added to form, minimal
                                shortcode = "[" + data.slug + " class='" + prefix + " cred-keep-original']\n[/" + data.slug + "]";
                            else
                                shortcode = "[" + data.slug + " class='" + prefix + "']\n[/" + data.slug + "]";
                        }

                        //hide of additional maxwidth maxheight of image type field
                        else if (data['type'] == '__image') {
                            // load template
                            jQuery(jQuery('#cred_image_dimensions_validation_template').html()).appendTo('#cred-shortcodes-box');
                            jQuery('#cred_image_dimensions_validation').__show();
                            jQuery('#cred_image_dimensions_cancel_button').unbind('click').click(function (event) {
                                event.stopPropagation();
                                event.preventDefault();
                                setTimeout(function () {
                                    jQuery('#cred_image_dimensions_validation').__hide();
                                }, 50);
                            });
                            jQuery('#cred_image_dimensions_validation_button').unbind('click').click(function (event) {
                                event.stopPropagation();
                                event.preventDefault();

                                var max_width = parseInt($.trim(jQuery('#cred_max_width').val()), 10);
                                var max_height = parseInt($.trim(jQuery('#cred_max_height').val()), 10);
                                shortcode = aux.shortcode(data, {max_width: max_width, max_height: max_height});
                                utils.InsertAtCursor(jQuery('#content'), shortcode);
                                utils.dispatch('cred.insertField');
                                setTimeout(function () {
                                    jQuery('#cred-shortcodes-box').__hide();
                                    jQuery('#cred-shortcode-button').css('z-index', 1);
                                }, 50);

                            });
                            return false;
                        }
                        // fields can have, placeholder, readonly and escape attributes
                        else if (data['type'] == 'textfield' ||
                            data['type'] == 'textarea' ||
                            data['type'] == 'wysiwyg' ||
                            data['type'] == 'date' ||
                            data['type'] == 'url' ||
                            data['type'] == 'phone' ||
                            data['type'] == 'numeric' ||
                            data['type'] == 'email') {
                            // load template
                            jQuery(jQuery('#cred_text_extra_options_template').html()).appendTo('#cred-shortcodes-box');
                            jQuery('#cred_text_extra_options').__show();
                            jQuery('#cred_text_extra_options_cancel_button').unbind('click').click(function (event) {
                                event.stopPropagation();
                                event.preventDefault();
                                setTimeout(function () {
                                    jQuery('#cred_text_extra_options').__hide();
                                }, 50);
                            });
                            jQuery('#cred_text_extra_options_button').unbind('click').click(function (event) {
                                event.stopPropagation();
                                event.preventDefault();

                                var placeholder = $.trim(jQuery('#cred_text_extra_placeholder').val());
                                var readonly = jQuery('#cred_text_extra_readonly').is(':checked');
                                var escape = false; //jQuery('#cred_text_extra_escape').is(':checked');
                                shortcode = aux.shortcode(data, {
                                    placeholder: placeholder,
                                    readonly: readonly,
                                    escape: escape
                                });
                                utils.InsertAtCursor(jQuery('#content'), shortcode);
                                utils.dispatch('cred.insertField');
                                setTimeout(function () {
                                    jQuery('#cred-shortcodes-box').__hide();
                                    jQuery('#cred-shortcode-button').css('z-index', 1);
                                }, 50);

                            });
                            return false;
                        } else if (data.is_parent) {
                            // load template
                            jQuery(jQuery('#cred_parent_field_settings_template').html()).appendTo('#cred-shortcodes-box');
                            jQuery('#cred_parent_field_settings #cred_parent_required').unbind('change').bind('change', function () {
                                if (jQuery(this).is(':checked')) {
                                    jQuery('#cred_parent_field_settings #cred_parent_select_text_container').stop(true).slideFadeDown('fast');
                                } else {
                                    jQuery('#cred_parent_field_settings #cred_parent_select_text_container').stop(true).slideFadeUp('fast');
                                }
                            });

                            // set default values
                            jQuery('#cred_parent_select_text').val(cred_settings.locale.default_select_text);
                            jQuery('#cred_parent_validation_text').val(data.data.post_type + ' must be selected');

                            setTimeout(function () {
                                jQuery('#cred_parent_field_settings #cred_parent_required').trigger('change');
                            }, 50);

                            jQuery('#cred_parent_field_settings').__show();
                            jQuery('#cred_parent_extra_cancel_button').unbind('click').click(function (event) {
                                event.stopPropagation();
                                event.preventDefault();
                                setTimeout(function () {
                                    jQuery('#cred_parent_field_settings').__hide();
                                }, 50);
                            });
                            jQuery('#cred_parent_extra_button').unbind('click').click(function (event) {
                                event.stopPropagation();
                                event.preventDefault();

                                var parent_order = jQuery('#cred_parent_order_by').val();
                                var parent_ordering = jQuery('#cred_parent_ordering').val();
                                var parent_max_results = parseInt($.trim(jQuery('#cred_parent_max_results').val()), 10);
                                var required = jQuery('#cred_parent_required').is(':checked');
                                var select_parent_text = jQuery('#cred_parent_select_text').val();
                                var validate_parent_text = jQuery('#cred_parent_validation_text').val();
                                shortcode = aux.shortcode(data, {
                                    parent_order: parent_order,
                                    parent_ordering: parent_ordering,
                                    parent_max_results: parent_max_results,
                                    required: required,
                                    select_parent_text: select_parent_text,
                                    validate_parent_text: validate_parent_text
                                });
                                utils.InsertAtCursor(jQuery('#content'), shortcode);
                                utils.dispatch('cred.insertField');
                                setTimeout(function () {
                                    jQuery('#cred-shortcodes-box').__hide();
                                    jQuery('#cred-shortcode-button').css('z-index', 1);
                                }, 50);

                            });
                            return false;
                        } else
                            shortcode = aux.shortcode(data);
                        utils.InsertAtCursor(jQuery('#content'), shortcode);
                        utils.dispatch('cred.insertField');
                        setTimeout(function () {
                            jQuery('#cred-shortcodes-box').__hide();
                            jQuery('#cred-shortcode-button').css('z-index', 1);
                        }, 50);
                    });

                    //If i click on close i set original situation on checkboxes
                    jQuery('#cred-scaffold-box').on('click', '#cred-popup-cancel', function (e) {
                        aux.updateAutogenerateScaffolds();
                    });

                    jQuery('#cred-scaffold-box').on('click', '#cred-popup-cancel2', function (e) {
                        aux.updateAutogenerateScaffolds();
                    });

                    jQuery('#cred-scaffold-box').on('click', '#cred-scaffold-insert', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        var scaffold = jQuery('#cred-scaffold-area').val();

                        jQuery('#wp-content-wrap').css('z-index', 0);

                        utils.InsertAtCursor(jQuery('#content'), scaffold);
                        utils.dispatch('cred.insertScaffold');
                        setTimeout(function () {
                            jQuery('#cred-scaffold-box').__hide();
                            jQuery('#cred-scaffold-button').css('z-index', 1);
                        }, 50);
                    });

                    $_post.on('click', '#cred-shortcode-button-button', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        /*cred_media_buttons*/
                        $_post.find('.cred-media-button').css('z-index', 1);
                        /*cred_popup_boxes*/
                        $_post.find('.cred-popup-box').hide();

                        jQuery(this).closest('.cred-media-button').css('z-index', 100);
                        $_post.find('.additional_field_options_popup').hide();
                        
						var $currentButton = jQuery( this ),
							$currentPopup = jQuery('#cred-shortcodes-box');
						
						aux.setPopupPosition( $currentButton, $currentPopup );
						
                        $currentPopup.__show();
						
                        if (jQuery('.cred-accordeon-item-active').length === 0) { // make fist accordion item active but only if there was no accordion item opened before. It makes last opened accordeon item opened.
                            jQuery('.cred-accordeon-item:first-child').addClass('cred-accordeon-item-active').find('.cred-accordeon-item-inside').stop().slideDown('fast');
                        }

                    });

                    $_post.on('click', '#cred-generic-shortcode-button-button', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        /*cred_media_buttons*/
                        $_post.find('.cred-media-button').css('z-index', 1);
                        /*cred_popup_boxes*/
                        $_post.find('.cred-popup-box').hide();

                        jQuery(this).closest('.cred-media-button').css('z-index', 100);
						
						var $currentButton = jQuery( this ),
							$currentPopup = jQuery('#cred-generic-shortcodes-box');
						
						aux.setPopupPosition( $currentButton, $currentPopup );
						
                        $currentPopup.__show();
                    });

                    $_post.on('click', '#cred-access-button-button', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        /*cred_media_buttons*/
                        $_post.find('.cred-media-button').css('z-index', 1);
                        /*cred_popup_boxes*/
                        $_post.find('.cred-popup-box').hide();

                        jQuery(this).closest('.cred-media-button').css('z-index', 100);
                        jQuery('#cred-access-box').__show();
                    });

                    jQuery('#cred_include_captcha_scaffold').change(function () {
                        aux.genScaffold();
                    });

                    jQuery('#cred_include_wpml_scaffold').change(function () {
                        aux.genScaffold();
                    });

                    jQuery('.cred_autogenerate_scaffold').change(function () {
                        aux.reloadUserFields();
                    });

                    $_post.on('click', '#cred-scaffold-button-button', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        jQuery('#wp-content-wrap').css('z-index', 1);

                        if (!aux.genScaffold()) {
                            return false;
                        }

                        /*cred_media_buttons*/
                        $_post.find('.cred-media-button').css('z-index', 1);
                        /*cred_popup_boxes*/
                        $_post.find('.cred-popup-box').hide();

                        jQuery(this).closest('.cred-media-button').css('z-index', 100);
						
						var $currentButton = jQuery( this ),
							$currentPopup = jQuery('#cred-scaffold-box');
						
						aux.setPopupPosition( $currentButton, $currentPopup );
						
                        $currentPopup.__show();
                    });

                    $_post.on('click', '#cred-user-scaffold-button-button', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        if (!aux.genUserScaffold("button")) {
                            return false;
                        }

                        jQuery('#wp-content-wrap').css('z-index', 1);

                        /*cred_media_buttons*/
                        $_post.find('.cred-media-button').css('z-index', 1);
                        /*cred_popup_boxes*/
                        $_post.find('.cred-popup-box').hide();

                        jQuery(this).closest('.cred-media-button').css('z-index', 100);
						
						var $currentButton = jQuery( this ),
							$currentPopup = jQuery('#cred-scaffold-box');
						
						aux.setPopupPosition( $currentButton, $currentPopup );
						
                        $currentPopup.__show();

                        aux.updateAutogenerateFields();
                    });

                    $_post.on('click', '.cred-icon-button', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        /*cred_media_buttons*/
                        $_post.find('.cred-media-button').css('z-index', 1);
                        /*cred_popup_boxes*/
                        $_post.find('.cred-popup-box').hide();

                        jQuery(this).closest('.cred-media-button').css('z-index', 100);
                        jQuery(this).next('.cred-popup-box').__show();
                    });

                    jQuery(document).mouseup(function (e) {
                        if (
                            /*cred_popup_boxes*/$_post.find('.cred-popup-box').filter(function () {
                            return jQuery(this).is(':visible');
                        }).has(e.target).length === 0
                        ) {
                            /*cred_media_buttons*/
                            $_post.find('.cred-media-button').css('z-index', 1);
                            /*cred_popup_boxes*/
                            $_post.find('.cred-popup-box').hide();
                        }
                    });

                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == KEYCODE_ESC) {
                            /*cred_media_buttons*/
                            $_post.find('.cred-media-button').css('z-index', 1);
                            /*cred_popup_boxes*/
                            $_post.find('.cred-popup-box').hide();
                        }
                    });


                    // cancel buttons
                    $_post.on('click', '.cred-cred-cancel-close', function (e) {
                        /*cred_media_buttons*/
                        $_post.find('.cred-media-button').css('z-index', 1);
                        /*cred_popup_boxes*/
                        $_post.find('.cred-popup-box').hide();
                        jQuery('#wp-content-wrap').css('z-index', 0);
                    });

                    //remove margins and paddings from the submitbox meta

                    jQuery(".submitbox").parent().addClass("cred-meta-box-no-margins");

                    extrajsdiv.addClass("closed");
                    extracssdiv.addClass("closed");

                    extrajsdiv.turnOnPushpin = function () {
                        if (Object.keys(CodeMirrorEditors).length > 0 && CodeMirrorEditors["cred-extra-js-editor"].getValue().length > 0) {
                            this.find(".cred-icon-pushpin").attr("style", "display: inline-block !important;");
                        } else {
                            this.turnOffPushpin();
                        }
                    };

                    extrajsdiv.turnOffPushpin = function () {
                        this.find(".cred-icon-pushpin").attr("style", "display: none !important;");
                    };

                    extrajsdiv.toggleExpansion = function () {
                        if (this.hasClass("closed")) {
                            this.removeClass("closed");
                            this.turnOffPushpin();
                        } else {
                            this.addClass("closed");
                            this.turnOnPushpin();
                        }
                    };

                    extracssdiv.turnOnPushpin = function () {
                        if (Object.keys(CodeMirrorEditors).length > 0 && CodeMirrorEditors["cred-extra-css-editor"].getValue().length > 0) {
                            this.find(".cred-icon-pushpin").attr("style", "display: inline-block !important;");
                        } else {
                            this.turnOffPushpin();
                        }

                    };

                    extracssdiv.turnOffPushpin = function () {
                        this.find(".cred-icon-pushpin").attr("style", "display: none !important;");
                    };

                    extracssdiv.toggleExpansion = function () {
                        if (this.hasClass("closed")) {
                            this.removeClass("closed");
                            this.turnOffPushpin();
                        } else {
                            this.addClass("closed");
                            this.turnOnPushpin();
                        }
                    };

                    setTimeout(function () {
                        extracssdiv.turnOnPushpin();
                        extrajsdiv.turnOnPushpin();
                    }, 1000);

                    extracssdiv.find(".handlediv").on("click", function (event) {
                        if (extracssdiv.hasClass("closed")) {
                            extracssdiv.turnOffPushpin();
                        } else {
                            extracssdiv.turnOnPushpin();
                        }
                    });

                    extracssdiv.find("h2").on("click", function (event) {
                        extracssdiv.toggleExpansion();
                    });

                    extrajsdiv.find("h2").on("click", function (event) {
                        extrajsdiv.toggleExpansion();
                    });

                    extrajsdiv.find(".handlediv").on("click", function (event) {
                        if (extrajsdiv.hasClass("closed")) {
                            extrajsdiv.turnOffPushpin();
                        } else {
                            extrajsdiv.turnOnPushpin();
                        }
                    });

                    jQuery(extracssdiv).sortable({
                        disabled: true
                    });

                    jQuery(extrajsdiv).sortable({
                        disabled: true
                    });

                    var extrajsdiv_draghandle = extrajsdiv.find("h2");
                    var extracssdiv_draghandle = extracssdiv.find("h2");

                    jQuery(extrajsdiv_draghandle).css('cursor', 'pointer');
                    jQuery(extrajsdiv_draghandle).removeClass("ui-sortable-handle");
                    jQuery(extrajsdiv_draghandle).removeClass("hndle");

                    jQuery(extracssdiv_draghandle).css('cursor', 'pointer');
                    jQuery(extracssdiv_draghandle).removeClass("ui-sortable-handle");
                    jQuery(extracssdiv_draghandle).removeClass("hndle");

                    // chain it
                    return this;
                }
            });
            //cred settings
            settingsPage = cred_settings.settingsurl;

            /*
             *
             *  ===================== init layout ================================
             *
             */

            // add explain texts for title and content
            if (cred_settings._current_page == 'cred-form') {
                jQuery('#titlediv')
                    .prepend('<p class="cred-explain-text">&nbsp;</p>')
                    .prepend('<a id="cred_add_forms_to_site_help" class="cred-help-link" style="position:absolute;top:0;right:0;" href="' + cred_settings.help['add_post_forms_to_site']['link'] + '" target="_blank" title="' + cred_settings.help['add_post_forms_to_site']['text'] + '">' + cred_settings.help['add_post_forms_to_site']['text'] + '</a>');
            } else {
                jQuery('#titlediv')
                    .prepend('<p class="cred-explain-text">&nbsp;</p>')
                    .prepend('<a id="cred_add_forms_to_site_help" class="cred-help-link" style="position:absolute;top:0;right:0;" href="' + cred_settings.help['add_user_forms_to_site']['link'] + '" target="_blank" title="' + cred_settings.help['add_user_forms_to_site']['text'] + '">' + cred_settings.help['add_user_forms_to_site']['text'] + '</a>');
            }

            postdivrich.prepend('<p class="cred-explain-text">' + cred_settings.locale.content_explain_text + '</p>');

            // reduce FOUC a bit
            // re-arrange meta boxes
            var pdro = postdivrich.detach().appendTo('#credformcontentdiv .inside');
            if (extracssdiv.length > 0) {
                extracssdiv.insertAfter(postdivrich);
                extracssdiv.addClass('cred-exclude');
            }
            if (extrajsdiv.length > 0) {
                extrajsdiv.insertAfter(postdivrich);
                extrajsdiv.addClass('cred-exclude');
            }
            $_post.find('.cred_related').removeClass('hide-if-js');

            // hide some stuff
            aux.getLoader().hide();
            if (
                !jQuery('#postbox-container-1').find('.cred_related').length
            ) {
                // if not module manager sidebar meta box exists
                jQuery('#postbox-container-1').hide();
                jQuery('#post-body').removeClass('columns-2').addClass('columns-1');
                jQuery('#poststuff').removeClass('has-right-sidebar');
                jQuery('#poststuff .inner-sidebar').hide();
            }

            // for CodeMirror compability with Wordpress 'send_to_editor' function
            // keep original function as 'cred_send_to_editor' for use if not CodeMirror editor
            if (undefined === window.cred_send_to_editor) {
                window.cred_send_to_editor = window.send_to_editor;
                window.send_to_editor = function (content) {
                    try {
                        if (wpActiveEditor) {
                            var cm = utils.isCodeMirror(jQuery('#' + wpActiveEditor));
                            if (cm) {
                                utils.InsertAtCursor(jQuery('#' + wpActiveEditor), content.replace(/%%NL%%/g, NL));
                                try {
                                    tb_remove();
                                } catch (e) {
                                }
                                ;
                                return;
                            }
                        }
                    } catch (e) {
                    }
                    // if not used for CodeMirror, execute Wordpress standard function
                    cred_send_to_editor(content);
                }
            }

            // enable CodeMirror for CSS/JS Editors
            aux.enableExtraCodeMirror();
            if (extracssdiv.length > 0) {
                extracssdiv.find(".inside").addClass("cred-html-editor-container");
                if (jQuery('#cred-extra-css-editor').hasClass('cred-always-open') || jQuery('#cred-extra-js-editor').hasClass('cred-always-open')) {
                    extracssdiv.removeClass('closed');
                    extracssdiv.find(".cred-icon-pushpin").attr("style", "display: none !important;");
                } else {
                    // Delay hiding the section. Otherwise the codemirror
                    // control doesn't initialize correctly sometimes.
                    jQuery(document).ready(function () {
                        _.delay(function () {
                            extracssdiv.addClass('closed');
                        }, 1000);
                    });
                }
            }

            if (extrajsdiv.length > 0) {
                extrajsdiv.find(".inside").addClass("cred-html-editor-container");
                if (jQuery('#cred-extra-css-editor').hasClass('cred-always-open') || jQuery('#cred-extra-js-editor').hasClass('cred-always-open')) {
                    extrajsdiv.removeClass('closed');
                } else {
                    // Delay hiding the section. Otherwise the codemirror
                    // control doesn't initialize correctly sometimes.
                    jQuery(document).ready(function () {
                        _.delay(function () {
                            extracssdiv.addClass('closed');
                            if (extracssdiv.find("textarea").val().length > 0) {
                                extracssdiv.find(".cred-icon-pushpin").attr("style", "display: inline-block !important;");
                            }

                        }, 1000);
                    });
                }
            }

            if (extrajsdiv.length > 0) {
                extrajsdiv.find(".inside").addClass("cred-html-editor-container");
                if (jQuery('#cred-extra-css-editor').hasClass('cred-always-open') || jQuery('#cred-extra-js-editor').hasClass('cred-always-open')) {
                    extrajsdiv.removeClass('closed');
                    extrajsdiv.find(".cred-icon-pushpin").attr("style", "display: none !important;");
                } else {
                    // Delay hiding the section. Otherwise the codemirror
                    // control doesn't initialize correctly sometimes.
                    jQuery(document).ready(function () {
                        _.delay(function () {
                            extrajsdiv.addClass('closed');
                            if (extrajsdiv.find("textarea").val().length > 0) {
                                extrajsdiv.find(".cred-icon-pushpin").attr("style", "display: inline-block !important;");
                            }
                        }, 1000);
                    });
                }
            }

            /*
             *
             *  ===================== init user interaction/bindings ================================
             *
             */
            // init View to handle user interaction and bindings
            _credView
                .init()
                .autobind(true)                      // autobind input fields, with model keys as names, to model
                .bind(['change', 'click'], '#post')  // bind view to 'change' and 'click' events to elements under '#post'
                .sync();                             // synchronize view to model

            // trigger the ajax now
            _credModel.trigger('change', {key: '[post][post_type]', value: _credModel.get('[post][post_type]')});

            doinit = false;
        }
    };

    //register myshortcodes mode for codemirror
    CodeMirror.defineMode("myshortcodes", codemirror_shortcodes_overlay);

    // make public methods/properties available
    if (window.hasOwnProperty("cred_cred"))
        jQuery.extend(window.cred_cred, self);
    else
        window.cred_cred = self;

})(window, jQuery, cred_settings, cred_utils, cred_gui, cred_mvc);

function credFieldAddCodeButtons() {
    jQuery('.cred_field_add_code').each(function (index) {
        if (jQuery(this).attr('data-value') == '%%USER_PASSWORD%%') {
            jQuery(this).hide();
        } else {
            jQuery(this).show();
        }
    });
}

function change_notification_to_info(item, data) {
    if (data["domRef"] == "#cred_notification_settings_panel_container") {
        var item = jQuery(item);
        var notification_message = item.find(".cred-notification");
        jQuery(notification_message).addClass("hidden");

        item.find(".fa .fa-warning").each(function () {
            jQuery(this).addClass("hidden");
        });
    }
}

function check_cred_form_type_for_notification() {
    jQuery('.cred_field_add_code').hide();
    var _cred_form_type_obj = jQuery('input[name="_cred[form][type]"]');
    if (_cred_form_type_obj) {
        var _cred_form_type_selected = jQuery('input[name="_cred[form][type]"]:checked').val();
        if ('new' == _cred_form_type_selected) {
            jQuery('.cred_field_add_code').show();
            jQuery('.cred_notification_field_only_if_changed input[type=checkbox]').attr('disabled', 'disabled');
            jQuery('.cred_notification_field_only_if_changed').hide();
            if (jQuery('.when_submitting_form_text').length) {
                jQuery('.when_submitting_form_text').html('When a new user is created by this form');
            }
        } else {
            credFieldAddCodeButtons();

            jQuery('.cred_notification_field_only_if_changed').show();
            jQuery('.cred_notification_field_only_if_changed input[type=checkbox]').removeAttr('disabled');
            if (jQuery('.when_submitting_form_text').length) {
                jQuery('.when_submitting_form_text').html('When a user is updated by this form');
            }
        }
    }
}

// When clicking on first-level buttons on the submit message editor, set the wpcfActiveEditor to the right textarea
jQuery(document).on('click', '#wp-credformactionmessage-media-buttons > .button ', function () {
    window.wpcfActiveEditor = 'credformactionmessage';
});

jQuery(document).on('click', '.wp-media-buttons > span.button ', function () {
    var data_editor = jQuery(this).parent('div').attr("data-editor");
    if (data_editor == undefined) {
        var id = jQuery(this).parent("div").attr('id');
        var spt = id.split('-');
        data_editor = spt[1];
    }
    window.wpcfActiveEditor = data_editor;
});

jQuery(document).ready(function ($) {

    //move all mail body shortcode buttons
    var $body_codes_buttons = jQuery('.cred-body-codes-button');

    for (var button_index in $body_codes_buttons) {
        if (isNaN(button_index)) {
            break;
        }
        var $current_button = jQuery($body_codes_buttons[button_index]);
        if (!$current_button.parent().hasClass('.wp-media-buttons')) {
            $current_button.parent().find('.wp-media-buttons').append($current_button);
        }
    }

    //add event listener for all cred-label clicks
    jQuery('.cred-label').click(function (evt) {
        if (jQuery(this).find('.cred-shortcode-container-radio').length > 0) {
            jQuery(this).find('.cred-shortcode-container-radio').trigger('click');
        }
    });

    //Expand toolset menu on form add/edit post
    if (window.hasOwnProperty('typenow') && window.typenow == 'cred-form' || window.typenow == 'cred-user-form') {
        jQuery('li.toplevel_page_toolset-dashboard').addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
    }

    //check if cred wizard is enabled and show or hide the preview button
    if (!window.hasOwnProperty('cred_wizard')) {
        jQuery('.cred-wizard-preview-button').remove();
    }

    jQuery('#cred-notification-add-button').click(function () {
        setTimeout(function () {
            jQuery('.cred_field_add_code').on('click', function (evt) {
                var $button = jQuery(this);
                if ($button.data('value') == '%%USER_DISPLAY_NAME%%' || $button.data('value') == '%%USER_LOGIN_NAME%%') {
                    if (!window.hasOwnProperty('cred_is_showing_shortcode_hint') || window.cred_is_showing_shortcode_hint === false) {
                        window.cred_is_showing_shortcode_hint = true;
                        var $subject_field = $button.closest('.cred-fieldset').find('.cred_mail_subject')[0];
                        jQuery($subject_field).pointer({
                            content: cred_settings.locale.logged_in_user_shortcodes_warning,
                            position: 'top',
                            close: function () {
                                window.cred_is_showing_shortcode_hint = false;
                            }
                        }).pointer('open');
                    }
                }
            });

        }, 500);
    });
});