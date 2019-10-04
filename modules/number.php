<?php
/**
** A base module for the following types of tags:
** 	[number] and [number*]		# Number
** 	[range] and [range*]		# Range
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_number', 10, 0 );

function wpcf7_add_form_tag_number() {
	wpcf7_add_form_tag( array( 'number', 'number*', 'range', 'range*' ),
		'wpcf7_number_form_tag_handler', array( 'name-attr' => true ) );
}

function wpcf7_number_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	$class .= ' wpcf7-validates-as-number';

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
	$atts['min'] = $tag->get_option( 'min', 'signed_int', true );
	$atts['max'] = $tag->get_option( 'max', 'signed_int', true );

	if ( $tag->has_option( 'readonly' ) ) {
		$atts['readonly'] = 'readonly';
	}

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$value = (string) reset( $tag->values );

	if ( $tag->has_option( 'placeholder' )
	or $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = $tag->get_default_option( $value );

	$value = wpcf7_get_hangover( $tag->name, $value );

	$atts['value'] = $value;

	if ( wpcf7_support_html5() ) {
		$atts['type'] = $tag->basetype;
	} else {
		$atts['type'] = 'text';
	}

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_number', 'wpcf7_number_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_number*', 'wpcf7_number_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_range', 'wpcf7_number_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_range*', 'wpcf7_number_validation_filter', 10, 2 );

function wpcf7_number_validation_filter( $result, $tag ) {
	$name = $tag->name;

	$value = isset( $_POST[$name] )
		? trim( strtr( (string) $_POST[$name], "\n", " " ) )
		: '';

	$min = $tag->get_option( 'min', 'signed_int', true );
	$max = $tag->get_option( 'max', 'signed_int', true );

	if ( $tag->is_required() and '' == $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	} elseif ( '' != $value and ! wpcf7_is_number( $value ) ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_number' ) );
	} elseif ( '' != $value and '' != $min and (float) $value < (float) $min ) {
		$result->invalidate( $tag, wpcf7_get_message( 'number_too_small' ) );
	} elseif ( '' != $value and '' != $max and (float) $max < (float) $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'number_too_large' ) );
	}

	return $result;
}


/* Messages */

add_filter( 'wpcf7_messages', 'wpcf7_number_messages', 10, 1 );

function wpcf7_number_messages( $messages ) {
	return array_merge( $messages, array(
		'invalid_number' => array(
			'description' => __( "Number format that the sender entered is invalid", 'valhalla-contact-form' ),
			'default' => __( "The number format is invalid.", 'valhalla-contact-form' )
		),

		'number_too_small' => array(
			'description' => __( "Number is smaller than minimum limit", 'valhalla-contact-form' ),
			'default' => __( "The number is smaller than the minimum allowed.", 'valhalla-contact-form' )
		),

		'number_too_large' => array(
			'description' => __( "Number is larger than maximum limit", 'valhalla-contact-form' ),
			'default' => __( "The number is larger than the maximum allowed.", 'valhalla-contact-form' )
		),
	) );
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_number', 18, 0 );

function wpcf7_add_tag_generator_number() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'number', __( 'number', 'valhalla-contact-form' ),
		'wpcf7_tag_generator_number' );
}

function wpcf7_tag_generator_number( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'number';

	$description = __( "Generate a field for numeric value input. For more details, see %s.", 'valhalla-contact-form' );

	$desc_link = wpcf7_link( __( 'https://drive.google.com/open?id=1yWQ1kFJHHeGvyE5HJTh6sS4bYKs3Twoj', 'valhalla-contact-form' ), __( 'Form Builder', 'valhalla-contact-form' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'valhalla-contact-form' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'valhalla-contact-form' ) ); ?></legend>
		<select name="tagtype">
			<option value="number" selected="selected"><?php echo esc_html( __( 'Spinbox', 'valhalla-contact-form' ) ); ?></option>
			<option value="range"><?php echo esc_html( __( 'Slider', 'valhalla-contact-form' ) ); ?></option>
		</select>
		<br />
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'valhalla-contact-form' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'valhalla-contact-form' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Default value', 'valhalla-contact-form' ) ); ?></label></th>
	<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
	<label><input type="checkbox" name="placeholder" class="option" /> <?php echo esc_html( __( 'Use this text as the placeholder of the field', 'valhalla-contact-form' ) ); ?></label></td>
	</tr>

	<tr>
	<th scope="row"><?php echo esc_html( __( 'Range', 'valhalla-contact-form' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Range', 'valhalla-contact-form' ) ); ?></legend>
		<label>
		<?php echo esc_html( __( 'Min', 'valhalla-contact-form' ) ); ?>
		<input type="number" name="min" class="numeric option" />
		</label>
		&ndash;
		<label>
		<?php echo esc_html( __( 'Max', 'valhalla-contact-form' ) ); ?>
		<input type="number" name="max" class="numeric option" />
		</label>
		</fieldset>
	</td>
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
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'valhalla-contact-form' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'valhalla-contact-form' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
