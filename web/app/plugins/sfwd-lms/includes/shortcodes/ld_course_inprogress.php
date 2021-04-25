<?php
/**
 * Shortcode for course_inprogress
 *
 * @since 2.1.0
 *
 * @package LearnDash\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `course_inprogress` shortcode output.
 *
 * Shortcode that shows the content if the user is in progress on the course.
 *
 * @global boolean $learndash_shortcode_used
 *
 * @since 2.1.0
 *
 * @param array  $atts {
 *    An array of shortcode attributes.
 *
 *    @type string  $content    Optional. The shortcode content. Default empty.
 *    @type int     $course_id  Optional. Course ID. Default false.
 *    @type int     $user_id    Optional. User ID. Default false.
 *    @type boolean $autop      Optional. Whether to replace linebreaks with paragraph elements. Default true.
 * }
 * @param string $content The shortcode content.
 *
 * @return string The `course_inprogress` shortcode output.
 */
function learndash_course_inprogress_shortcode( $atts = array(), $content = '' ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;

	if ( ! empty( $content ) ) {

		if ( ! is_array( $atts ) ) {
			if ( ! empty( $atts ) ) {
				$atts = array( $atts );
			} else {
				$atts = array();
			}
		}

		$defaults = array(
			'content'   => $content,
			'course_id' => false,
			'user_id'   => false,
			'autop'     => true,
		);
		$atts     = wp_parse_args( $atts, $defaults );
		if ( ( true === $atts['autop'] ) || ( 'true' === $atts['autop'] ) || ( '1' === $atts['autop'] ) ) {
			$atts['autop'] = true;
		} else {
			$atts['autop'] = false;
		}

		/**
		 * Filters `course_inprogress` shortcode attributes.
		 *
		 * @param array $attributes An array of course_inprogress shortcode attributes.
		 */
		$atts = apply_filters( 'learndash_course_inprogress_shortcode_atts', $atts );

		$atts['content'] = learndash_course_status_content_shortcode( $atts, $atts['content'], esc_html__( 'In Progress', 'learndash' ) );
		return SFWD_LMS::get_template(
			'learndash_course_inprogress_message',
			array(
				'shortcode_atts' => $atts,
			),
			false
		);
	}
}

add_shortcode( 'course_inprogress', 'learndash_course_inprogress_shortcode' );
