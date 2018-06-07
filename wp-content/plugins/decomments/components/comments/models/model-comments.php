<?php

class DECOM_Model_Comments extends DECOM_Model {

	private $children = array();

	public function __construct() {

		parent::__construct();
		$this->table_badges = $this->prefix . DECOM_TABLE_BADGES;
		$this->table_social = $this->prefix . DECOM_TABLE_SOCIAL;

	}

	public function addCommentReplay( $text_replay, $author_id, $post_id, $id_replay ) {

		$type = array( '%s', '%d', '%d' );
		$data = array(
			'comment_content' => $text_replay,
			'fk_author_id'    => $author_id,
			'fk_post_id'      => $post_id
		);


		if ( $author_id && $post_id && $text_replay ) {
			return $this->insert( $this->table_name, $data, $type );
		}

		return false;
	}

	public function getPostComments( array $args = array() ) {

		if ( count( $args ) <= 0 ) {
			return false;
		}

		$args = array(
			'order' => 'ASC'
		);

		return get_comments( $args );
	}

	public function getComment( $id, $output = ARRAY_A ) {

		return get_comment( $id, $output );
	}

	public function getComments( $id, $max_votes = array() ) {

		static $get_comments_data;

		if ( count( $max_votes ) > 0 ) {
			$hash = md5( implode( '', $max_votes ) );
		} else {
			$hash = md5( '0' );
		}

		if ( isset( $get_comments_data[ $id ] ) ) {
			if ( isset( $get_comments_data[ $id ][ $hash ] ) ) {
				return $get_comments_data[ $id ][ $hash ];
			}
		}

		if ( count( $max_votes ) > 0 ) {
			$notIn = ' AND comment_ID NOT IN (' . implode( ',', $max_votes ) . ')';
		} else {
			$notIn = '';
		}

		$comment_type[] = '';

		$comment_type = apply_filters( 'decomments_comment_type', $comment_type );

		$comment_type = implode( "','", $comment_type );

		$hide_negative_comments = '';
		if ( apply_filters( 'decomments_hide_negative_comments', false ) ) {
			$hide_negative_comments = 'comment_karma >= 0 AND';
		}


        $sql = "SELECT *
            FROM {$this->wpdb->comments}
            WHERE
                $hide_negative_comments
                comment_post_ID=$id
                AND
                comment_type IN ('$comment_type')
                AND (
                  comment_approved = 1
                OR
                  comment_approved = 'trash'
                )
                $notIn
            ORDER BY comment_date DESC";

        $comments                          = $this->wpdb->get_results( $sql, OBJECT );

        $comment_ids = array();

        $remove_top_comments_index = array();

        foreach ( $comments as $comment ) {
            $comment_ids[] = $comment->comment_ID;
        }

        // Search for a branch in which all comments deleted
        foreach ( $comments as $index => $comment ) {
            if ( $comment->comment_approved == 'trash' ) {
                if ( $comment->comment_parent != 0
                    && ! in_array( $comment->comment_parent, $comment_ids ) ) {
                    $remove_top_comments_index[] = $index;
                } else if ( $this->filterTrashComment( $comment, $comments ) ) {
                    $remove_top_comments_index[] = $index;
                }
            }
        }

        $remove_comments_index = $remove_top_comments_index;

        // Find indexes of child comments of remote branches
        foreach ( $remove_top_comments_index as $index ) {
            $remove_comments_index = array_merge(
                $remove_comments_index,
                $this->filterCommentEmptyIndex( $comments[$index], $comments )
            );
        }

        // Deleting all the branches in which comments are removed
        foreach ( $remove_comments_index as $index ) {
            unset( $comments[$index] );
        }

        $get_comments_data[ $id ][ $hash ] = $comments;

		return $comments;
	}

	public function filterCommentEmptyIndex( $comment, $comment_list, $root_comment = null ) {
        $child_index = array();

        foreach ( $comment_list as $index => $item ) {
            if ( $item->comment_parent == $comment->comment_ID ) {
                $child_index = array_merge(
                    $child_index,
                    $this->filterCommentEmptyIndex( $item, $comment_list, $comment )
                );
            } else if ( $root_comment && $item->comment_ID == $comment->comment_ID ) {
                $child_index[] = $index;
            }
        }

        return count( $child_index ) ? $child_index : array();
    }

