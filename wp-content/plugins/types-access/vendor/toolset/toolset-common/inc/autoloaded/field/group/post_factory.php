<?php

/**
 * Factory for the Toolset_Field_Group_Post class.
 *
 * @since 2.0
 */
class Toolset_Field_Group_Post_Factory extends Toolset_Field_Group_Factory {


	/**
	 * @return Toolset_Field_Group_Post_Factory
	 */
	public static function get_instance() {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::get_instance();
	}

	protected function __construct() {
		parent::__construct();

		add_action( 'wpcf_group_updated', array( $this, 'on_group_updated' ), 10, 2 );
	}


	/**
	 * Load a field group instance.
	 *
	 * @param int|string|WP_Post $field_group Post ID of the field group, it's name or a WP_Post object.
	 *
	 * @param bool $force_query_by_name
	 *
	 * @return null|Toolset_Field_Group_Post Field group or null if it can't be loaded.
	 */
	public static function load( $field_group, $force_query_by_name = false ) {
		// we cannot use self::get_instance here, because of low PHP requirements and missing get_called_class function
		// we have a fallback class for get_called_class but that scans files by debug_backtrace and return 'self'
		//   instead of Toolset_Field_Group_Post_Factory like the original get_called_class() function does
		// ends in an error because of parents (abstract) $var = new self();

		/** @noinspection PhpIncompatibleReturnTypeInspection Because this will always be a post field group. */
		return Toolset_Field_Group_Post_Factory::get_instance()->load_field_group( $field_group, $force_query_by_name );
	}


	/**
	 * Create new field group.
	 *
	 * @param string $name Sanitized field group name. Note that the final name may change when new post is inserted.
	 * @param string $title Field group title.
	 * @param String $status Post status
	 * @param String $purpose Purpose.
	 *
	 * @return null|Toolset_Field_Group The new field group or null on error.
	 */
	public static function create( $name, $title = '', $status = 'draft', $purpose = Toolset_Field_Group_Post::PURPOSE_GENERIC ) {
		// we cannot use self::get_instance here, because of low PHP requirements and missing get_called_class function
		// we have a fallback class for get_called_class but that scans files by debug_backtrace and return 'self'
		//   instead of Toolset_Field_Group_Term_Factory like the original get_called_class() function does
		// ends in an error because of parents (abstract) $var = new self();
		return Toolset_Field_Group_Post_Factory::get_instance()->create_field_group( $name, $title, $status, $purpose );
	}


	public function get_post_type() {
		return Toolset_Field_Group_Post::POST_TYPE;
	}


	protected function get_field_group_class_name() {
		return 'Toolset_Field_Group_Post';
	}


	private $post_type_assignment_cache = null;


	/**
	 * Get all field groups sorted by their association with post types.
	 *
	 * @return Toolset_Field_Group_Post[][] For each (registered) post type, there will be an array element, which is
	 *     an array of post field groups associated to it.
	 * @since m2m
	 */
	public function get_groups_by_post_types() {

		if( null == $this->post_type_assignment_cache ) {
			// We need also special-purpose groups; Everything will be filtered by $group->is_assigned_by_post_type.
			$groups = $this->query_groups( array( 'purpose' => '*' ) );

			$post_type_query = new Toolset_Post_Type_Query(
				array(
					Toolset_Post_Type_Query::HAS_SPECIAL_PURPOSE => null,
					Toolset_Post_Type_Query::RETURN_TYPE => 'slug'
				)
			);

			/** @var string[] $post_types */
			$post_types = $post_type_query->get_results();

			$this->post_type_assignment_cache = array();
			foreach( $post_types as $post_type_slug ) {
				$groups_for_post_type = array();

				foreach( $groups as $group ) {
					if( $group instanceof Toolset_Field_Group_Post
						&& $group->is_active()
						&& $group->is_assigned_to_type( $post_type_slug )
					) {
						$groups_for_post_type[] = $group;
					}
				}

				$this->post_type_assignment_cache[ $post_type_slug ] = $groups_for_post_type;
			}

		}

		return $this->post_type_assignment_cache;
	}


