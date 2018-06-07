<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/**
 * Verify can edit the comment user or guest
 *
 * @param string $comment
 * @param array  $decom_settings
 *
 * @return bool
 */
function decom_is_can_edit_comment( $comment = '', $expired_time ) {

	if ( is_user_logged_in() && get_current_user_id() == $comment->user_id && $expired_time ) {
		return true;
	}
	$comment_id       = $comment->comment_ID;
	$cookie_author    = isset( $_COOKIE['decommentsa'] ) ? $_COOKIE['decommentsa'] : '';
	$cookie_email     = isset( $_COOKIE['decommentse'] ) ? $_COOKIE['decommentse'] : '';
	$cookie_site      = isset( $_COOKIE['decommentsp'] ) ? $_COOKIE['decommentsp'] : '';
	$comment_author   = $comment->comment_author;
	$comment_email    = $comment->comment_author_email;
	$comment_site     = isset( $_COOKIE['PHPSESSID'] ) ? $_COOKIE['PHPSESSID'] : '';
	$md5_cookie_data  = md5( $comment_id . $cookie_author . $cookie_email . $cookie_site . $comment_id );
	$md5_comment_data = md5( $comment_id . $comment_author . $comment_email . $comment_site . $comment_id );

	if ( $md5_comment_data == $md5_cookie_data && $expired_time ) {
		return true;
	}

	return false;
}

/**
 * User is the author of the comment
 *
 * @param string $comment
 *
 * @return bool
 */
function deco_is_user_author_comment( $comment = '' ) {
	return ( is_user_logged_in() && is_object( $comment ) && get_current_user_id() == $comment->user_id ) ? true : false;
}

/**
 * @param $text
 *
 * @return mixed|string
 */
function decom_comments_formated_content( $content ) {
	$decom_settings     = decom_get_options();
	$enable_embed_links = $follow = empty( $decom_settings['enable_embed_links'] ) ? 0 : $decom_settings['enable_embed_links'];
	if ( $enable_embed_links ) {
		$max_embed_links_count = empty( $decom_settings['max_embed_links_count'] ) ? 0 : $decom_settings['max_embed_links_count'];
	}

	$search = "/((https?)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})+(\/)?[^\s^<]*\b(\/)?/";
	if ( preg_match_all( $search, $content, $matches ) ) {
		if ( is_array( $matches ) ) {
			if ( isset( $decom_settings['allow_html_in_comments'] ) && $decom_settings['allow_html_in_comments'] == 1 ) {
				preg_match_all( '/(href|src)="(.*?)"/', $content, $links_not_convert );
				foreach ( $links_not_convert[2] as $item ) {
					$links_not_convert_arr[] = md5( $item );
				}
			}
			$i = 0;
			foreach ( $matches[0] as $url ) {
				if ( is_array( $links_not_convert_arr ) && in_array( md5( $url ), $links_not_convert_arr ) ) {
					continue;
				}

				$info      = pathinfo( $url );
				$mime_type = isset( $info['extension'] ) ? $info['extension'] : '';
				$replace   = '';
				if ( $enable_embed_links ) {
					$i ++;
					if ( $i <= $max_embed_links_count ) {
						if ( in_array( $mime_type, array(
							'png',
							'jpeg',
							'jpg',
							'gif'
						) )
						) {
							$replace = "<img src=\"$url\" width=\"100%\" height=\"100%\"/>";
							$content = decom_url_replace( $url, $replace, $content );
						} elseif ( preg_match( "/(http|https):\/\/(www.instagram|instagram|tw|twitter|www.twitter|vimeo|pinterest|www.pinterest)\.(be|com)\/([^<\s]*)/", $url, $match ) ) {
							$replace = wp_oembed_get( $url );
							$content = str_replace( $url, $replace, $content );
						} elseif ( preg_match( "/(http|https):\/\/(www.youtube|youtube|youtu)\.(be|com)\/([^<\s]*)/", $url, $match ) ) {

							if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							} else if ( preg_match( '/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $url, $id ) ) {
								$values = $id[1];
							}
							$replace = '<iframe width="500" height="281" src="https://www.youtube.com/embed/' . $values . '" frameborder="0" allowfullscreen></iframe>';
							$content = str_replace( $url, $replace, $content );
						} elseif ( preg_match( "/(http|https):\/\/(fb|www.facebook|facebook)\.com\/([^<\s]*)/", $url, $match ) ) {
							if ( preg_match( '/\/posts\//', $url, $match ) || preg_match( '/\/photos\//', $url, $match ) ) {
								$replace = '<div id="fb-root"></div>';
								$replace .= '<script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";  fjs.parentNode.insertBefore(js, fjs);}(document, \'script\', \'facebook-jssdk\'));</script>';
								$replace .= '<div class="fb-post" data-href="' . $url . '"></div>';
								$content = str_replace( $url, $replace, $content );
							} else {
								$replace = decom_make_clicable_url( $url );
								$content = str_replace( $url, $replace, $content );
							}
						} elseif ( preg_match( "/(http|https):\/\/plus.google\.com\/([^<\s]*)/", $url, $match ) ) {
							$replace = '<!-- Place this tag in your head or just before your close body tag. -->';
							$replace .= '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
							$replace .= '<!-- Place this tag where you want the widget to render. -->';
							$replace .= '<div class="g-post" data-href="' . $url . '"></div>';
							$content = str_replace( $url, $replace, $content );
						} else {
							$replace = decom_make_clicable_url( $url );
							$content = str_replace( $url, $replace, $content );
						}
					} else {
						$replace = decom_make_clicable_url( $url );
						$content = str_replace( $url, $replace, $content );
					}
				} else {
					$replace = decom_make_clicable_url( $url );
					$content = str_replace( $url, $replace, $content );
				}
			}
		}
	}
	if ( preg_match( '/<blockquote><div><cite>/', $content, $match ) ) {
		list( $tmp, $qute_content ) = explode( '<blockquote><div><cite>', $content );
		list( $qute_content, $tmp ) = explode( '</cite></div></blockquote>', $qute_content );
		$tmp_qute_content = str_replace( ' ', '', $qute_content );
		if ( empty( $tmp_qute_content ) ) {
			$content = str_replace( '<blockquote><div><cite>' . $qute_content . '</cite></div></blockquote>', '', $content );
		}
	}
	$content = convert_smilies( $content );
	$content = str_replace( array( "\r", "\n", "\r\n", "\n\r" ), '<br>', $content );

	$content = wpautop( $content );

	return $content;
}

