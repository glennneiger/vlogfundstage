/**
 * API and helper functions for the GUI on CRED shortcodes.
 *
 * @since 1.9.3
 * @package CRED
 */

var Toolset = Toolset || {};

if ( typeof Toolset.CRED === "undefined" ) {
	Toolset.CRED = {};
}

/**
 * -------------------------------------
 * Shortcode GUI
 * -------------------------------------
 */

Toolset.CRED.shortcodeManager = function( $ ) {
	
	var self = this;
	
	/**
	 * Shortcodes GUI API version.
	 *
	 * Access to it using the API methods, from inside this object:
	 * - self.getShortcodeGuiApiVersion
	 * 
	 * Access to it using the API hooks, from the outside world:
	 * - cred-filter-get-shortcode-gui-api-version
	 *
	 * @since 1.9.3
	 */
	self.apiVersion = 193000;
	
	/**
	 * Get the current shortcodes GUI API version.
	 *
	 * @see cred-filter-get-shortcode-gui-api-version
	 *
	 * @since 1.9.3
	 */
	self.getShortcodeGuiApiVersion = function( version ) {
		return self.apiVersion;
	};
	
	/**
	 * Dialog rendering helpers, mainly size calculators.
	 *
	 * @since 1.9.3
	 */
	self.dialogMinWidth = 870;
	self.calculateDialogMaxWidth = function() {
		return ( $( window ).width() - 200 );
	};
	self.calculateDialogMaxHeight = function() {
		return ( $( window ).height() - 100 );
	};
	
	/**
	 * Register the canonical Toolset hooks, both API filters and actions.
	 *
	 * @since 1.9.3
	 */
	self.initHooks = function() {
		
		/**
		 * ###############################
		 * API filters
		 * ###############################
		 */
		
		/**
		 * Return the current shortcodes GUI API version.
		 *
		 * @since 1.9.3
		 */
		Toolset.hooks.addFilter( 'cred-filter-get-shortcode-gui-api-version', self.getShortcodeGuiApiVersion );
		
		/**
		 * ###############################
		 * API actions
		 * ###############################
		 */
		
		/**
		 * Set the right dialog buttonpane buttons labels, after the dialog is opened, based on the current GUI action.
		 *
		 * @since 1.9.3
		 */
		Toolset.hooks.addAction( 'cred-action-shortcode-dialog-preloaded', self.manageShortcodeDialogButtonpane );
		
		Toolset.hooks.addFilter( 'toolset-filter-shortcode-gui-cred_delete_post_link-computed-attribute-values', self.adjustDeletePostLinkAttributes, 10, 2 );
		
		return self;
		
	};
	
	/**
	 * Init GUI templates.
	 *
	 * @uses wp.template
	 * @since 1.9.3
	 */
	self.templates = {};
	self.initTemplates = function() {
		
		self.templates.editPostForm = wp.template( 'cred-post-edit-form-template' );
		self.templates.editUserForm = wp.template( 'cred-user-edit-form-template' );
		self.templates.deletePostLink = wp.template( 'cred-delete-post-link-template' );
		self.templates.createChildPostLink = wp.template( 'cred-create-child-post-link-template' );
		
		self.templates = _.extend( Toolset.hooks.applyFilters( 'toolset-filter-get-shortcode-gui-templates', {} ), self.templates );
		
		return self;
		
	}
	
	/**
	 * Init GUI dialogs.
	 *
	 * @uses jQuery.dialog
	 * @since 1.9.3
	 */
	self.dialogs = {};
	self.dialogs.main = null;
	self.dialogs.shortcode = null;
	
	self.shortcodeDialogSpinnerContent = $(
		'<div style="min-height: 150px;">' +
		'<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; ">' +
		'<div class="ajax-loader"></div>' +
		'<p>' + cred_shortcode_i18n.action.loading + '</p>' +
		'</div>' +
		'</div>'
	);
	
	self.initDialogs = function() {
		
		/**
		 * Main dialog to list the CRED available shortcodes.
		 *
		 * @since 1.9.3
		 */
		if ( ! $( '#js-cred-shortcode-gui-dialog-container-main' ).length ) {
			$( 'body' ).append( '<div id="js-cred-shortcode-gui-dialog-container-main" class="toolset-shortcode-gui-dialog-container js-toolset-shortcode-gui-dialog-container js-cred-shortcode-gui-dialog-container js-cred-shortcode-gui-dialog-container-main"></div>' );
		}
		self.dialogs.main = $( '#js-cred-shortcode-gui-dialog-container-main' ).dialog({
				autoOpen:	false,
				modal:		true,
				minWidth:	self.dialogMinWidth,
				title:		cred_shortcode_i18n.title.dialog,
				resizable:	false,
				draggable:	false,
				show: {
					effect:		"blind",
					duration:	800
				},
				create: function( event, ui ) {
					$( event.target ).parent().css( 'position', 'fixed' );
				},
				open: function( event, ui ) {
					$( 'body' ).addClass('modal-open');
				},
				close: function( event, ui ) {
					$( 'body' ).removeClass( 'modal-open' );
				}
			});
		
		/**
		 * Canonical dialog to insert CRED post and user edit form shortcodes.
		 *
		 * @since 1.9.3
		 */
		if ( ! $( '#js-cred-shortcode-gui-dialog-container-shortcode' ).length ) {
			$( 'body' ).append( '<div id="js-cred-shortcode-gui-dialog-container-shortcode" class="toolset-shortcode-gui-dialog-container js-toolset-shortcode-gui-dialog-container js-cred-shortcode-gui-dialog-container js-cred-shortcode-gui-dialog-container-shortcode"></div>' );
		}
		self.dialogs.shortcode = $( "#js-cred-shortcode-gui-dialog-container-shortcode" ).dialog({
			autoOpen:	false,
			modal:		true,
			minWidth:	self.dialogMinWidth,
			resizable:	false,
			draggable:	false,
			show: {
				effect:		"blind",
				duration:	800
			},
			create: function( event, ui ) {
				$( event.target ).parent().css( 'position', 'fixed' );
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				//$( document ).trigger( 'js_event_wpv_shortcode_gui_dialog_closed' );
				$( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'toolset-shortcode-gui-dialog-button-align-right button-primary js-cred-shortcode-gui-button-craft',
					text: cred_shortcode_i18n.action.insert,
					click: function() {
						var shortcodeToInsert = Toolset.hooks.applyFilters( 'toolset-filter-get-crafted-shortcode', false );
						// shortcodeToInsert will fail on validtion failure
						if ( shortcodeToInsert ) {
							$( this ).dialog( "close" );
							Toolset.hooks.doAction( 'toolset-action-do-shortcode-gui-action', shortcodeToInsert );
						}
					}
				},
				{
					class: 'toolset-shortcode-gui-dialog-button-align-right button-secondary toolset-shortcode-gui-dialog-button-back js-cred-shortcode-gui-button-back',
					text: cred_shortcode_i18n.action.back,
					click: function() {
						$( this ).dialog( "close" );
						self.openCredDialog();
					}
				},
				{
					class: 'button-secondary toolset-shortcode-gui-dialog-button-close js-cred-shortcode-gui-button-close',
					text: cred_shortcode_i18n.action.cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});
		
		return self;
		
	}
	
	/**
	 * Open the main CRED dialog to offer CRED shortcodes.
	 *
	 * @since 1.9.3
	 */
	self.openCredDialog = function() {
		self.dialogs.main.dialog( 'open' ).dialog({
			height:		self.calculateDialogMaxHeight(),
			width:		self.calculateDialogMaxWidth(),
			maxWidth:	self.calculateDialogMaxWidth(),
			position: 	{
				my:			"center top+50",
				at:			"center top",
				of:			window,
				collision:	"none"
			}
		});
	}
	
	/**
	 * Init the Admin Bar button, if any.
	 *
	 * @since 1.9.3
	 */
	self.initAdminBarButton = function() {
		if ( $( '.js-cred-shortcode-generator-node a' ).length > 0 ) {
			$( '.js-cred-shortcode-generator-node a' ).addClass( 'js-cred-in-adminbar' );
		}
	};
	
	/**
	 * Set the right active editor and action when clicking any CRED button, and open the main dialog.
	 *
	 * Acceptable selectors to trigger actions are:
	 * - Admin Bar: .js-cred-in-adminbar
	 * - Editor Toolbar: .js-cred-in-toolbar
	 *
	 * @since 1.9.3
	 */
	$( document ).on( 'click','.js-cred-in-adminbar', function( e ) {
		e.preventDefault();
		
		Toolset.hooks.doAction( 'toolset-action-set-shortcode-gui-action', 'create' );
		self.openCredDialog();
		
		return false;
	});
	$( document ).on( 'click', '.js-cred-in-toolbar', function( e ) {
		e.preventDefault();
		
		var credInToolbarButton = $( this );
		if ( credInToolbarButton.attr( 'data-editor' ) ) {
			window.wpcfActiveEditor = credInToolbarButton.data( 'editor' );
		}
		
		Toolset.hooks.doAction( 'toolset-action-set-shortcode-gui-action', 'insert' );
		self.openCredDialog();
		
		return false;
	});
	
	/**
	 * Insert a shortcode without attributes.
	 *
	 * @since 1.9.3
	 */
	$( document ).on( 'click', '.js-cred-shortcode-gui-no-attributes', function( e ) {
		e.preventDefault();
		
		if ( self.dialogs.main.dialog( "isOpen" ) ) {
			self.dialogs.main.dialog('close');
		}
		
		var shortcode = $( this ).data( 'shortcode' );
		Toolset.hooks.doAction( 'toolset-action-do-shortcode-gui-action', shortcode );
	});
	
	self.shortcodeDoAction = function( data ) {
		if ( self.dialogs.main.dialog( "isOpen" ) ) {
			self.dialogs.main.dialog('close');
		}
		
		var shortcode = '[' + data.shortcode;
		if ( _.has( data, 'parameters' ) ) {
			_.each( data.parameters, function( parameterValue, parameterKey ) {
				shortcode += ' ' + parameterKey + '="' + parameterValue + '"';
			});
		}
		shortcode += ']';

		/**
		 * Filter the generated shortcode to support shortcodes with different format.
		 *
		 * As of Views 2.5.0, a new shortcode format is introduced that uses placeholders instead of brackets.
		 * This format is mainly used when building a Content Template using some page builder in order to
		 * avoid unwanted escaping.
		 *
		 * @param string  shortcode  The generated shortcode.
		 *
		 * @since 1.9.3
		 */
		shortcode = Toolset.hooks.applyFilters( 'wpv-filter-wpv-shortcodes-transform-format', shortcode );

		Toolset.hooks.doAction( 'toolset-action-do-shortcode-gui-action', shortcode );
	};
	
	/**
	 * Close the main dialog when clicking on any of its items.
	 *
	 * @since 1.9.3
	 */
	$( document ).on( 'click', '.js-cred-shortcode-gui-group-list .js-cred-shortcode-gui', function( e ) {
		e.preventDefault();
		
		if ( self.dialogs.main.dialog( "isOpen" ) ) {
			self.dialogs.main.dialog('close');
		}
	});
	
	/**
	 * Display a dialog for inserting a generic CRED shortcode.
	 *
	 * @param object dialog_data 
	 *     shortcode	string	Shortcode name.
	 *     title 		string	Form title.
	 *     parameters	object	Optional. Hidden parameters to enforce as attributes for the resulting shortcode.
	 *     overrides	object	Optional. Attribute values to override/enforce, mainly when editing a shortcode.
	 *
	 * @since 1.9.3
	 */
	self.shortcodeDialogOpen = function( dialog_data ) {
		
		// Race condition:
		// We close the main dialog before opening the shortcode dialog, 
		// so we can keep the .modal-open classname in the document body, to:
		// - avoid scrolling
		// - prevent positioning issues with toolset_select2
		if ( self.dialogs.main.dialog( "isOpen" ) ) {
			self.dialogs.main.dialog('close');
		}
		
		_.defaults( dialog_data, { parameters: {}, overrides: {} } );
		
		Toolset.hooks.doAction( 'cred-action-shortcode-dialog-requested', dialog_data );
		Toolset.hooks.doAction( 'toolset-action-shortcode-dialog-requested', dialog_data );
		
		// Show the "empty" dialog with a spinner while loading dialog content
		self.dialogs.shortcode.dialog( 'open' ).dialog({
			title:		dialog_data.title,
			maxHeight:	self.calculateDialogMaxHeight(),
			maxWidth:	self.calculateDialogMaxWidth(),
			position:	{
				my:			"center top+50",
				at:			"center top",
				of:			window,
				collision:	"none"
			}
		});
		self.dialogs.shortcode.html( self.shortcodeDialogSpinnerContent );

		Toolset.hooks.doAction( 'cred-action-shortcode-dialog-preloaded', dialog_data );
		Toolset.hooks.doAction( 'toolset-action-shortcode-dialog-preloaded', dialog_data );
		
		var templateData = _.extend( 
			{ 
				templates: self.templates 
			},
			dialog_data
		);
		
		switch ( dialog_data.shortcode ) {
			case 'cred_form':
				self.dialogs.shortcode.html( self.templates.editPostForm( templateData ) );
				break;
			case 'cred_user_form':
				self.dialogs.shortcode.html( self.templates.editUserForm( templateData ) );
				break;
			case 'cred_delete_post_link':
				self.dialogs.shortcode.html( self.templates.deletePostLink( templateData ) );
				break;
			case 'cred_child_link_form':
				self.dialogs.shortcode.html( self.templates.createChildPostLink( templateData ) );
				break;
		}
		
		if ( self.dialogs.shortcode.find( '.js-toolset-shortcode-gui-tabs-list > li' ).length > 1 ) {
			self.dialogs.shortcode.find( '.js-toolset-shortcode-gui-tabs' )
				.tabs({
					beforeActivate: function( event, ui ) {
						
						var valid = Toolset.hooks.applyFilters( 'toolset-filter-is-shortcode-attributes-container-valid', true, ui.oldPanel );
						if ( ! valid ) {
							event.preventDefault();
							ui.oldTab.focus().addClass( 'toolset-shortcode-gui-tabs-incomplete' );
							setTimeout( function() {
								ui.oldTab.removeClass( 'toolset-shortcode-gui-tabs-incomplete' );
							}, 1000 );
						}
					}
				})
				.addClass( 'ui-tabs-vertical ui-helper-clearfix' )
				.removeClass( 'ui-corner-top ui-corner-right ui-corner-bottom ui-corner-left ui-corner-all' );
			$( '#js-toolset-shortcode-gui-dialog-tabs ul, #js-toolset-shortcode-gui-dialog-tabs li' )
				.removeClass( 'ui-corner-top ui-corner-right ui-corner-bottom ui-corner-left ui-corner-all');
		} else {
			self.dialogs.shortcode.find( '.js-toolset-shortcode-gui-tabs-list' ).remove();
		}
		
		Toolset.hooks.doAction( 'cred-action-shortcode-dialog-loaded', dialog_data );
		Toolset.hooks.doAction( 'toolset-action-shortcode-dialog-loaded', dialog_data );
		
	}
	
	/**
	 * Adjust the dialog buttons labels depending on the current GUI action.
	 *
	 * @since 1.9.3
	 */
	self.manageShortcodeDialogButtonpane = function( dialog_data ) {
		switch ( Toolset.hooks.applyFilters( 'toolset-filter-get-shortcode-gui-action', '' ) ) {
			case 'save':
				$( '.js-cred-shortcode-gui-button-back' ).hide();
				$( '.js-cred-shortcode-gui-button-craft .ui-button-text' ).html( cred_shortcode_i18n.action.save );
				break;
			case 'create':
			case 'append':
				$( '.js-cred-shortcode-gui-button-back' ).show();
				$( '.js-cred-shortcode-gui-button-craft .ui-button-text' ).html( cred_shortcode_i18n.action.create );
				break;
			case 'edit':
				$( '.js-cred-shortcode-gui-button-back' ).hide();
				$( '.js-cred-shortcode-gui-button-craft .ui-button-text' ).html( cred_shortcode_i18n.action.update );
				break;
			case 'insert':
			default:
				$( '.js-cred-shortcode-gui-button-back' ).show();
				$( '.js-cred-shortcode-gui-button-craft .ui-button-text' ).html( cred_shortcode_i18n.action.insert );
				break;
		}
	};
	
	self.adjustDeletePostLinkAttributes = function( shortcodeAttributeValues, data ) {
		if ( 'toolsetCombo' == data.rawAttributes[ 'message_show' ] ) {
			shortcodeAttributeValues[ 'message' ] = data.rawAttributes[ 'toolsetCombo:message_show' ]
			shortcodeAttributeValues[ 'message_show' ] = '1';
		}
		if ( 'reload' == data.rawAttributes[ 'redirect' ] ) {
			if ( _.has( shortcodeAttributeValues, 'class' ) ) {
				shortcodeAttributeValues[ 'class' ] += ' cred-refresh-after-delete'; 
			} else {
				shortcodeAttributeValues[ 'class' ] = 'cred-refresh-after-delete'; 
			}
			shortcodeAttributeValues[ 'redirect' ] = false;
		}
		return shortcodeAttributeValues;
	};
	
	//--------------------------------
    // Compatibility
    //--------------------------------

    /**
     * Handle the event that is triggered by Fusion Builder when creating the WP editor instance.	 *
	 * The event was added as per our request because Fusion Builder does not load the WP editor using
	 * the native PHP function "wp_editor". It creates the WP editor instance on JS, so no PHP actions
	 * to add custom media buttons like ours are available. It generates the media button plus the toolbar that
	 * contains it as javascript objects that it appends to its own template. It offers no way of adding our custom
	 * buttons to it.
	 *
	 * @param event			The actual event.
	 * @param editorId		The id of the editor that is being created.
     *
     * @since 1.9.4
     */
    $( document ).on( 'fusionButtons', function( event, editorId ) {
		self.addButtonToDynamicEditor( editorId );
    });

    /**
	 * Add a CRED button dynamically to any native editor that contains a media toolbar, given its editor ID.
     *
     * @since 1.9.4
     */

    self.addButtonToDynamicEditor = function( editorId ) {
        var mediaButtons = $( '#wp-' + editorId + '-media-buttons' ),
            button = '<span'
                + ' class="button js-cred-in-toolbar"'
                + ' data-editor="' + editorId + '">'
                + '<i class="icon-cred-logo fa fa-cred-custom ont-icon-18 ont-color-gray"></i>'
                + '<span class="button-label">' + cred_shortcode_i18n.title.button + '</span>'
                + '</span>',
            credButton = $( button );

        credButton.appendTo( mediaButtons );
    };
	
	/**
	 * Init main method:
	 * - Init API hooks.
	 * - Init templates
	 * - Init dialogs.
	 * - Init the Admin Bar button.
	 *
	 * @since 1.9.3
	 */
	self.init = function() {
		
		self.initHooks()
			.initTemplates()
			.initDialogs()
			.initAdminBarButton();
		
	};

	self.init();
	
}

jQuery( document ).ready( function( $ ) {
	Toolset.CRED.shortcodeGUI = new Toolset.CRED.shortcodeManager( $ );
});