	/**
	 * Get array of groups that are associated with given post type.
	 *
	 * @param string $post_type_slug Slug of the post type.
	 *
	 * @return Toolset_Field_Group_Post[] Associated post field groups.
	 */
	public function get_groups_by_post_type( $post_type_slug ) {
		$groups_by_post_types = $this->get_groups_by_post_types();
		return toolset_ensarr( toolset_getarr( $groups_by_post_types, $post_type_slug ) );
	}


	/**
	 * This needs to be executed whenever a post field group is updated.
	 *
	 * Hooked into the wpcf_group_updated action.
	 * Erases cache for the get_groups_by_post_types() method.
	 *
	 * @param int $group_id Ignored
	 * @param Toolset_Field_Group $group Field group that has been just updated.
	 */
	public function on_group_updated( /** @noinspection PhpUnusedParameterInspection */ $group_id = null, $group = null ) {
		$this->post_type_assignment_cache = null;
	}


	/**
	 * Apply given filters on field groups based on their filter operator.
	 *
	 * The filter operator can be either 'all' or 'any'.
	 *
	 * @param Toolset_Field_Group_Post[] $all_groups All groups to filter.
	 * @param callable[] $filters Regular filters that are applied according to the filter operator. For 'all', a field
	 *     group will be disqualified if it doesn't pass any single filter. For 'any', it will be selected if it passes
	 *     a single filter.
	 * @param callable[] $force_filters Filters that are applied on all groups (after applying the regular filters),
	 *     disregarding their filter operator.
	 *
	 * @return Toolset_Field_Group_Post[] Selected groups.
	 */
	private function filter_groups( $all_groups, $filters, $force_filters ) {

		// First, sort groups according to their filter operator.
		$groups_requiring_all_filters = array();
		$groups_requiring_any_filter = array();
		foreach( $all_groups as $group ) {
			if( 'all' === $group->get_filter_operator() ) {
				$groups_requiring_all_filters[ $group->get_slug() ] = $group;
			} else {
				$groups_requiring_any_filter[ $group->get_slug() ] = $group;
			}
		}

		// Disqualify groups requiring all filters which don't match all.
		foreach( $filters as $filter ) {
			$groups_requiring_all_filters = array_filter(
				$groups_requiring_all_filters,
				function( Toolset_Field_Group_Post $group ) use( $filter ) {
					return $filter( $group, true );
				}
			);
		}

		$selected_groups = $groups_requiring_all_filters;

		// Select groups that match any filter.
		foreach( $groups_requiring_any_filter as $group ) {
			foreach( $filters as $filter ) {
				if( $filter( $group, false ) ) {
					$selected_groups[ $group->get_slug() ] = $group;
					// No point in applying remaining filters, the group has already been selected.
					continue 2;
				}
			}
		}

		// Process filters that apply on all groups.
		foreach( $force_filters as $filter ) {
			$selected_groups = array_filter( $selected_groups, $filter );
		}

		return $selected_groups;
	}