add_filter( 'decomments_comment_text', 'decom_comments_formated_content' );

/**
 * @param $text
 *
 * @return mixed|string
 */
function decom_make_clicable_url( $text ) {
	$text   = make_clickable( $text );
	$follow = get_option( 'decom_follow' );
	$text   = preg_replace( '|<a href="(.*)" rel="nofollow">.*</a>|', '<a href="\1" rel="' . $follow . '" target="_blank">\1</a>', $text );

	return $text;
}

/**
 * @param $url
 * @param $replace
 * @param $content
 *
 * @return mixed
 */
function decom_url_replace( $url, $replace, $content ) {

	$start  = strpos( $content, $url );
	$length = strlen( $url );

	$element_check = substr( $content, $start - 6, 6 );
	while ( $element_check == 'href="' || $element_check == 'cite="' ) {
		$new_start     = $start + strlen( $url );
		$start         = strpos( $content, $url, $new_start );
		$element_check = substr( $content, $start - 6, 6 );
	}

	return $content = $start !== false ? substr_replace( $content, $replace, $start, $length ) : $content;
}

/**
 * @param $text
 *
 * @return mixed|string
 */
function decom_filter_tags_replace( $text ) {

	$text = preg_replace( '#<p>\s*</p>#siU', '', $text );
	$text = preg_replace( '#(<div>.*)(<p>)(.*<cite>)#siU', '$1$3', $text );
	$text = preg_replace( '#(</div>.*)(<p>)(.*</cite>)#siU', '$1$3', $text );
	$text = preg_replace( '#(</blockquote>.*)(<p>)(.*<script.*)#siU', '$1$3', $text );

	$arr = preg_split( '#(<p.*>.*</p>)#siU', $text, - 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

	if ( is_array( $arr ) && count( $arr ) > 0 ) {
		foreach ( $arr as &$a ) {
			if ( strpos( $a, '<p>' ) !== false && strpos( $a, '</p>' ) === false ) {
				$a = str_replace( '<p>', '', $a );
			} elseif ( strpos( $a, '<p>' ) === false && strpos( $a, '</p>' ) !== false ) {
				$a = str_replace( '</p>', '', $a );
			} else {
				$a = preg_replace( '#(<p.*>.*)(<p>)+?(.*</p>)#siU', '$1$3', $a );
				$a = preg_replace( '#(<p.*>.*)(</p>)+?(.*</p>)#siU', '$1$3', $a );
			}
		}
		$text = implode( '', $arr );
	}

	return $text;
}

/**
 * Clean XSS code in comments
 *
 * @param $content
 *
 * @return mixed|string
 */
function decom_xss_clean( $data ) {
	$data = str_replace( array( '&amp;', '&lt;', '&gt;' ), array( '&amp;amp;', '&amp;lt;', '&amp;gt;' ), $data );
	$data = preg_replace( '/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data );
	$data = preg_replace( '/(&#x*[0-9A-F]+);*/iu', '$1;', $data );
	$data = html_entity_decode( $data, ENT_COMPAT, 'UTF-8' );
	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace( '#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data );
	// Remove javascript: and vbscript: protocols
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data );
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data );
	$data = preg_replace( '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data );
	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data );
	$data = preg_replace( '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data );
	// Remove namespaced elements (we do not need them)
	$data = preg_replace( '#</*\w+:\w[^>]*+>#i', '', $data );
	do {
		// Remove really unwanted tags
		$old_data = $data;
		$data     = preg_replace( '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data );
	} while ( $old_data !== $data );
	$data = strip_tags( $data );
	$data = filter_var( $data, FILTER_SANITIZE_STRING );

	// we are done...
	return $data;
}

/**
 * @param $max_page
 * @param $post_id
 * @param $page
 */
function otherPagination( $max_page, $post_id, $page ) {

	if ( $page > $max_page ) {
		$page = $max_page;
	}

	$args = array(
		'base'         => add_query_arg( 'cpage', '%#%' ),
//		'base'         => '',
		'format'       => null,
		'total'        => $max_page,
		'current'      => $page,
		'echo'         => false,
		'add_fragment' => '#comments',
		'prev_text'    => '<span class="decomments-button nav-previous decomments-nav-previous"><i class="decomments-icon-angle-double-right"></i>' . __( 'Previous comments', DECOM_LANG_DOMAIN ) . '</span>',
		'next_text'    => '<span style="float:right" class="decomments-button nav-next decomments-nav-next">' . __( 'Next comments', DECOM_LANG_DOMAIN ) . '<i class="decomments-icon-angle-double-right"></i></span>',
		'type'         => 'array'
	);

	$arr = paginate_comments_links( $args );
	if ( count( $arr ) > 1 ) {
		if ( $page != 1 ) {
			$prev_link_comment = $arr[0];
//			echo $prev_link_comment;
			$yes_slash         = get_end_slash_in_url( $prev_link_comment );
			$prev_link_comment = str_replace( '?cpage=' . ( $page - 1 ), '', $prev_link_comment );
			$prev_link_comment = str_replace( '&cpage=' . ( $page - 1 ), '', $prev_link_comment );
			$prev_link_comment = str_replace( 'comment-page-' . $page, 'comment-page-' . ( $page - 1 ), $prev_link_comment );
			if ( ! preg_match( '/comment-page/', $prev_link_comment, $match ) ) {
				if ( $yes_slash ) {
					$prev_link_comment = str_replace( '#comments', 'comment-page-' . ( $page - 1 ) . '/#comments', $prev_link_comment );
				} else {
					$prev_link_comment = str_replace( '#comments', '/comment-page-' . ( $page - 1 ) . '/#comments', $prev_link_comment );
				}

			}

			echo $prev_link_comment;
		} else {
			$href = get_permalink( $post_id );
			$href = str_replace( site_url(), '', $href );
			if ( $href ) {
				echo '<a id="decom_cur_page" style="display: none" href="' . $href . '"></a>';
			}
		}

		if ( $page < $max_page ) {
			$next_link_comment = $arr[ count( $arr ) - 1 ];
			$yes_slash         = get_end_slash_in_url( $next_link_comment );

			$next_link_comment = str_replace( '?cpage=' . ( $page + 1 ), '', $next_link_comment );
			$next_link_comment = str_replace( 'comment-page-' . $page, 'comment-page-' . ( $page + 1 ), $next_link_comment );
			if ( ! preg_match( '/comment-page/', $next_link_comment, $match ) ) {
				if ( $yes_slash ) {
					$next_link_comment = str_replace( '#comments', 'comment-page-' . ( $page + 1 ) . '/#comments', $next_link_comment );
				} else {
					$next_link_comment = str_replace( '#comments', '/comment-page-' . ( $page + 1 ) . '/#comments', $next_link_comment );
				}
			}
			if ( ( $page + 1 ) == $max_page ) {
				$next_link_comment = str_replace( 'comment-page-' . ( $page + 1 ) . '/#comments', '', $next_link_comment );
				$next_link_comment = str_replace( 'comment-page-' . ( $page + 1 ) . '#comments', '', $next_link_comment );
			}

			echo $next_link_comment;
		}
	}
}

/**
 * @param $content
 *
 * @return bool
 */
function get_end_slash_in_url( $content ) {
	$search = "/href=\"(.*?)\"/";

	if ( preg_match( $search, $content, $match ) ) {
		list( $main_url, $comment_pagination_params ) = explode( '?', $match[1] );
		$main_url = str_split( $main_url );
		if ( count( $main_url ) > 0 && $main_url[ count( $main_url ) - 1 ] == '/' ) {
			return true;
		}
	}

	return false;
}

/**
 * @param $current_user_id
 *
 * @return mixed|string
 */
function getUserSort( $current_user_id ) {

	return decomments_get_user_sort( $current_user_id );
}


/**
 * @param $current_user_id
 *
 * @return mixed|string
 */
function decomments_get_user_sort( $current_user_id ) {

	$allVariableCommentsSort = array(
		'rate',
		'newer',
		'older'
	);

	$user_sort = '';

	if ( isset( $_REQUEST['decom_comments_sort'] ) && in_array( $_REQUEST['decom_comments_sort'], $allVariableCommentsSort ) ) {
		update_user_meta( $current_user_id, 'decom_comments_sort', $_REQUEST['decom_comments_sort'] );

		$user_sort = $_REQUEST['decom_comments_sort'];
	} else if ( is_user_logged_in() ) {
		$user_sort = get_user_meta( $current_user_id, 'decom_comments_sort', true );
	} else {
		if ( isset( $_COOKIE['decomments_sort'] ) && ! empty( $_COOKIE['decomments_sort'] ) ) {
			$user_sort = $_COOKIE['decomments_sort'];
		}
	}

	if ( ! $user_sort ) {
		$user_sort = get_option( 'default_comments_page' );
	}

	return $user_sort;
}


/**
 * @param $comment
 * @param $args
 * @param $depth
 */
function decom_render_comment( $comment, $args, $depth ) {

	$view_comments = DECOM_Loader_MVC::getComponentView( 'comments', 'comments' );
	echo $view_comments->renderCommentBegin( $comment, $args, $depth );
}

/**
 * @param $comment
 * @param $args
 * @param $depth
 */
function decom_end_comment( $comment, $args, $depth ) {

	$view_comments = DECOM_Loader_MVC::getComponentView( 'comments', 'comments' );
	echo $view_comments->renderCommentEnd( $comment, $args, $depth );
}


/**
 * @return mixed
 */
function decom_get_options() {
	$decom_options_instance = Decom_Settings::instance();

	return $decom_options_instance->get_settings();
}

/**
 * Class Decom_Settings
 */
class Decom_Settings {

	private static $settings = array();

	private static $decom_prefix = '';

	private static $default_settings = array(
		'avatar'                                     => '',
		'avatar_size_thumb'                          => 60,
		'avatar_height'                              => 44,
		'avatar_width'                               => 44,
		'number_comments_per_page'                   => 10,
		'follow'                                     => 'dofollow',
		// dofollow или nofollow
		'output_subscription_comments'               => true,
		'mark_subscription_comments'                 => 0,
		'output_subscription_rejoin'                 => true,
		'mark_subscription_rejoin'                   => true,
		'allocate_comments_author_post'              => true,
		'background_comment_author'                  => '#ffffff',
		'allow_html_in_comments'                     => false,
		'output_numbers_comments'                    => true,
		'allow_quote_comments'                       => true,
		'output_total_number_comments_top'           => true,
		'enable_client_validation_fields'            => true,
		'sort_comments'                              => 'best',
		//array( best ‘Лучший’,  newest ‘Самые новые’, earlier ‘Ранее’)
		'comments_negative_rating_below'             => true,
		'show_comments_negative_rating_low_opacity'  => true,
		'show_two_comments_highest_ranking_top_list' => true,
		'max_size_uploaded_images'                   => 5,
		'time_editing_deleting_comments'             => 30,
		'display_avatars_right'                      => false,
		'comment_form_up'                            => true,
		'enable_lazy_comments_loading'               => false,
		'best_comment_min_likes_count'               => 5,
		'enable_dislike'                             => true,
		'allow_lazy_load'                            => false,
		'enable_embed_links'                         => false,
		'max_embed_links_count'                      => 3,
		'enable_social_share'                        => false,
		'tweet_share'                                => 0,
		'facebook_share'                             => 0,
		'vkontakte_share'                            => 0,
		'google_share'                               => 0,
		'linkedin_share'                             => 0,
		'enable_field_website'                       => 0,
	);

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance    = new $class();
			self::$decom_prefix = DECOM_PREFIX;
		}

		return self::$_instance;
	}

	function get_settings() {
		if ( count( self::$settings ) > 0 ) {
			return self::$settings;
		}

		$options = wp_load_alloptions();

		$options_are_exist = false;
		$tmp_options       = array();

		if ( count( $options ) > 0 ) {

			foreach ( $options as $op_key => $option ) {
				if ( preg_match( '/^' . self::$decom_prefix . '(.*)/', $op_key, $matches ) ) {
					$tmp_options[ $matches[1] ] = $option;
					$options_are_exist          = true;
				} else if ( $op_key == 'page_comments' ) {
					$tmp_options['page_comments'] = $option;
				} else if ( $op_key == 'active_plugins' ) {
					$tmp_options['active_plugins'] = unserialize( $option );
				} else if ( $op_key == 'show_avatars' ) {
					$tmp_options['show_avatars'] = $option;
				} else if ( $op_key == 'comments_per_page' ) {
					$tmp_options['comments_per_page'] = $option;
				} else if ( $op_key == 'date_format' ) {
					$tmp_options['date_format'] = $option;
				} else if ( $op_key == 'time_format' ) {
					$tmp_options['time_format'] = $option;
				} else if ( $op_key == 'require_name_email' ) {
					$tmp_options['require_name_email'] = $option;
				} else if ( $op_key == 'comment_registration' ) {
					$tmp_options['comment_registration'] = $option;
				}
			}
		}

		if ( ! $options_are_exist ) {
			$tmp_options = self::$default_settings;
		}
		$options = $tmp_options;

		if ( is_array( $options ) && count( $options ) ) {
			self::$settings = $options;

			return $options;
		}
	}

}

function deco_get_user_badges( $user_id_or_email ) {
	$decom_options_instance = Decom_Badges_By_User::instance();

	return $decom_options_instance->get_badges_by_user( $user_id_or_email );

}

class Decom_Badges_By_User {

	private static $users = array();

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new $class();

			return self::$_instance;
		}

		return self::$_instance;
	}

	function get_badges_by_user( $user_id_or_email ) {
		if ( empty( $user_id_or_email ) ) {
			return false;
		}

		if ( isset( self::$users[ $user_id_or_email ] ) ) {
			return self::$users[ $user_id_or_email ];
		}

		global $wpdb;

		if ( $badges = $wpdb->get_results( "select * from {$wpdb->prefix}decom_badges" ) ) {

			if ( is_email( $user_id_or_email ) ) {
				$where          = "WHERE comment_approved = 1 AND comment_author_email = '$user_id_or_email' ";
				$coment_IDS     = $wpdb->get_col( "SELECT comment_ID FROM {$wpdb->comments} {$where} " );
				$likes_dislikes = 0;
				if ( $coment_IDS ) {
					$coment_IDS = implode( ',', $coment_IDS );
					if ( $coment_IDS ) {
						$likes_dislikes = $wpdb->get_var( "select (SUM(vote_like) - SUM(vote_dislike)) as votes from {$wpdb->prefix}decom_comments_votes where fk_comment_id IN ($coment_IDS) group by fk_user_id" );
					}
				}
			} else {
				$where          = 'WHERE comment_approved = 1 AND user_id = ' . $user_id_or_email;
				$likes_dislikes = $wpdb->get_var( "select (SUM(vote_like) - SUM(vote_dislike)) as votes from {$wpdb->prefix}decom_comments_votes where fk_user_id = $user_id_or_email group by fk_user_id" );
			}

			$comment_count = $wpdb->get_var( "SELECT COUNT(*) AS total FROM {$wpdb->comments} {$where}" );

			$likes_count   = 0;
			$dislike_count = 0;

			if ( $likes_dislikes > 0 ) {
				$likes_count = $likes_dislikes;
			} elseif ( $likes_dislikes < 0 ) {
				$dislike_count = - 1 * $likes_dislikes;
			}
			$badges_user = '';
			foreach ( $badges as $item ) {
				if ( $item->badge_like_number && $likes_count && $likes_count >= $item->badge_like_number ) {
					$badges_user[] = $item;
				} elseif ( $item->badge_dislike_number && $dislike_count && $dislike_count >= $item->badge_dislike_number ) {
					$badges_user[] = $item;
				} elseif ( $item->badge_comments_number && $comment_count && $comment_count >= $item->badge_comments_number ) {
					$badges_user[] = $item;
				}
			}
			self::$users[ $user_id_or_email ] = $badges_user;

			return self::$users[ $user_id_or_email ];
		}

		return false;
	}

}

