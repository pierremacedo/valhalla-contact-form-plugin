<?php

class WPCF7_TagGenerator {

	private static $instance;

	private $panels = array();

	private function __construct() {}

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function add( $id, $title, $callback, $options = array() ) {
		$id = trim( $id );

		if ( '' === $id
		or ! wpcf7_is_name( $id ) ) {
			return false;
		}

		$this->panels[$id] = array(
			'title' => $title,
			'content' => 'tag-generator-panel-' . $id,
			'options' => $options,
			'callback' => $callback,
		);

		return true;
	}

	public function print_buttons() {
		echo '<span id="tag-generator-list">';
        echo '<a id="addrow" type="button" class="button" onclick="addRows()">Add a Row</a>';
        echo '<a id="addlabel" type="button" class="button" onclick="addLabels()">Add a Label </a>';

		foreach ( (array) $this->panels as $panel ) {
			echo sprintf(
				'<a href="#TB_inline?width=900&height=500&inlineId=%1$s" class="thickbox button" title="%2$s">%3$s</a>',
				esc_attr( $panel['content'] ),
				/* translators: %s: title of form-tag like 'email' or 'checkboxes' */
				esc_attr( sprintf(
					__( 'Form Builder: %s', 'valhalla-contact-form' ),
					$panel['title'] ) ),
				esc_html( $panel['title'] )
			);
		}

		echo '</span>';
        echo '<div id="row-options" class="hide-options">'; 
        echo '<h3>Choose an option:</h3>';
        echo '<label><input name="choice" type="radio"   value="1" onclick="countColumns(this.value)" />One column</label>';
        echo '  <label><input name="choice" type="radio" value="2" onclick="countColumns(this.value)" />Two columns</label>';
        echo '</div>';
        echo '<div id="label-options" class="hide-options">'; 
        echo '<h3>Choose an option:</h3>';
        echo '<label><input name="label-choice" type="radio"   value="name" onclick="getLabelOption(this.value)" />Name</label>';
        echo '  <label><input name="label-choice" type="radio" value="email" onclick="getLabelOption(this.value)" />Email</label>';
        echo '  <label><input name="label-choice" type="radio" value="phone" onclick="getLabelOption(this.value)" />Phone</label>';
        echo '  <label><input name="label-choice" type="radio" value="subject" onclick="getLabelOption(this.value)" />Subject</label>';
        echo '  <label><input name="label-choice" type="radio" value="message" onclick="getLabelOption(this.value)" />Message</label>';
        echo '  <label><input name="label-choice" type="radio" value="blank" onclick="getLabelOption(this.value)" />Blank</label>';
        echo '</div>';
       
	}

	public function print_panels( WPCF7_ContactForm $contact_form ) {
		foreach ( (array) $this->panels as $id => $panel ) {
			$callback = $panel['callback'];

			$options = wp_parse_args( $panel['options'], array() );
			$options = array_merge( $options, array(
				'id' => $id,
				'title' => $panel['title'],
				'content' => $panel['content'],
			) );

			if ( is_callable( $callback ) ) {
				echo sprintf( '<div id="%s" class="hidden">',
					esc_attr( $options['content'] ) );
				echo sprintf(
					'<form action="" class="tag-generator-panel" data-id="%s">',
					$options['id'] );

				call_user_func( $callback, $contact_form, $options );

				echo '</form></div>';
			}
		}
	}

}
