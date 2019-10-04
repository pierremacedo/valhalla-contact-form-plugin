<?php
/**
** A base module for [submit]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_submit', 10, 0 );

function wpcf7_add_form_tag_submit() {
	wpcf7_add_form_tag( 'submit', 'wpcf7_submit_form_tag_handler' );
}

function wpcf7_submit_form_tag_handler( $tag ) {
	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) ) {
		$value = __( 'Send', 'valhalla-contact-form' );
	}

	$atts['type'] = 'submit';
	$atts['value'] = $value;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<input %1$s />', $atts );

	return $html;
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_submit', 55, 0 );

function wpcf7_add_tag_generator_submit() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'submit', __( 'submit', 'valhalla-contact-form' ),
		'wpcf7_tag_generator_submit', array( 'nameless' => 1 ) );
}

function wpcf7_tag_generator_submit( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );

	$description = __( "Generate a submit button. For more details, see %s.", 'valhalla-contact-form' );

	$desc_link = wpcf7_link( __( 'https://drive.google.com/open?id=1yWQ1kFJHHeGvyE5HJTh6sS4bYKs3Twoj', 'valhalla-contact-form' ), __( 'Form Builder', 'valhalla-contact-form' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Label', 'valhalla-contact-form' ) ); ?></label></th>
	<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'valhalla-contact-form' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'valhalla-contact-form' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="submit" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'valhalla-contact-form' ) ); ?>" />
	</div>
</div>
<?php
}