function decom_is_user_block( $user_id = 0, $user_email = '' ) {
	$is_blocked = false;
	if ( $user_id ) {
		$is_blocked = get_user_meta( $user_id, 'decom_block_user_leave_comment', true ) ? true : false;
	} elseif ( $user_email ) {
		$decom_blocked_guests_leave_comment = get_option( 'decom_blocked_guests_leave_comment' );
		if ( isset( $decom_blocked_guests_leave_comment[ $user_email ] ) ) {
			$is_blocked = intval( $decom_blocked_guests_leave_comment[ $user_email ] ) ? true : false;
		}
	}

	return $is_blocked;

}

/**
 * @param string $comment
 * @param int    $avatar_size
 *
 * @return mixed
 */
function decom_get_comment_avatar_cached( $comment = '', $avatar_size = 90 ) {
	$comment_avatar_hash    = md5( "{$comment->comment_author_email}{$comment->user_id}" );
	$decom_options_instance = Decom_Comment_Avatar_Cached::instance();

	return $decom_options_instance->get_comment_avatar_by_comment_hash( $comment_avatar_hash, $comment, $avatar_size );

}

/**
 * Class Decom_Comment_Avatar_Cached
 */
class Decom_Comment_Avatar_Cached {

	private static $users_avatar = array();

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new $class();