	public function filterTrashComment( $comment, $comment_list, $root_comment = null ) {
        if ( $root_comment == null || $comment->comment_approved == 'trash' ) {
            $remove_comment = true;

            foreach ( $comment_list as $item ) {
                if ( $item->comment_parent == $comment->comment_ID ) {
                    if ( ! $this->filterTrashComment( $item, $comment_list, $comment ) ) {
                        $remove_comment = false;

                        break;
                    }
                }
            }

            return $remove_comment;
        }

        return false;
    }

	public function updateComments( array $params ) {

		return wp_update_comment( $params );
	}

	public function updateCommentsKarma( $comment_ID, $new_comments_karma ) {

		$args                  = array();
		$args['comment_ID']    = $comment_ID;
		$args['comment_karma'] = $new_comments_karma;

		return $this->updateComments( $args );
	}

	public function deleteComments( $comment_id ) {
		if ( deco_comment_get_children_comments( $comment_id ) ) {
			return wp_delete_comment( $comment_id );
		}

		return wp_delete_comment( $comment_id );
	}

	public function addMetaCommentWp( $comment_id, $meta_key, $meta_value, $unique ) {

		return add_comment_meta( $comment_id, $meta_key, $meta_value, $unique );
	}

	public function getMetaCommentWp( $comment_id, $key, $single ) {

		return get_comment_meta( $comment_id, $key, $single );
	}

