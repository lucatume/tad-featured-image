<?php
/**
 * Plugin Name: Featured Image Labels
 * Plugin URI: http://theAverageDev.com
 * Description: A utility to rename the built-in featured image meta box link labels.
 * Version: 1.0
 * Author: Modern Tribe
 * Author URI: http://tri.be
 */

if ( ! function_exists( 'is_post_type' ) ) {
	/**
	 * Ajax-aware method to check for the current post type.
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 */
	function is_post_type( $post_type ) {
		if ( empty( $post_type ) ) {
			return false;
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			if ( get_post_type() != $post_type ) {
				return false;
			}
		} else {
			if ( empty( $_POST['post_id'] ) ) {
				return false;
			}
			if ( ! get_post_type( $_POST['post_id'] ) == $post_type ) {
				return false;
			}
		}

		return true;
	}
}

if ( ! class_exists( 'tad_Featured_Image' ) ) {
	/**
	 * Class tad_Featured_Image
	 *
	 *
	 */
	class tad_Featured_Image {

		/**
		 * The default english label for the set featured image link.
		 *
		 * @var string
		 */
		protected $default_set = 'Set featured image';

		/**
		 * The default english label for the remove featured image link.
		 *
		 * @var string
		 */
		protected $default_remove = 'Remove featured image';

		/**
		 * @var string
		 */
		public $post_type;

		/**
		 * The featured image meta box context, `side` by default.
		 *
		 * @var string
		 */
		public $context = 'side';

		/**
		 * The featured image meta box priority, `default` by default.
		 *
		 * @var string
		 */
		public $priority = 'default';
		/**
		 * The text that will replace the Featured Image meta box title.
		 *
		 * @var string
		 */
		protected $title;

		/**
		 * The text that will replace the "Set featured image" text.
		 *
		 * @var string
		 */
		protected $set_label;

		/**
		 * The text that will replace the "Remove featured image" text.
		 *
		 * @var string
		 */
		protected $remove_label;

		/**
		 * @param string     $post_type
		 * @param array|null $args An array of replaceable fields and labels, all of them optionals.
		 *
		 *     string $title        The Featured Image meta box title, defaults to default title.
		 *     string $set_label    The "Set featured image" link label, defaults to default label.
		 *     string $remove_label The "Remove featured image" link label, defaults to default label.
		 *     string $context      The new context of the Featured Image meta box, defaults to `side`.
		 *     string $priority     The new priority of the Featured Image meta box, defaults to `default`.
		 *
		 * @return void
		 */
		public static function set_labels( $post_type, array $args = null ) {
			$instance = new self;

			$instance->post_type = $post_type;

			if ( ! empty( $args ) ) {
				$instance->title        = empty( $args['title'] ) ? null : $args['title'];
				$instance->set_label    = empty( $args['set_label'] ) ? null : $args['set_label'];
				$instance->remove_label = empty( $args['remove_label'] ) ? null : $args['remove_label'];
				$instance->context      = empty( $args['context'] ) ? $instance->context : $args['context'];
				$instance->priority     = empty( $args['priority'] ) ? $instance->priority : $args['priority'];
			}

			$instance->hooks();
		}

		private function hooks() {
			add_action( 'do_meta_boxes', [ $this, 'replace_title' ], 99, 1 );
			add_filter( 'gettext', [ $this, 'replace_labels' ], 99, 2 );
		}

		/**
		 * Replaces the built-in Featured Image meta box title.
		 */
		public function replace_title() {
			if ( is_post_type( $this->post_type ) && isset( $this->title ) ) {
				remove_meta_box( 'postimagediv', $this->post_type, 'side' );
				add_meta_box( 'postimagediv', $this->title, 'post_thumbnail_meta_box', $this->post_type, $this->context, $this->priority );
			}
		}

		/**
		 * Replaces the set and remove Featured Image meta box labels.
		 *
		 * @param $translation
		 * @param $text
		 *
		 * @return string
		 */
		public function replace_labels( $translation, $text ) {
			if ( ! is_post_type( $this->post_type ) ) {
				return $translation;
			}

			if ( ! in_array( $text, [ $this->default_set, $this->default_remove ] ) ) {
				return $translation;
			}

			if ( $text == $this->default_set && isset( $this->set_label ) ) {
				$translation = $this->set_label;
			} else if ( $text == $this->default_remove && isset( $this->remove_label ) ) {
				$translation = $this->remove_label;
			}

			return $translation;
		}
	}
}