			return self::$_instance;
		}

		return self::$_instance;
	}

	function get_comment_avatar_by_comment_hash( $comment_avatar_hash, $comment, $avatar_size ) {
		if ( empty( $comment_avatar_hash ) ) {
			return false;
		}

		if ( isset( self::$users_avatar[ $comment_avatar_hash ] ) ) {
			return self::$users_avatar[ $comment_avatar_hash ];
		}

		$avatar = get_avatar( $comment, $avatar_size );

		/*$search = "/((https?)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})+(\/)?[^\s^<]*\b(\/)?/";
		preg_match_all( $search, $avatar, $matches );
		$image_mime = pathinfo( $matches[0][0] );
		if ( isset( $_GET['decotest'] ) ) {
			print_r( $image_mime );
		}
		if ( isset( $image_mime['extension'] ) && in_array( $image_mime['extension'], array(
				'jpg',
				'jpeg',
				'gif',
				'png'
			) )
		) {
			$img_content_check = wp_remote_get( $matches[0][0], array( 'timeout' => 13 ) );
			if ( ! is_wp_error( $img_content_check ) ) {
				if ( empty( $img_content_check['body'] ) ) {
					$avatar = get_avatar( 0, $avatar_size );
				}
			}
		}*/

		self::$users_avatar[ $comment_avatar_hash ] = $avatar;

		return $avatar;
	}

}