	/**
	 * Retrieve groups that should be displayed with a certain element, taking all possible conditions into account.
	 *
	 * @param IToolset_Element $element Element of the domain matching the field group.
	 * @return Toolset_Field_Group_Post[]
	 * @throws InvalidArgumentException
	 */
	public function get_groups_for_element( IToolset_Element $element ) {
		if( ! $element instanceof IToolset_Post ) {
			throw new InvalidArgumentException( 'Wrong element domain.' );
		}

		// Regular filter by a post type.
		$post_type = $element->get_type();
		$filter_by_post_type = function(
			Toolset_Field_Group_Post $group, /** @noinspection PhpUnusedParameterInspection */ $require_all
		) use( $post_type ) {
			return $group->is_assigned_to_type( $post_type );
		};

		// Regular filter by post terms (term_taxonomy IDs)
		$term_taxonomy_ids = $element->get_term_taxonomy_ids();
		$filter_by_terms = function( Toolset_Field_Group_Post $group, $require_all ) use( $term_taxonomy_ids ) {
			$terms_for_group = $group->get_assigned_to_terms();
			if( empty( $terms_for_group ) ) {
				// Empty means there are rules regarding terms and the filter should not
				// influence the final result (unlike a filter by post type).
				//
				// If the group needs all filters to pass in order to be selected, this one will not stand in the way.
				// If it needs a single filter to pass, this will not be the one.
				return $require_all;
			}

			if( $require_all ) {
				// Get terms required by the field group but not present in the post.
				$missing_terms = array_diff( $terms_for_group, $term_taxonomy_ids );
				return ( count( $missing_terms ) === 0 );
			}

			// Require at least one match.
			$intersection = array_intersect( $terms_for_group, $term_taxonomy_ids );
			return ( count( $intersection ) > 0 );
		};

		// Regular filter by assigned post template(s).
		$filter_by_templates = function(
			Toolset_Field_Group_Post $group, /** @noinspection PhpUnusedParameterInspection */ $require_all
		) use( $element ) {
			$template_filters = $group->get_assigned_to_templates();
			if( empty( $template_filters ) ) {
				// Empty means there are rules regarding templates and the filter should not
				// influence the final result (unlike a filter by post type).
				//
				// If the group needs all filters to pass in order to be selected, this one will not stand in the way.
				// If it needs a single filter to pass, this will not be the one.
				return $require_all;
			}
			foreach( $template_filters as $template_filter ) {
				if( $template_filter->is_match_for_post( $element ) ) {
					return true;
				}
			}
			return false;
		};

		// Regular filter by data-dependent condition.
		// This is only a pre-evaluation, the rest happens in the browser.
		$filter_by_data_dependent_condition = function(
			Toolset_Field_Group_Post $group
		) {
			if( 'all' === $group->get_filter_operator() ) {
				// Do not block the group from being selected.
				return true;
			}

			if( $group->has_conditional_display_conditions() ) {
				// We actively select the group, so that the condition can be evaluated dynamically in the browser.
				return true;
			}

			// Requiring "any" condition to be true but the data-dependent one is not set, and not
			// giving a reason to select the group.
			return false;
		};

		// Forced filter that allows a field group with a RFG or a PRF only if its post type assignment matches.
		$field_group_service = new Types_Field_Group_Repeatable_Service();
		$force_post_type_filter_for_relationships = function(
			Toolset_Field_Group_Post $group
		) use( $field_group_service, $post_type ) {
			if( 'all' === $group->get_filter_operator() ) {
				// This filter handles only a special case when filtering by ANY condition
				return true;
			}

			if( ! $field_group_service->group_contains_rfg_or_prf( $group->get_id() ) ) {
				// The group doesn't contain neither a RFG nor a PRF and will be handled by other filters.
				return true;
			}

			// In this case, we strictly require the group to be assigned to the post type of this element.
			return $group->is_assigned_to_type( $post_type, true );
		};

		// Forced filter that allows excluding selected groups.
		$allow_disabling_group_by_wp_filter = function( Toolset_Field_Group_Post $group ) use( $element ) {

			/**
			 * toolset_show_field_group_for_post
			 *
			 * Allows preventing a field group being displayed for a particular post.
			 *
			 * @param bool Initial result value
			 * @param int Post ID where the group is about to be displayed.
			 * @param WP_Post Post where the group is about to be displayed.
			 * @param string Slug of the field group.
			 * @param int ID of the field group.
			 */
			return apply_filters(
				'toolset_show_field_group_for_post',
				true,
				$element->get_id(),
				$element->get_underlying_object(),
				$group->get_slug(),
				$group->get_id()
			);
		};

		// Magic!
		/** @var Toolset_Field_Group_Post[] $all_groups */
		$all_groups = $this->query_groups( array( 'purpose' => '*', 'is_active' => true ) );
		$selected_groups = $this->filter_groups(
			$all_groups,
			array(
				$filter_by_post_type,
				$filter_by_terms,
				$filter_by_templates,
				$filter_by_data_dependent_condition,
			),
			array(
				$force_post_type_filter_for_relationships,
				$allow_disabling_group_by_wp_filter,
			)
		);

		return $selected_groups;
	}

}
