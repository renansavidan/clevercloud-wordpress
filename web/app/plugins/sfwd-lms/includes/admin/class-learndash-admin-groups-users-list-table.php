<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'Learndash_Admin_Groups_Users_List_Table' ) ) {
	class Learndash_Admin_Groups_Users_List_Table extends WP_List_Table {

		public $filters  = array();
		public $per_page = 20;
		public $columns  = array();

		public $group_id = 0;

		public function __construct() {
			global $status, $page;

			//Set parent defaults
			parent::__construct(
				array(
					'singular' => 'group',        //singular name of the listed records
					'plural'   => 'groups',           //plural name of the listed records
					'ajax'     => true,            //does this table support ajax?
				)
			);
		}

		public function check_table_filters() {
			$this->filters = array();

			if ( ( isset( $_GET['s'] ) ) && ( ! empty( $_GET['s'] ) ) ) {
				$this->filters['search'] = esc_attr( $_GET['s'] );
			}
		}

		public function search_box( $text, $input_id ) {
			if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
				return;
			}

			$input_id = $input_id . '-search-input';

			echo '<input type="hidden" name="paged" value="1" />';

			?><p class="search-box"><label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label><input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" /><?php submit_button( $text, 'button', 'learndash-search', false, array( 'id' => 'search-submit' ) ); ?></p>
			<?php
		}

		public function extra_tablenav( $which ) {
			if ( 'top' == $which ) {
				if ( ! empty( $this->group_id ) ) {

					?>
					<div class="alignleft actions">
						<a href="<?php echo esc_url( add_query_arg( 'action', 'learndash-group-email' ) ); ?>" class="button button-secondary">
											<?php
											echo sprintf(
											// translators: placeholder: Group.
												esc_html_x( 'Email %s', 'placeholder: Group', 'learndash' ),
												LearnDash_Custom_Label::get_label( 'group' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
											);
											?>
						</a>
					</div>
					<?php
				}
			}
		}

		public function get_columns() {
			return $this->columns;
		}

		public function column_default( $item, $column_name ) {
		}

		public function column_group_name( $item ) {
			$output = '';

			if ( current_user_can( 'edit_group', $item->ID ) ) {
				$output .= '<strong><a href="' . get_edit_post_link( $item->ID ) . '">' . apply_filters( 'the_title', $item->post_title, $item->ID ) . '</a></strong>'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook

				$row_actions = array( 'edit' => '<a href="' . get_edit_post_link( $item->ID ) . '">' . esc_html__( 'edit', 'learndash' ) . '</a>' );
				$output     .= $this->row_actions( $row_actions );

			} else {
				$output .= '<strong>' . apply_filters( 'the_title', $item->post_title, $item->ID ) . '</strong>'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook
			}

			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
		}

		public function column_group_actions( $item ) {
			$data_settings_courses = learndash_data_upgrades_setting( 'user-meta-courses' );
			$data_settings_quizzes = learndash_data_upgrades_setting( 'user-meta-quizzes' );

			$actions              = array();
			$actions['list-view'] = '<a href="' . esc_url( add_query_arg( 'group_id', $item->ID, remove_query_arg( array( 's', 'paged', 'learndash-search', 'ld-group-list-view-nonce', '_wp_http_referer', '_wpnonce' ) ) ) ) . '">' . esc_html__( 'List Users', 'learndash' ) . '</a>';

			if ( ( ! empty( $data_settings_courses ) ) && ( ! empty( $data_settings_quizzes ) ) ) {
				$data_slug             = 'user-courses';
				$actions[ $data_slug ] = '<a href="" class="learndash-data-group-reports-button" data-nonce="' . wp_create_nonce( 'learndash-data-reports-' . $data_slug . '-' . get_current_user_id() ) . '" data-group-id="' . $item->ID . '" data-slug="' . $data_slug . '">' . esc_html__( 'Export Progress', 'learndash' ) . '<span class="status"></span></a>';

				$data_slug             = 'user-quizzes';
				$actions[ $data_slug ] = '<a href="" class="learndash-data-group-reports-button" data-nonce="' . wp_create_nonce( 'learndash-data-reports-' . $data_slug . '-' . get_current_user_id() ) . '" data-group-id="' . $item->ID . '" data-slug="' . $data_slug . '">' . esc_html__( 'Export Results', 'learndash' ) . '<span class="status"></span></a>';
			} else {
				$data_slug             = 'user-courses';
				$actions[ $data_slug ] = '<a href="' . add_query_arg(
					array(
						'nonce-sfwd'            => wp_create_nonce( 'sfwd-nonce' ),
						'courses_export_submit' => 'Export',
						'group_id'              => $item->ID,
					),
					admin_url( 'admin.php?page=group_admin_page' )
				)
													. '">' . esc_html__( 'Export Progress', 'learndash' ) . '</a>';

				$data_slug             = 'user-quizzes';
				$actions[ $data_slug ] = '<a href="' . add_query_arg(
					array(
						'nonce-sfwd'         => wp_create_nonce( 'sfwd-nonce' ),
						'quiz_export_submit' => 'Export',
						'group_id'           => $item->ID,
					),
					admin_url( 'admin.php?page=group_admin_page' )
				)
													. '">' . esc_html__( 'Export Results', 'learndash' ) . '</a>';

			}

			if ( current_user_can( 'edit_groups' ) ) {
				$data_slug             = 'edit-group';
				$actions[ $data_slug ] = '<a href="' . get_edit_post_link( $item->ID ) . '">' . sprintf(
					// translators: placeholder: Group.
					esc_html_x( 'Edit %s', 'placeholder: Group', 'learndash' ),
					LearnDash_Custom_Label::get_label( 'group' )
				) . '</a>';
			}

			if ( ! empty( $actions ) ) {
				echo implode( ' | ', $actions ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
			}

			/**
			 * Fires after admin page group actions column.
			 *
			 * @param int $group_id Group ID.
			 */
			do_action( 'learndash_group_admin_page_actions', $item->ID );
		}

		public function column_username( $item ) {
			$output = '';

			$output .= get_avatar( $item->ID, 32 );

			if ( current_user_can( 'edit_users' ) ) {
				$output .= '<strong><a href="' . get_edit_user_link( $item->ID ) . '">' . $item->display_name . '</a></strong>';

				$row_actions = array( 'edit' => '<a href="' . get_edit_user_link( $item->ID ) . '">' . esc_html__( 'edit', 'learndash' ) . '</a>' );
				$output     .= $this->row_actions( $row_actions );

			} else {
				$output .= '<strong>' . $item->display_name . '</strong>';
			}
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
		}

		public function column_name( $item ) {
			echo esc_html( $item->user_login );
		}

		public function column_email( $item ) {
			echo esc_html( $item->user_email );
		}

		public function column_user_actions( $item ) {
			?>
			<a href="<?php echo esc_url( add_query_arg( 'user_id', $item->ID, remove_query_arg( array( 's', 'paged', 'learndash-search', 'ld-group-list-view-nonce', '_wp_http_referer', '_wpnonce' ) ) ) ); ?>"><?php esc_html_e( 'Report', 'learndash' ); ?></a>
			<?php
		}


		public function prepare_items() {

			$current_page = $this->get_pagenum();
			$total_items  = 0;
			$per_page     = $this->per_page;

			$this->items = array();

			if ( ! empty( $this->group_id ) ) {
				$users_ids = learndash_get_groups_user_ids( $this->group_id, true );
				if ( ! empty( $users_ids ) ) {
					$user_query_args = array(
						'include' => $users_ids,
						'orderby' => 'display_name',
						'order'   => 'ASC',
						'number'  => $per_page,
						'paged'   => $current_page,
					);

					if ( ! empty( $this->filters['search'] ) ) {
						$user_query_args['search'] = '*' . $this->filters['search'] . '*';
					}

					$user_query = new WP_User_Query( $user_query_args );

					$this->items = $user_query->results;
					$total_items = $user_query->total_users;

				}
			} else {
				$current_user = wp_get_current_user();

				$group_ids = learndash_get_administrators_group_ids( $current_user->ID, true );
				if ( ! empty( $group_ids ) ) {

					$groups_query_args = array(
						'post_type'      => 'groups',
						'posts_per_page' => $per_page,
						'paged'          => $current_page,
						'post__in'       => $group_ids,
					);

					if ( ! empty( $this->filters['search'] ) ) {
						$groups_query_args['s'] = '"' . $this->filters['search'] . '"';
						add_filter( 'posts_search', array( $this, 'search_filter_by_title' ), 10, 2 );
					}

					$groups_query = new WP_Query( $groups_query_args );

					if ( ! empty( $this->filters['search'] ) ) {
						remove_filter( 'posts_search', array( $this, 'search_filter_by_title' ), 10, 2 );
					}

					if ( ! empty( $groups_query->posts ) ) {
						$this->items = $groups_query->posts;
						$total_items = $groups_query->found_posts;
					}
				}
			}

			$this->set_pagination_args(
				array(
					'total_items' => intval( $total_items ),    //WE have to calculate the total number of items
					'per_page'    => intval( $per_page ),       //WE have to determine how many items to show on a page
					'total_pages' => ceil( intval( $total_items ) / intval( $per_page ) ),   //WE have to calculate the total number of pages
				)
			);
		}

		public function search_filter_by_title( $search, $wp_query ) {
			if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
				global $wpdb;

				$q = $wp_query->query_vars;
				$n = ! empty( $q['exact'] ) ? '' : '%';

				$search = array();

				foreach ( (array) $q['search_terms'] as $term ) {
					$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $term . $n );
				}

				if ( ! is_user_logged_in() ) {
					$search[] = "$wpdb->posts.post_password = ''";
				}

				$search = ' AND ' . implode( ' AND ', $search );
			}
			return $search;
		}

	}
}