/**
 * @param $user_id
 *
 * @return mixed
 */
function decom_get_user_data( $user_id ) {
	$decom_options_instance = Decom_Comment_User_Data::instance();

	return $decom_options_instance->get_user_data( $user_id );

}

/**
 * Class Decom_Comment_User_Data
 */
class Decom_Comment_User_Data {

	private static $users = array();

	/**
	 * Class instance.
	 */
	protected static $_instance = null;

	/**
	 * Get class instance
	 */
	final public static function instance() {
		$class = get_called_class();

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new $class();

			return self::$_instance;
		}

		return self::$_instance;
	}

	function get_user_data( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		if ( isset( self::$users[ $user_id ] ) ) {
			return self::$users[ $user_id ];
		}

		self::$users[ $user_id ] = get_userdata( $user_id );

		self::$users[ $user_id ]->data->count_user_posts = get_post_meta( $user_id, 'count_user_posts', true );
		self::$users[ $user_id ]->data->user_link        = '';

		$site_url = str_replace( array(), '', site_url() );


		if ( isset( self::$users[ $user_id ]->data->user_url ) && false === strpos( self::$users[ $user_id ]->data->user_url, $site_url ) ) {
			self::$users[ $user_id ]->data->user_link = self::$users[ $user_id ]->data->user_url;
		}


		return self::$users[ $user_id ];
	}

}

