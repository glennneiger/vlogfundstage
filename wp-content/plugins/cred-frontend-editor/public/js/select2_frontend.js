var CREDFrontendSelect2 = CREDFrontendSelect2 || {};

/**
 * This new set of methods are needed to transform for each cred form select fields in select2
 * The elaboration starts from a select2FieldList that includes the list of
 * cred forms select fields that will have to be transformed in select2 Standard or Ajax
 */
CREDFrontendSelect2.init = function () {

    var self = this;

    self.ajaxurl = cred_select2_frontend_settings.ajaxurl;
    self.select2FieldsList = cred_select2_frontend_settings.select2_fields_list;

    /**
     * Execution of all signed select2
     */
    self.executeSelect2FieldsTransformation = function () {
        for (var htmlFormId in self.select2FieldsList) {
            for (var fieldName in self.select2FieldsList[htmlFormId]) {
                var action = self.select2FieldsList[htmlFormId][fieldName].action;
                var parameter = self.select2FieldsList[htmlFormId][fieldName].parameter;
                var fieldSettings = self.select2FieldsList[htmlFormId][fieldName].field_settings;
                var currentOption = (self.select2FieldsList[htmlFormId][fieldName].current_option) ? self.select2FieldsList[htmlFormId][fieldName].current_option : null;

                //If there is action info means that is a ajax select2
                if (typeof action !== 'undefined') {
                    self.transformSelectToSelect2Ajax(htmlFormId, fieldName, currentOption, action, parameter, fieldSettings);
                } else {
                    self.transformSelectToSelect2(htmlFormId, fieldName, currentOption, parameter, fieldSettings);
                }
            }
        }
    };

    /**
     * Transforms a classic select field to a select2 with Ajax feature
     *
     * @param htmlFormId
     * @param fieldName
     * @param currentOption
     * @param action
     * @param parameter
     * @param fieldSettings
     */
    self.transformSelectToSelect2Ajax = function (htmlFormId, fieldName, currentOption, action, parameter, fieldSettings) {
        var is_multiple = (fieldSettings['type'] === 'multiselect');
        if (is_multiple) {
            fieldName += '[]';
        }

        // setting placeholder empty in order to respect select_text shortcode default rule
        var placeholder = '';
        var hasPlaceholder = (typeof fieldSettings['placeholder'] !== 'undefined');
        if (hasPlaceholder) {
            placeholder = fieldSettings['placeholder'];
        }
        var hasCurrentOption = (currentOption !== null);

        var $currentFieldSelector = jQuery('select[name="' + fieldName + '"]', '#' + htmlFormId);
        $currentFieldSelector.toolset_select2({
            width: 'resolve',
            allowClear: true,
            placeholder: placeholder,
            multiple: is_multiple,
            minimumInputLength: 1,
            maximumSelectionLength: 20,
            ajax: {
                url: self.ajaxurl,
                dataType: 'json',
                delay: 250,
                type: 'POST',
                cache: false,
                data: function (params) {
                    return {
                        q: (params.term) ? params.term : '',
                        page: (params.page) ? params.page : 0,
                        action: action,
                        parameter: parameter,
                        wpml_context: fieldSettings.wpml_context,
                        wpml_name: fieldSettings.slug
                    };
                },
                processResults: function (response) {
                    if (response.success) {
                        return {
                            results: response.data
                        };
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function (ajaxContext) {
                    console.log(ajaxContext.responseText);
                },
                complete: function () {
                }
            }
        });

        //Set current option in Edit Form if exists
        if (hasCurrentOption) {
            $currentFieldSelector.append('<option value="' + currentOption.value + '">' + currentOption.text + '</option>');
            $currentFieldSelector.toolset_select2('val', currentOption.value, true);
        }
    };

    /**
     * Transforms a classic select box to a standard select2 not ajax
     *
     * @param htmlFormId
     * @param fieldName
     * @param currentOption
     * @param parameter
     * @param fieldSettings
     */
    self.transformSelectToSelect2 = function (htmlFormId, fieldName, currentOption, parameter, fieldSettings) {
        var is_multiple = fieldSettings['type'] === 'multiselect';
        if (is_multiple) {
            fieldName += '[]';
        }

        var placeholder = '— Select —';
        var has_placeholder = (typeof fieldSettings['placeholder'] !== 'undefined');
        if (has_placeholder) {
            placeholder = fieldSettings['placeholder'];
        }
        var hasCurrentOption = (currentOption !== null);

        var $currentFieldSelector = jQuery('select[name="' + fieldName + '"]', '#' + htmlFormId);
        $currentFieldSelector.toolset_select2({
            data: parameter,
            placeholder: placeholder,
            multiple: is_multiple,
            minimumInputLength: 1,
            allowClear: true
        });

        //Set current option in Edit Form if exists
        if (hasCurrentOption) {
            $currentFieldSelector.append('<option value="' + currentOption.value + '">' + currentOption.text + '</option>');
            $currentFieldSelector.toolset_select2('val', currentOption.value, true);
        }
    };

    self.executeSelect2FieldsTransformation();
};

jQuery(document).ready(function () {
    var credSelect2FrontendSetting = new CREDFrontendSelect2.init();

    //bounding onValidatedSubmitAjaxForm
    var boundOnCREDFrontendSelect2Init = _.bind(CREDFrontendSelect2.init, CREDFrontendSelect2);
    //After cred form submit validation success
    Toolset.hooks.addAction('cred_form_ajax_completed', boundOnCREDFrontendSelect2Init);

});