	public function updateCommentMeta( $comment_id, $meta_key, $meta_value, $prev_value ) {

		return update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value );
	}

	public function getCommentMeta( $comment_id, $key, $single ) {

		return get_comment_meta( $comment_id, $key, $single );
	}

	public function updatePostMeta( $post_id, $meta_key, $meta_value, $prev_value = true ) {

		return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
	}

	public function getPostMeta( $post_id, $key, $single = true ) {

		return get_post_meta( $post_id, $key, $single );
	}

	public function decom_escape( $data ) {

		return $this->wpdb->escape( $data );
	}

	public function getCommentsAgent() {

		return substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 );
	}

	public function validationComments() {

	}

	public function onSubscribeComments( $comments ) {
		$email_list = $this->subscribeComments( $comments );
	}

	public function subscribeComments( $data ) {

		$mails            = array( 'new_post_comment' => array(), 'new_comment_to_comment' => '' );
		$new_post_comment = $subscribe_post = $this->getPostMeta( $data->comment_post_ID, '_decom_subscribers', true );
		if ( $new_post_comment ) {
			$mails['new_post_comment'] = $new_post_comment;
		}

		if ( $data->comment_parent ) {
			$subscribe_mail_comments = $this->getCommentMeta( $data->comment_parent, '_decom_subscriber', true );
			if ( $subscribe_mail_comments ) {
				$mails['new_comment_to_comment'] = $subscribe_mail_comments;
			}
		}

		$subscribe_all_post_comments = isset( $_POST['subscribe_all_comments'] ) && $_POST['subscribe_all_comments'] == 'true' ? true : false;
		$subscribe_my_comment        = isset( $_POST['subscribe_my_comment'] ) && $_POST['subscribe_my_comment'] == 'true' ? true : false;

		if ( $subscribe_my_comment ) {
			$this->updateCommentMeta( $data->comment_ID, '_decom_subscriber', $data->comment_author_email, true );
		}

		if ( $subscribe_all_post_comments ) {
			if ( is_array( $subscribe_post ) ) {
				if ( in_array( $data->comment_author_email, $subscribe_post ) ) {
					return $mails;
				}
			}

			$subscribe_post[] = $data->comment_author_email;
			$this->updatePostMeta( $data->comment_post_ID, '_decom_subscribers', $subscribe_post, false );
		}

		return $mails;
	}

	public function getPostMaxCommentKarmaId() {

		$sql = 'SELECT * FROM ' . $this->wpdb->comments . ' WHERE comment_post_ID =:id ORDER BY comment_karma DESC LIMIT 0,2';
	}

	public function getPostMaxCommentKarma( $post_id, $rate, $user_sort ) {

		$sort = ' DESC';
		if ( $user_sort == 'older' ) {
			$sort = ' ASC';
		}
		$sql = 'SELECT * FROM ' . $this->wpdb->comments . '
                WHERE comment_post_ID = %d
                AND comment_approved = 1
                AND comment_parent = 0
                AND comment_karma >= ' . $rate . '
                ORDER BY comment_karma DESC, comment_date' . $sort . '
                LIMIT 0,2';

		$param = array( $post_id );
		$maxim = $this->selectRows( $sql, $param );

		if ( $maxim ) {
			return $maxim;
		}


		return array();
	}

	public function getCommentsByIds( $comment_ids_array ) {

		if ( ! is_array( $comment_ids_array ) ) {
			return false;
		}

		$ids = implode( ',', $comment_ids_array );

		$sql = "SELECT *
        FROM {$this->wpdb->comments}
        WHERE comment_ID IN ({$ids})
        AND comment_approved = 1
        ORDER BY comment_date DESC";

		$comments = $this->wpdb->get_results( $sql, OBJECT );

		return $comments;
	}

	public function getChildrenCommentsByParentId( $post_id, $parent_id ) {

		$comments_branch = $this->getCommentsBranchByParentId( $post_id, $parent_id );

		return $this->getCommentsByIds( $comments_branch );
	}

	public function getCommentsBranchByParentId( $post_id, $parent_id ) {

		$comments_branch   = $this->getChildrenHierarchy( $post_id, $parent_id );
		$comments_branch[] = $parent_id;

		return $comments_branch;
	}

	public function getChildrenHierarchy( $post_id, $parent_id ) {

		$this->children = array();

		$comments = $this->getIndexedCommentsForTree( $post_id );

		$this->traverseChildren( $parent_id, $comments );

		return $this->children;
	}

	private function traverseChildren( $parent_id, $comments ) {

		if ( ! array_key_exists( $parent_id, $comments ) ) {
			return false;
		}

		$children = $comments[ $parent_id ];

		for ( $i = 0; $i < count( $children ); $i ++ ) {
			$this->children[] = $children[ $i ]['id'];

			$parent_id = $children[ $i ]['id'];
			$this->traverseChildren( $parent_id, $comments );
		}
	}

	public function getIndexedCommentsForTree( $post_id ) {

		$sql = "SELECT comment_ID as id, comment_parent as parent_id
        FROM {$this->wpdb->comments}
        WHERE comment_post_ID = %d
        AND comment_approved = 1
        ORDER BY comment_date DESC
        ";

		$pdo_params = array(
			$post_id
		);

		$rows = $this->selectRows( $sql, $pdo_params, 'ARRAY_A' );

		$comments = array();

		for ( $i = 0; $i < count( $rows ); $i ++ ) {
			$comments[ $rows[ $i ]['parent_id'] ][] = $rows[ $i ];
		}

		return $comments;
	}

	public function getChildren( $parent_id ) {

		$sql   = 'SELECT * FROM ' . $this->wpdb->comments . ' WHERE comment_parent = %d ORDER BY comment_date DESC';
		$param = array( $parent_id );

		return $this->selectRows( $sql, $param );
	}

	public function isSuperAdmin() {

		$user_id = get_current_user_id();
		if ( is_super_admin( $user_id ) ) {
			return true;
		} else {
			return false;
		}
	}

	public function moderateCommentStatus( $comment_id, $comment_status ) {

		return wp_set_comment_status( $comment_id, $comment_status );
	}

	public function selectSocial( $user_id ) {

		$sql    = 'SELECT provider FROM ' . $this->table_social . ' WHERE user_id = %d';
		$param  = array( $user_id );
		$social = $this->selectRow( $sql, $param, 'ARRAY_A' );
		if ( empty( $social ) ) {
			return false;
		} else {
			return $social['provider'];
		}
	}

	public function saveSocial( $comment_id, $social ) {

		$this->updateCommentMeta( $comment_id, 'social_icon', $social, true );
	}
}