/**
 * If comment have children comments or not
 *
 * @param $comment_ID
 *
 * @return bool
 */
function deco_comment_get_children_comments( $comment_ID ) {
	global $wpdb;
	static $comments_not_in;

	if ( empty( $comments_not_in ) ) {
		$comments_not_in = $wpdb->get_col( "SELECT comment_ID FROM {$wpdb->comments} decom WHERE comment_approved IN ('trash') and NOT EXISTS (SELECT * FROM {$wpdb->comments} WHERE comment_parent = decom.comment_ID  AND comment_approved NOT IN ('trash') )" );
	}

	if ( in_array( $comment_ID, $comments_not_in ) ) {
		return true;
	}

	return false;
}


function decom_pre_get_comments( $query ) {
	global $wpdb, $wp_query;

	static $comments_not_in;
	if ( empty( $comments_not_in ) ) {
		$comments_not_in = $wpdb->get_col( "SELECT comment_ID FROM {$wpdb->comments} decom WHERE comment_approved IN ('trash') and NOT EXISTS (SELECT * FROM {$wpdb->comments} WHERE comment_parent = decom.comment_ID  AND comment_approved NOT IN ('trash') )" );
	}
//	unset( $query );
//	$query->set( 'comment__not_in', array( 614 ) );
//	print_r( $wp_query );

}

//add_action( 'pre_get_comments', 'action_pre_get_comments' );


function decom_is_comment_close( $post ) {
	$obj = $post;
	if ( is_object( $obj ) ) {
		return ( 'open' == $post->comment_status ) ? false : true;
	}

	return false;
}

function decom_get_comment_post( $post_id ) {
	global $post;
	if ( empty( $post ) ) {
		static $decom_get_comment_post;
		if ( $decom_get_comment_post ) {
			return $decom_get_comment_post;
		}

		return get_post( $post_id );
	}

	return $post;
}

function decomments_get_user_ip() {
	//Just get the headers if we can or else use the SERVER global
	if ( function_exists( 'apache_request_headers' ) ) {
		$headers = apache_request_headers();
	} else {
		$headers = $_SERVER;
	}
	//Get the forwarded IP if it exists
	if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		$the_ip = $headers['X-Forwarded-For'];
	} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
	) {
		$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
	} else {

		$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
	}

	return $the_ip;
}


function decomments_check_activated() {
	static $decomments_activated;
	if ( ! isset( $decomments_activated ) ) {
		$decomments_activated = false;
		$auth_key             = '';
		$option_key           = md5( 'decom_activation_key' );
		$option_data          = get_option( $option_key );

		if ( defined( 'AUTH_KEY' ) ) {
			$auth_key = AUTH_KEY;
		}

		if ( is_multisite() ) {
			$site_url = get_site_option( 'siteurl' );
		} else {
			$site_url = get_option( 'decomments_site_url' );
		}

		if ( is_multisite() ) {
			$option_data = get_site_option( $option_key );
		}

		if ( md5( $site_url . $auth_key ) == $option_data ) {
			$decomments_activated = true;
		}
	}

	return $decomments_activated;
}

