<?php
/**
 * LearnDash Settings Metabox for Topic Display and Content Options.
 *
 * @since 3.0.0
 * @package LearnDash\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'LearnDash_Settings_Metabox' ) ) && ( ! class_exists( 'LearnDash_Settings_Metabox_Topic_Display_Content' ) ) ) {
	/**
	 * Class LearnDash Settings Metabox for Topic Display and Content Options.
	 *
	 * @since 3.0.0
	 */
	class LearnDash_Settings_Metabox_Topic_Display_Content extends LearnDash_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'sfwd-topic';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'learndash-topic-display-content-settings';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Display and Content Options', 'learndash' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: topic.
				esc_html_x( 'Controls the look and feel of the %s and optional content settings', 'placeholder: topic', 'learndash' ),
				learndash_get_custom_label_lower( 'topic' )
			);

			add_filter( 'learndash_metabox_save_fields_' . $this->settings_metabox_key, array( $this, 'filter_saved_fields' ), 30, 3 );

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				// New fields
				'topic_materials_enabled'            => 'topic_materials_enabled',
				'topic_materials'                    => 'topic_materials',

				'lesson_video_enabled'               => 'lesson_video_enabled',
				'lesson_video_url'                   => 'lesson_video_url',
				'lesson_video_shown'                 => 'lesson_video_shown',
				'lesson_video_auto_start'            => 'lesson_video_auto_start',
				'lesson_video_show_controls'         => 'lesson_video_show_controls',
				'lesson_video_focus_pause'           => 'lesson_video_focus_pause',
				'lesson_video_track_time'            => 'lesson_video_track_time',
				'lesson_video_auto_complete'         => 'lesson_video_auto_complete',
				'lesson_video_auto_complete_delay'   => 'lesson_video_auto_complete_delay',
				'lesson_video_hide_complete_button'  => 'lesson_video_hide_complete_button',
				'lesson_video_show_complete_button'  => 'lesson_video_show_complete_button',

				'lesson_assignment_upload'           => 'lesson_assignment_upload',
				'assignment_upload_limit_extensions' => 'assignment_upload_limit_extensions',
				'assignment_upload_limit_size'       => 'assignment_upload_limit_size',
				'lesson_assignment_points_enabled'   => 'lesson_assignment_points_enabled',
				'lesson_assignment_points_amount'    => 'lesson_assignment_points_amount',
				'assignment_upload_limit_count'      => 'assignment_upload_limit_count',
				'lesson_assignment_deletion_enabled' => 'lesson_assignment_deletion_enabled',
				'auto_approve_assignment'            => 'auto_approve_assignment',

				'forced_lesson_time_enabled'         => 'forced_lesson_time_enabled',
				'forced_lesson_time'                 => 'forced_lesson_time',
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			global $sfwd_lms;

			parent::load_settings_values();
			if ( true === $this->settings_values_loaded ) {

				if ( ! isset( $this->setting_option_values['topic_materials'] ) ) {
					$this->setting_option_values['topic_materials'] = '';
				}
				if ( ! empty( $this->setting_option_values['topic_materials'] ) ) {
					$this->setting_option_values['topic_materials_enabled'] = 'on';
				} else {
					$this->setting_option_values['topic_materials_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_enabled'] ) ) {
					$this->setting_option_values['lesson_video_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_url'] ) ) {
					$this->setting_option_values['lesson_video_url'] = '';
				}

				if ( ( ! isset( $this->setting_option_values['lesson_video_shown'] ) ) || ( empty( $this->setting_option_values['lesson_video_shown'] ) ) ) {
					$this->setting_option_values['lesson_video_shown'] = 'BEFORE';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_auto_start'] ) ) {
					$this->setting_option_values['lesson_video_auto_start'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_show_controls'] ) ) {
					$this->setting_option_values['lesson_video_show_controls'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_focus_pause'] ) ) {
					$this->setting_option_values['lesson_video_focus_pause'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_track_time'] ) ) {
					$this->setting_option_values['lesson_video_track_time'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_auto_complete'] ) ) {
					$this->setting_option_values['lesson_video_auto_complete'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_auto_complete_delay'] ) ) {
					$this->setting_option_values['lesson_video_auto_complete_delay'] = '0';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_hide_complete_button'] ) ) {
					$this->setting_option_values['lesson_video_hide_complete_button'] = '';
				}

				if ( 'on' === $this->setting_option_values['lesson_video_hide_complete_button'] ) {
					$this->setting_option_values['lesson_video_show_complete_button'] = '';
				} else {
					$this->setting_option_values['lesson_video_show_complete_button'] = 'on';
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_upload'] ) ) {
					$this->setting_option_values['lesson_assignment_upload'] = '';
				}

				if ( ! empty( $this->setting_option_values['assignment_upload_limit_extensions'] ) ) {
					if ( is_array( $this->setting_option_values['assignment_upload_limit_extensions'] ) ) {
						if ( count( $this->setting_option_values['assignment_upload_limit_extensions'] ) > 1 ) {
							$this->setting_option_values['assignment_upload_limit_extensions'] = implode( ',', $this->setting_option_values['assignment_upload_limit_extensions'] );
						} else {
							$this->setting_option_values['assignment_upload_limit_extensions'] = $this->setting_option_values['assignment_upload_limit_extensions'][0];
						}
					}
				}

				if ( ! isset( $this->setting_option_values['assignment_upload_limit_size'] ) ) {
					$this->setting_option_values['assignment_upload_limit_size'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_points_enabled'] ) ) {
					$this->setting_option_values['lesson_assignment_points_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_points_amount'] ) ) {
					$this->setting_option_values['lesson_assignment_points_amount'] = '';
				}

				if ( ! isset( $this->setting_option_values['assignment_upload_limit_count'] ) ) {
					$this->setting_option_values['assignment_upload_limit_count'] = '';
				}
				$this->setting_option_values['assignment_upload_limit_count'] = absint( $this->setting_option_values['assignment_upload_limit_count'] );
				if ( empty( $this->setting_option_values['assignment_upload_limit_count'] ) ) {
					$this->setting_option_values['assignment_upload_limit_count'] = 1;
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_deletion_enabled'] ) ) {
					$this->setting_option_values['lesson_assignment_deletion_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['auto_approve_assignment'] ) ) {
					$this->setting_option_values['auto_approve_assignment'] = 'on';
				}

				if ( ! isset( $this->setting_option_values['forced_lesson_time'] ) ) {
					$this->setting_option_values['forced_lesson_time'] = '';
				}

				if ( ! isset( $this->setting_option_values['forced_lesson_time_enabled'] ) ) {
					$this->setting_option_values['forced_lesson_time_enabled'] = '';
				}

				if ( ( isset( $this->setting_option_values['forced_lesson_time'] ) ) && ( ! empty( $this->setting_option_values['forced_lesson_time'] ) ) ) {
					$this->setting_option_values['forced_lesson_time_enabled'] = 'on';
				} else {
					$this->setting_option_values['forced_lesson_time_enabled'] = '';
				}
			}

			// Ensure all settings fields are present.
			foreach ( $this->settings_fields_map as $_internal => $_external ) {
				if ( ! isset( $this->setting_option_values[ $_internal ] ) ) {
					$this->setting_option_values[ $_internal ] = '';
				}
			}

			if ( 'on' === $this->setting_option_values['lesson_video_enabled'] ) {
				$this->setting_option_values['lesson_assignment_upload']   = '';
				$this->setting_option_values['forced_lesson_time_enabled'] = '';
			} elseif ( 'on' === $this->setting_option_values['lesson_assignment_upload'] ) {
				$this->setting_option_values['lesson_video_enabled']       = '';
				$this->setting_option_values['forced_lesson_time_enabled'] = '';
			} elseif ( 'on' === $this->setting_option_values['forced_lesson_time_enabled'] ) {
				$this->setting_option_values['lesson_video_enabled']     = '';
				$this->setting_option_values['lesson_assignment_upload'] = '';
			}
			if ( 'on' !== $this->setting_option_values['lesson_video_enabled'] ) {
				$this->setting_option_values['lesson_video_enabled']              = '';
				$this->setting_option_values['lesson_video_url']                  = '';
				$this->setting_option_values['lesson_video_shown']                = '';
				$this->setting_option_values['lesson_video_auto_start']           = '';
				$this->setting_option_values['lesson_video_show_controls']        = '';
				$this->setting_option_values['lesson_video_focus_pause']          = '';
				$this->setting_option_values['lesson_video_track_time']           = '';
				$this->setting_option_values['lesson_video_auto_complete']        = '';
				$this->setting_option_values['lesson_video_auto_complete_delay']  = '0';
				$this->setting_option_values['lesson_video_show_complete_button'] = '';

			} elseif ( 'on' !== $this->setting_option_values['lesson_assignment_upload'] ) {
				$this->setting_option_values['lesson_assignment_upload']           = '';
				$this->setting_option_values['assignment_upload_limit_extensions'] = '';
				$this->setting_option_values['assignment_upload_limit_size']       = '';
				$this->setting_option_values['lesson_assignment_points_enabled']   = '';
				$this->setting_option_values['lesson_assignment_points_amount']    = '';
				$this->setting_option_values['assignment_upload_limit_count']      = '';
				$this->setting_option_values['lesson_assignment_deletion_enabled'] = '';
				$this->setting_option_values['auto_approve_assignment']            = 'on';
			} elseif ( 'on' !== $this->setting_option_values['forced_lesson_time_enabled'] ) {
				$this->setting_option_values['forced_lesson_time_enabled'] = '';
				$this->setting_option_values['forced_lesson_time']         = '';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {
			global $sfwd_lms;

			$this->setting_option_fields = array(
				'lesson_video_auto_complete'        => array(
					'name'      => 'lesson_video_auto_complete',
					'type'      => 'checkbox-switch',
					'label'     => sprintf(
						// translators: placeholder: Topic.
						esc_html_x( '%s auto-completion', 'placeholder: Topic', 'learndash' ),
						learndash_get_custom_label( 'topic' )
					),
					'default'   => '',
					'value'     => $this->setting_option_values['lesson_video_auto_complete'],
					'options'   => array(
						''   => '',
						'on' => '',
					),
					'help_text' => sprintf(
						// translators: placeholder: topic.
						esc_html_x( ' Automatically mark the %s as completed once the user has watched the full video.', 'placeholder: topic', 'learndash' ),
						learndash_get_custom_label_lower( 'topic' )
					),
					'rest'      => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_auto_complete',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Auto-complete', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_auto_complete_delay'  => array(
					'name'        => 'lesson_video_auto_complete_delay',
					'label'       => esc_html__( 'Completion delay', 'learndash' ),
					'type'        => 'number',
					'class'       => '-small',
					'default'     => 0,
					'value'       => $this->setting_option_values['lesson_video_auto_complete_delay'],
					'attrs'       => array(
						'step' => 1,
						'min'  => 0,
					),
					'input_label' => esc_html__( 'seconds', 'learndash' ),
					'help_text'   => sprintf(
						// translators: placeholder: topic.
						esc_html_x( 'Specify a delay between video completion and %s completion.', 'placeholder: topic', 'learndash' ),
						learndash_get_custom_label_lower( 'topic' )
					),
					'default'     => 0,
					'rest'        => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_auto_complete_delay',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Completion Delay (seconds).', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),
				'lesson_video_show_complete_button' => array(
					'name'      => 'lesson_video_show_complete_button',
					'label'     => esc_html__( 'Mark Complete Button', 'learndash' ),
					'type'      => 'checkbox-switch',
					'help_text' => sprintf(
						// translators: placeholder: lesson.
						esc_html_x( 'Display the Mark Complete button on a %s even if not yet clickable.', 'placeholder: lesson', 'learndash' ),
						learndash_get_custom_label_lower( 'lesson' )
					),
					'value'     => $this->setting_option_values['lesson_video_show_complete_button'],
					'default'   => '',
					'options'   => array(
						'on' => '',
					),
					'rest'      => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_show_complete_button',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Show Mark Complete Button', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['video_display_timing_after_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'assignment_upload_limit_count'      => array(
					'name'        => 'assignment_upload_limit_count',
					'label'       => esc_html__( 'Limit number of uploaded files', 'learndash' ),
					'type'        => 'number',
					'value'       => $this->setting_option_values['assignment_upload_limit_count'],
					'default'     => '1',
					'class'       => 'small-text',
					'input_label' => esc_html__( 'file(s) maximum', 'learndash' ),
					'attrs'       => array(
						'step' => 1,
						'min'  => 1,
					),
					'help_text'   => esc_html__( 'Specify the maximum number of files a user can upload for this assignment.', 'learndash' ),
					'rest'        => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Assignment Upload Count Limit.', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),
				'lesson_assignment_deletion_enabled' => array(
					'name'      => 'lesson_assignment_deletion_enabled',
					'label'     => esc_html__( 'Allow file deletion', 'learndash' ),
					'type'      => 'checkbox-switch',
					'value'     => $this->setting_option_values['lesson_assignment_deletion_enabled'],
					'default'   => '',
					'help_text' => esc_html__( 'Allow the user to delete their own uploaded files. This is only possible up until the assignment has been approved.', 'learndash' ),
					'options'   => array(
						'on' => '',
					),
					'default'   => 0,
					'rest'      => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_deletion_enabled',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Assignment Allow File Deletion.', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['lesson_assignment_grading_manual_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'topic_materials_enabled'            => array(
					'name'                => 'topic_materials_enabled',
					'type'                => 'checkbox-switch',
					'label'               => sprintf(
						// translators: placeholder: Topic.
						esc_html_x( '%s Materials', 'placeholder: Topic', 'learndash' ),
						learndash_get_custom_label( 'topic' )
					),
					'help_text'           => sprintf(
						// translators: placeholder: topic, topic.
						esc_html_x( 'List and display support materials for the %1$s. This is visible to any user having access to the %2$s.', 'placeholder: topic, topic', 'learndash' ),
						learndash_get_custom_label_lower( 'topic' ),
						learndash_get_custom_label_lower( 'topic' )
					),
					'value'               => $this->setting_option_values['topic_materials_enabled'],
					'default'             => '',
					'options'             => array(
						'on' => sprintf(
							// translators: placeholder: topic.
							esc_html_x( 'Any content added below is displayed on the %s page', 'placeholder: topic', 'learndash' ),
							learndash_get_custom_label_lower( 'topic' )
						),
						''   => '',
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['topic_materials_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'materials_enabled',
								'description' => esc_html__( 'Materials Eabled', 'learndash' ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'topic_materials'                    => array(
					'name'           => 'topic_materials',
					'type'           => 'wpeditor',
					'parent_setting' => 'topic_materials_enabled',
					'value'          => $this->setting_option_values['topic_materials'],
					'default'        => '',
					'placeholder'    => esc_html__( 'Add a list of needed documents or URLs. This field supports HTML.', 'learndash' ),
					'editor_args'    => array(
						'textarea_name' => $this->settings_metabox_key . '[topic_materials]',
						'textarea_rows' => 3,
					),
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'materials',
								'description' => esc_html__( 'Materials', 'learndash' ),
								'type'        => 'string',
								'default'     => '',
								'properties'  => array(
									'raw'      => array(
										'description' => 'Content for the object, as it exists in the database.',
										'type'        => 'string',
										'context'     => array( 'edit' ),
									),
									'rendered' => array(
										'description' => 'HTML content for the object, transformed for display.',
										'type'        => 'string',
										'context'     => array( 'view', 'edit' ),
										'readonly'    => true,
									),
								),
							),
						),
					),
				),
				'lesson_video_enabled'               => array(
					'name'                => 'lesson_video_enabled',
					'label'               => esc_html__( 'Video Progression', 'learndash' ),
					'type'                => 'checkbox-switch',
					'help_text'           => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'Require users to watch the full video as part of the %s progression. Use shortcode [ld_video] to move within the post content.', 'placeholder: Course', 'learndash' ),
						learndash_get_custom_label_lower( 'course' )
					),
					'value'               => $this->setting_option_values['lesson_video_enabled'],
					'default'             => '',
					'options'             => array(
						''   => '',
						'on' => array(
							'label'       => sprintf(
								// translators: placeholder: Course.
								esc_html_x( 'The below video is tied to %s progression', 'placeholder: Course', 'learndash' ),
								learndash_get_custom_label_lower( 'course' )
							),
							'description' => '',
							'tooltip'     => sprintf(
								// translators: placeholder: Topic.
								esc_html_x( 'Cannot be enabled while %s timer or Assignments are enabled', 'placeholder: Topic', 'learndash' ),
								learndash_get_custom_label( 'topic' )
							),
						),
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['lesson_video_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_enabled',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Progression Eabled', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_url'                   => array(
					'name'           => 'lesson_video_url',
					'label'          => esc_html__( 'Video URL', 'learndash' ),
					'type'           => 'textarea',
					'class'          => 'full-text',
					'value'          => $this->setting_option_values['lesson_video_url'],
					'default'        => '',
					'placeholder'    => esc_html__( 'Input URL, iFrame, or shortcode here.', 'learndash' ),
					'attrs'          => array(
						'rows' => '1',
						'cols' => '57',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_url',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Progression URL', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'text',
								'default'     => '',
							),
						),
					),
				),
				'lesson_video_shown'                 => array(
					'name'           => 'lesson_video_shown',
					'label'          => esc_html__( 'Display Timing', 'learndash' ),
					'type'           => 'radio',
					'value'          => $this->setting_option_values['lesson_video_shown'],
					'default'        => 'AFTER',
					'parent_setting' => 'lesson_video_enabled',
					'options'        => array(
						'BEFORE' => array(
							'label'       => esc_html__( 'Before completed sub-steps', 'learndash' ),
							'description' => sprintf(
								// translators: placeholder: topic.
								esc_html_x( 'The video will be shown and must be fully watched before the user can access the %s’s associated steps.', 'placeholder: topic', 'learndash' ),
								learndash_get_custom_label_lower( 'topic' )
							),
						),
						'AFTER'  => array(
							'label'               => esc_html__( 'After completing sub-steps', 'learndash' ),
							'description'         => sprintf(
								// translators: placeholder: topic, topic.
								esc_html_x( 'The video will be visible after the user has completed the %1$s’s associated steps. The full video must be watched in order to complete the %2$s.', 'placeholder: topic, topic', 'learndash' ),
								learndash_get_custom_label_lower( 'topic' ),
								learndash_get_custom_label_lower( 'topic' )
							),
							'inline_fields'       => array(
								'lesson_video_display_timing_after' => $this->settings_sub_option_fields['video_display_timing_after_fields'],
							),
							'inner_section_state' => ( 'AFTER' === $this->setting_option_values['lesson_video_shown'] ) ? 'open' : 'closed',
						),
					),
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_shown',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Shown before or after sub-steps', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'default'     => 'ANY',
								'type'        => 'string',
								'enum'        => array(
									'BEFORE',
									'AFTER',
								),
							),
						),
					),
				),
				'lesson_video_auto_start'            => array(
					'name'           => 'lesson_video_auto_start',
					'label'          => esc_html__( 'Autostart', 'learndash' ),
					'type'           => 'checkbox-switch',
					'value'          => $this->setting_option_values['lesson_video_auto_start'],
					'help_text'      => esc_html__( 'Note, due to browser requirements videos will be automatically muted for autoplay to work.', 'learndash' ),
					'default'        => '',
					'options'        => array(
						'on' => esc_html__( 'The video now starts automatically on page load', 'learndash' ),
						''   => '',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_auto_start',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Autostart', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_show_controls'         => array(
					'name'           => 'lesson_video_show_controls',
					'label'          => esc_html__( 'Video Controls Display', 'learndash' ),
					'type'           => 'checkbox-switch',
					'help_text'      => esc_html__( 'Only available for YouTube and local videos.', 'learndash' ),
					'value'          => $this->setting_option_values['lesson_video_show_controls'],
					'default'        => '',
					'options'        => array(
						''   => '',
						'on' => esc_html__( 'Users can pause, move backward and forward within the video', 'learndash' ),
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_show_controls',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Video Controls Display. YouTube and local videos only', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_focus_pause'           => array(
					'name'           => 'lesson_video_focus_pause',
					'label'          => esc_html__( 'Video Pause on Window Unfocused', 'learndash' ),
					'type'           => 'checkbox-switch',
					'value'          => $this->setting_option_values['lesson_video_focus_pause'],
					'help_text'      => esc_html__( 'Pause the video if user switches to a different window. VooPlayer not supported.', 'learndash' ),
					'default'        => '',
					'options'        => array(
						'on' => '',
						''   => '',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key' => 'video_focus_pause',
								'type'      => 'boolean',
								'default'   => false,
							),
						),
					),
				),
				'lesson_video_track_time'            => array(
					'name'           => 'lesson_video_track_time',
					'label'          => esc_html__( 'Video Resume', 'learndash' ),
					'type'           => 'checkbox-switch',
					'value'          => $this->setting_option_values['lesson_video_track_time'],
					'help_text'      => esc_html__( 'Allows user to resume video position. Uses browser cookie. VooPlayer not supported.', 'learndash' ),
					'default'        => '',
					'options'        => array(
						'on' => '',
						''   => '',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key' => 'video_resume',
								'type'      => 'boolean',
								'default'   => false,
							),
						),
					),
				),

				'lesson_assignment_upload'           => array(
					'name'                => 'lesson_assignment_upload',
					'label'               => esc_html__( 'Assignment Uploads', 'learndash' ),
					'type'                => 'checkbox-switch',
					'default'             => '',
					'value'               => $this->setting_option_values['lesson_assignment_upload'],
					'options'             => array(
						'on' => array(
							'label'       => '',
							'description' => '',
							'tooltip'     => sprintf(
								// translators: placeholder: topic.
								esc_html_x( 'Cannot be enabled while %s timer or Video progression are enabled', 'placeholder: toic', 'learndash' ),
								learndash_get_custom_label_lower( 'topic' )
							),
						),
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['lesson_assignment_upload'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_upload_enabled',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Assignment Uploads Enabled', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'assignment_upload_limit_extensions' => array(
					'name'           => 'assignment_upload_limit_extensions',
					'label'          => esc_html__( 'File Extensions', 'learndash' ),
					'type'           => 'text',
					'placeholder'    => esc_html__( 'pdf, xls, zip', 'learndash' ),
					'help_text'      => esc_html__( 'Specify the type of files users can upload. Leave blank for any.', 'learndash' ),
					'class'          => '-small',
					'default'        => '',
					'value'          => $this->setting_option_values['assignment_upload_limit_extensions'],
					'parent_setting' => 'lesson_assignment_upload',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Assignment Allowed file extensions. Comma separated pdf, xls, zip', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'text',
								'default'     => '',
							),
						),
					),
				),
				'assignment_upload_limit_size'       => array(
					'name'           => 'assignment_upload_limit_size',
					'label'          => esc_html__( 'File Size Limit', 'learndash' ),
					'type'           => 'text',
					'class'          => '-small',
					'placeholder'    => ini_get( 'upload_max_filesize' ),
					'help_text'      => sprintf(
						// translators: placeholder: PHP file upload size.
						esc_html_x( 'Default maximum file size supported is: %s', 'placeholder: PHP file upload size', 'learndash' ),
						ini_get( 'upload_max_filesize' )
					),
					'default'        => '',
					'value'          => $this->setting_option_values['assignment_upload_limit_size'],
					'parent_setting' => 'lesson_assignment_upload',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%1$s Assignment Upload size limit. Max per server is %2$s ', 'placeholder: Topic, Upload size limit', 'learndash' ), learndash_get_custom_label( 'topic' ), ini_get( 'upload_max_filesize' ) ),
								'type'        => 'text',
								'default'     => '',
							),
						),
					),
				),

				'lesson_assignment_points_enabled'   => array(
					'name'                => 'lesson_assignment_points_enabled',
					'label'               => esc_html__( 'Points', 'learndash' ),
					'type'                => 'checkbox-switch',
					'default'             => 0,
					'value'               => $this->setting_option_values['lesson_assignment_points_enabled'],
					'options'             => array(
						'on' => esc_html__( 'Award points for submitting assignments', 'learndash' ),
						''   => '',
					),
					'parent_setting'      => 'lesson_assignment_upload',
					'child_section_state' => ( 'on' === $this->setting_option_values['lesson_assignment_points_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_points_enabled',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Assignment Points Enabled', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_assignment_points_amount'    => array(
					'name'           => 'lesson_assignment_points_amount',
					'type'           => 'number',
					'class'          => '-small',
					'attrs'          => array(
						'step' => 1,
						'min'  => 0,
					),
					'default'        => 0,
					'value'          => $this->setting_option_values['lesson_assignment_points_amount'],
					'input_label'    => esc_html__( 'available point(s)', 'learndash' ),
					'parent_setting' => 'lesson_assignment_points_enabled',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_points_amount',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Assignment Points Amount', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),

				'auto_approve_assignment'            => array(
					'name'           => 'auto_approve_assignment',
					'label'          => esc_html__( 'Grading Type', 'learndash' ),
					'type'           => 'radio',
					'value'          => $this->setting_option_values['auto_approve_assignment'],
					'parent_setting' => 'lesson_assignment_upload',
					'options'        => array(
						'on' => array(
							'label'       => esc_html__( 'Auto-approve', 'learndash' ),
							'description' => esc_html__( 'No grading or approval needed. The assignment will be automatically approved and full points will be awarded.', 'learndash' ),
						),
						''   => array(
							'label'               => esc_html__( 'Manually grade', 'learndash' ),
							'description'         => sprintf(
								// translators: placeholder: Group, topic.
								esc_html_x( 'Admin or %1$s leader approval and grading required. The %2$s cannot be completed until the assignment is approved.', 'placeholder: Group, topic', 'learndash' ),
								learndash_get_custom_label( 'group' ),
								learndash_get_custom_label_lower( 'topic' )
							),
							'inline_fields'       => array(
								'lesson_assignment_grading_manual' => $this->settings_sub_option_fields['lesson_assignment_grading_manual_fields'],
							),
							'inner_section_state' => ( '' === $this->setting_option_values['auto_approve_assignment'] ) ? 'open' : 'closed',
						),
					),
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_auto_approve',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Assignment Auto-approve Enabled', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => true,
							),
						),
					),
				),

				'forced_lesson_time_enabled'         => array(
					'name'                => 'forced_lesson_time_enabled',
					'label'               => sprintf(
						// translators: placeholder: Topic.
						esc_html_x( '%s Timer', 'placeholder: Topic', 'learndash' ),
						learndash_get_custom_label( 'topic' )
					),
					'default'             => '',
					'type'                => 'checkbox-switch',
					'value'               => $this->setting_option_values['forced_lesson_time_enabled'],
					'help_text'           => sprintf(
						// translators: placeholder: topic.
						esc_html_x( 'The %s cannot be marked as completed until the set time has elapsed.', 'placeholder: topic', 'learndash' ),
						learndash_get_custom_label_lower( 'topic' )
					),
					'options'             => array(
						'on' => array(
							'label'       => '',
							'description' => '',
							'tooltip'     => esc_html__( 'Cannot be enabled while Video progression or Assignments are enabled', 'learndash' ),
						),
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['forced_lesson_time_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'forced_timer_enabled',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Time Enabled', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'forced_lesson_time'                 => array(
					'name'           => 'forced_lesson_time',
					'type'           => 'timer-entry',
					'class'          => 'small-text',
					'default'        => '',
					'value'          => $this->setting_option_values['forced_lesson_time'],
					'parent_setting' => 'forced_lesson_time_enabled',
					'rest'           => array(
						'show_in_rest' => LearnDash_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'forced_timer_amount',
								// translators: placeholder: Topic.
								'description' => sprintf( esc_html_x( '%s Timer Amount.', 'placeholder: Topic', 'learndash' ), learndash_get_custom_label( 'topic' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),
			);

			if ( 'yes' === LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) ) {
				unset( $this->setting_option_fields['course'] );
				unset( $this->setting_option_fields['lesson'] );
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_metabox_key );

			parent::load_settings_fields();
		}

		/**
		 * Update Metabox Settings values.
		 *
		 * @since 3.4.0
		 *
		 * @param array $settings_values Array of key/value settings changes.
		 */
		public function apply_metabox_settings_fields_changes( $settings_field_updates = array() ) {
			$settings_field_values = $this->get_settings_metabox_values();

			if ( ! empty( $settings_field_updates ) ) {
				$settings_changes_only = array();
				foreach ( $settings_field_updates as $setting_key => $setting_value ) {
					if ( isset( $settings_field_values[ $setting_key ] ) ) {
						$settings_changes_only[ $setting_key ] = $setting_value;
					}
				}

				if ( ! empty( $settings_changes_only ) ) {
					if ( ( isset( $settings_changes_only['lesson_video_enabled'] ) ) && ( 'on' === $settings_changes_only['lesson_video_enabled'] ) ) {
						$settings_changes_only['lesson_assignment_upload']   = '';
						$settings_changes_only['forced_lesson_time_enabled'] = '';
					} elseif ( ( isset( $settings_changes_only['lesson_assignment_upload'] ) ) && ( 'on' === $settings_changes_only['lesson_assignment_upload'] ) ) {
						$settings_changes_only['lesson_video_enabled']       = '';
						$settings_changes_only['forced_lesson_time_enabled'] = '';
					} elseif ( ( isset( $settings_changes_only['forced_lesson_time_enabled'] ) ) && ( 'on' === $settings_changes_only['forced_lesson_time_enabled'] ) ) {
						$settings_changes_only['lesson_video_enabled']     = '';
						$settings_changes_only['lesson_assignment_upload'] = '';
					} else {
						$settings_changes_only['lesson_video_enabled']       = '';
						$settings_changes_only['lesson_assignment_upload']   = '';
						$settings_changes_only['forced_lesson_time_enabled'] = '';
					}

					foreach ( $settings_changes_only as $setting_key => $setting_value ) {
						if ( isset( $settings_field_values[ $setting_key ] ) ) {
							$settings_field_values[ $setting_key ] = $setting_value;
						}
					}
				}
			}

			return $settings_field_values;
		}

		/**
		 * Filter settings values for metabox before save to database.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $settings_values Array of settings values.
		 * @param string $settings_metabox_key Metabox key.
		 * @param string $settings_screen_id Screen ID.
		 *
		 * @return array $settings_values.
		 */
		public function filter_saved_fields( $settings_values = array(), $settings_metabox_key = '', $settings_screen_id = '' ) {
			if ( ( $settings_screen_id === $this->settings_screen_id ) && ( $settings_metabox_key === $this->settings_metabox_key ) ) {

				if ( ( 'on' !== $settings_values['topic_materials_enabled'] ) || ( empty( $settings_values['topic_materials'] ) ) ) {
					$settings_values['topic_materials_enabled'] = '';
					$settings_values['topic_materials']         = '';
				}

				// If video progression is enables but the video URL is empty then turn off video progression.
				if ( ( 'on' !== $settings_values['lesson_video_enabled'] ) || ( empty( $settings_values['lesson_video_url'] ) ) ) {
					$settings_values['lesson_video_enabled'] = '';
					$settings_values['lesson_video_url']     = '';
				}

				if ( ( 'on' !== $settings_values['forced_lesson_time_enabled'] ) || ( empty( $settings_values['forced_lesson_time'] ) ) ) {
					$settings_values['forced_lesson_time_enabled'] = '';
					$settings_values['forced_lesson_time']         = '';
				}

				if ( ( 'on' !== $settings_values['lesson_assignment_points_enabled'] ) || ( empty( $settings_values['lesson_assignment_points_amount'] ) ) ) {
					$settings_values['lesson_assignment_points_amount']  = '';
					$settings_values['lesson_assignment_points_enabled'] = '';
				}

				if ( 'on' === $settings_values['lesson_video_enabled'] ) {
					$settings_values['lesson_assignment_upload']   = '';
					$settings_values['forced_lesson_time_enabled'] = '';
				} elseif ( 'on' === $settings_values['lesson_assignment_upload'] ) {
					$settings_values['lesson_video_enabled']       = '';
					$settings_values['forced_lesson_time_enabled'] = '';
				} elseif ( 'on' === $settings_values['forced_lesson_time_enabled'] ) {
					$settings_values['lesson_video_enabled']     = '';
					$settings_values['lesson_assignment_upload'] = '';
				} else {
					$settings_values['lesson_video_enabled']       = '';
					$settings_values['lesson_assignment_upload']   = '';
					$settings_values['forced_lesson_time_enabled'] = '';
				}

				if ( 'on' !== $settings_values['lesson_video_enabled'] ) {
					$settings_values['lesson_video_url']                  = '';
					$settings_values['lesson_video_shown']                = '';
					$settings_values['lesson_video_auto_start']           = '';
					$settings_values['lesson_video_show_controls']        = '';
					$settings_values['lesson_video_focus_pause']          = '';
					$settings_values['lesson_video_track_time']           = '';
					$settings_values['lesson_video_auto_complete']        = '';
					$settings_values['lesson_video_auto_complete_delay']  = '';
					$settings_values['lesson_video_show_complete_button'] = '';
					$settings_values['lesson_video_hide_complete_button'] = '';
				}

				if ( 'on' !== $settings_values['lesson_assignment_upload'] ) {
					$settings_values['assignment_upload_limit_extensions'] = '';
					$settings_values['assignment_upload_limit_size']       = '';
					$settings_values['lesson_assignment_points_enabled']   = '';
					$settings_values['lesson_assignment_points_amount']    = '';
					$settings_values['assignment_upload_limit_count']      = '';
					$settings_values['lesson_assignment_deletion_enabled'] = '';
					$settings_values['auto_approve_assignment']            = '';
				}

				if ( 'on' !== $settings_values['forced_lesson_time_enabled'] ) {
					$settings_values['forced_lesson_time_enabled'] = '';
					$settings_values['forced_lesson_time']         = '';
				}

				if ( 'on' === $settings_values['lesson_video_enabled'] ) {
					if ( ( 'on' === $settings_values['lesson_video_show_complete_button'] ) ) {
						$settings_values['lesson_video_hide_complete_button'] = '';
					} else {
						$settings_values['lesson_video_hide_complete_button'] = 'on';
					}
				}

				if ( 'on' === $settings_values['lesson_assignment_upload'] ) {
					if ( ! empty( $settings_values['assignment_upload_limit_extensions'] ) ) {
						$settings_values['assignment_upload_limit_extensions'] = learndash_validate_extensions( $settings_values['assignment_upload_limit_extensions'] );
					}

					if ( ! empty( $settings_values['assignment_upload_limit_size'] ) ) {
						$limit_file_size    = learndash_return_bytes_from_shorthand( $settings_values['assignment_upload_limit_size'] );
						$wp_limit_file_size = wp_max_upload_size();

						if ( $limit_file_size > $wp_limit_file_size ) {
							$settings_values['assignment_upload_limit_size'] = '';
						}
					}
				}
			}

			return $settings_values;
		}

		// End of functions.
	}

	add_filter(
		'learndash_post_settings_metaboxes_init_' . learndash_get_post_type_slug( 'topic' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['LearnDash_Settings_Metabox_Topic_Display_Content'] ) ) && ( class_exists( 'LearnDash_Settings_Metabox_Topic_Display_Content' ) ) ) {
				$metaboxes['LearnDash_Settings_Metabox_Topic_Display_Content'] = LearnDash_Settings_Metabox_Topic_Display_Content::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