add_action( 'admin_notices', 'decomments_message_enter_license' );
function decomments_message_enter_license() {

	if ( ! decomments_check_activated() && ( is_network_admin() || ( ! defined( 'WP_ALLOW_MULTISITE' ) || defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE == false ) && is_admin() )
	) {
		echo '<div class="error"><h3>' . sprintf( __( "Please enter your license key to enable %s!", DECOM_LANG_DOMAIN ), "<a href=\"edit-comments.php?page=decomments-index\">de:comments plugin</a>" ) . '</h3></div>';
	}
}

add_action( "after_plugin_row_" . DECOM_PLUGIN_BASE, 'decomments_plugin_message_enter_license_row', 1, 2 );
function decomments_plugin_message_enter_license_row( $file, $plugin_data ) {

	if ( ! decomments_check_activated() ) {
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
		echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message" style="border-color:#cf0000"><span style="color:#cf0000">';

		_e( sprintf( '<a href="%s">Activate or renew your license key</a> to enable, receive updates and support', admin_url( 'edit-comments.php?page=decomments-index' ) ), DECOM_LANG_DOMAIN );

		echo '</span></div></td></tr>';
	}

}

function decomments_is_user_subscribe_all_comments_by_post() {
	if ( $decom_post_subscribe_users = get_post_meta( get_the_ID(), '_decom_subscribers', true ) ) {
		$decomments_get_current_commenter = wp_get_current_commenter();
		if ( ! empty( $decomments_get_current_commenter['comment_author_email'] ) ) {
			$user_email = $decomments_get_current_commenter['comment_author_email'];
		} else {
			$decomments_current_user_info = wp_get_current_user();
			$user_email                   = $decomments_current_user_info->data->user_email;
		}

		if ( in_array( $user_email, $decom_post_subscribe_users ) ) {
			return true;
		}
	}

	return false;
}

add_action( 'save_post', 'decomments_user_posts_count', 10, 2 );
function decomments_user_posts_count( $post_id, $post ) {
	update_user_meta( $post->post_author, 'count_user_posts', count_user_posts( $post->post_author ) );
}

function decomments_send_email_subscribers_is_comment_approved( $approved = 0, $comment_ID = 0, $commentdata = '' ) {

	if ( $approved == 1 && ! get_comment_meta( $comment_ID, 'decomments_comment_send_after_appoved', true ) ) {
		require_once DECOM_LIBRARIES_PATH . '/email/email.php';
		$email_class = new DECOM_Email();
		if ( empty( $commentdata ) ) {
			$commentdata = get_comment( $comment_ID );
		}
		$email_class->notyfy_post_comments( $commentdata );
	}


	return $approved;
}

add_action( 'wp_set_comment_status', 'decomments_comment_approvecomment', 10, 2 );
function decomments_comment_approvecomment( $comment_ID, $comment_status ) {
	if ( 'approve' === $comment_status ) {
		$commentdata = get_comment( $comment_ID );
//		decomments_send_email_subscribers_is_comment_approved( 1, $comment_ID, $commentdata );
		wp_schedule_single_event( time() + 3, 'decomments_send_email_notify_by_cron', array( $comment_ID ) );
	}
}

add_action( 'comment_post', 'decomments_admin_post_comment', 10, 3 );
function decomments_admin_post_comment( $comment_ID, $comment_status, $commentdata ) {
	if ( 1 === $comment_status && isset( $_POST['action'] ) && $_POST['action'] == 'replyto-comment' ) {
//		decomments_send_email_subscribers_is_comment_approved( 1, $comment_ID, $commentdata );
		wp_schedule_single_event( time() + 3, 'decomments_send_email_notify_by_cron', array( $comment_ID ) );
	}
}

add_action( 'decomments_send_to_admin_notify_about_new_comment', 'decomments_send_to_admin_notify_about_new_comment' );
function decomments_send_to_admin_notify_about_new_comment( $comment_ID ) {
	require_once DECOM_LIBRARIES_PATH . '/email/email.php';
	$email_class = new DECOM_Email();
	$email_class->notyfy_post_comment_admin( $comment_ID );
}

add_action( 'decomments_send_email_notify_by_cron', 'decomments_send_email_notify_by_cron' );
function decomments_send_email_notify_by_cron( $comment_ID ) {
	$commentdata = get_comment( $comment_ID );
	decomments_send_email_subscribers_is_comment_approved( 1, $comment_ID, $commentdata );
}

add_action( 'wp', function () {
	if ( ! apply_filters( 'decomments_disable_for_post_type', false ) ) {
		$decom_hooks_handler = DECOM_Loader::getHooksHandler();

		/**
		 *  Disable function comment preprocess in other plugins and theme
		 */
		remove_all_filters( 'comments_template', 99 );
		remove_all_filters( 'comment_post_redirect', 999 );
		remove_all_filters( 'preprocess_comment', 999 );
		remove_all_actions( 'comment_post', 999 );
		remove_all_actions( 'wp_insert_comment', 999 );

		/**
		 * Initialize plugin environment
		 */
		$decom_hooks_handler->onInit();

		add_filter( 'comments_template', array( $decom_hooks_handler, 'onCommentsTemplate' ), 999 );
//		add_action( 'decomments_comments_template', array( $decom_hooks_handler, 'onCommentsTemplate' ), 999 );
	}
} );


function decomments_get_social_providers_icons( $proveder = '' ) {
	$providers = array(
		'facebook'      => 'decomments-social-login--facebook',
		'vkontakte'     => 'decomments-social-login--vkontakte',
		'twitter'       => 'decomments-social-login--twitter',
		'google'        => 'decomments-social-login--google',
		'instagram'     => 'decomments-social-login--instagram',
		'pinterest'     => 'decomments-social-login--pinterest',
		'linkedin'      => 'decomments-social-login--linkedin',
		'vimeo'         => 'decomments-social-login--vimeo',
		'tumblr'        => 'decomments-social-login--tumblr',
		'flickr'        => 'decomments-social-login--flickr',
		'dribbble'      => 'decomments-social-login--dribbble',
		'quora'         => 'decomments-social-login--quora',
		'foursquare'    => 'decomments-social-login--foursquare',
		'forrst'        => 'decomments-social-login--forrst',
		'wordpress'     => 'decomments-social-login--wordpress',
		'stumbleupon'   => 'decomments-social-login--stumbleupon',
		'yahoo'         => 'decomments-social-login--yahoo',
		'blogger'       => 'decomments-social-login--blogger',
		'soundcloud'    => 'decomments-social-login--soundcloud',
		'odnoklassniki' => 'decomments-social-login--odnoklassniki',
	);

	if ( ! empty( $providers[ $proveder ] ) ) {
		echo $providers[ $proveder ];
	}

	return false;
}

function decomments_get_comment_date( $d, $gmt = false, $comment, $datetime_format, $translate = true ) {

	$comment_date = $gmt ? $comment->comment_date_gmt : $comment->comment_date;
	if ( '' == $d ) {
		$date = mysql2date( $datetime_format, $comment_date, $translate );
	} else {
		$date = mysql2date( $d, $comment_date, $translate );
	}

	return $date;
}

function decomments_get_expired_time( $comment, $decom_settings ) {
	$unix_time_gmt      = decomments_get_comment_date( 'U', true, $comment, $decom_settings );
	$diff_expired_time  = time() - $unix_time_gmt;
	$start_expired_time = $decom_settings['time_editing_deleting_comments'] * 60;
	if ( $diff_expired_time <= $start_expired_time ) {
		return ceil( ( $start_expired_time - $diff_expired_time ) / 60 );
	}

	return false;
}

add_filter( 'decomments_get_name_is_email', 'decomments_get_name_if_current_is_email' );
function decomments_get_name_if_current_is_email( $comment_author ) {
	// if name author email then explode name and display ziro array element
	if ( is_email( $comment_author ) ) {
		list( $comment_author, $tmp ) = explode( '@', $comment_author );

		return str_replace( array( '.', '-', '_', ',' ), ' ', $comment_author );
	}

	return $comment_author;
}

add_filter( 'decomments_form_block_for_hidden_inputs', 'decomments_wpss_hidden_inputs' );
function decomments_wpss_hidden_inputs( $inputs_html ) {

	if ( function_exists( 'rs_wpss_get_key_values' ) ) {
		$wpss_key_values = rs_wpss_get_key_values();

		$inputs_html .= '<input type="hidden" name="' . $wpss_key_values['wpss_ck_key'] . '" value="' . $wpss_key_values['wpss_ck_val'] . '" />';
		$inputs_html .= '<input type="hidden" name="' . $wpss_key_values['wpss_js_key'] . '" value="' . $wpss_key_values['wpss_js_val'] . '" />';
		$inputs_html .= '<input type="hidden" name="' . $wpss_key_values['wpss_jq_key'] . '" value="' . $wpss_key_values['wpss_jq_val'] . '" />';
		$inputs_html .= '<input type="hidden" name="' . WPSS_REF2XJS . '" value="" />';
		$inputs_html .= '<input type="hidden" name="WP55T3S7XJS2" value="7H5W8K53HX" />';
//		$inputs_html .= '<input type="hidden" name="' . WPSS_JSONST . '" value="NS1" />';

	}

	return $inputs_html;
}

add_action( 'init', 'decomments_define_ajax', 0 );
function decomments_define_ajax() {
	if ( ! empty( $_REQUEST['decomments_send'] ) ) {

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}
		if ( ! defined( 'WC_DOING_AJAX' ) ) {
			define( 'WC_DOING_AJAX', true );
		}
		// Turn off display_errors during AJAX events to prevent malformed JSON
		if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
			@ini_set( 'display_errors', 0 );

			if ( function_exists( 'ini_set' ) ) {
				ini_set( 'display_errors', 'Off' );
				ini_set( 'error_reporting', E_ALL );
			} else if ( function_exists( 'error_reporting' ) ) {
				error_reporting( 0 );
			}

		}

		$GLOBALS['wpdb']->hide_errors();
	}
}