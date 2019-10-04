<?php
/**
** A base module for [checkbox], [checkbox*], and [radio]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_checkbox', 10, 0 );

function wpcf7_add_form_tag_checkbox() {
	wpcf7_add_form_tag( array( 'checkbox', 'checkbox*', 'radio' ),
		'wpcf7_checkbox_form_tag_handler',
		array(
			'name-attr' => true,
			'selectable-values' => true,
			'multiple-controls-container' => true,
		)
	);
}

function wpcf7_checkbox_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$label_first = $tag->has_option( 'label_first' );
	$exclusive = $tag->has_option( 'exclusive' );
	$multiple = false;

	if ( 'checkbox' == $tag->basetype ) {
		$multiple = ! $exclusive;
	} else { // radio
		$exclusive = false;
	}

	if ( $exclusive ) {
		$class .= ' wpcf7-exclusive-checkbox';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();

	$tabindex = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( false !== $tabindex ) {
		$tabindex = (int) $tabindex;
	}

	$html = '';
	$count = 0;

	if ( $data = (array) $tag->get_data_option() ) {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
	}

	$values = $tag->values;
	$labels = $tag->labels;

	$default_choice = $tag->get_default_option( null, array(
		'multiple' => $multiple,
	) );

	$hangover = wpcf7_get_hangover( $tag->name, $multiple ? array() : '' );

	foreach ( $values as $key => $value ) {
		if ( $hangover ) {
			$checked = in_array( $value, (array) $hangover, true );
		} else {
			$checked = in_array( $value, (array) $default_choice, true );
		}

		if ( isset( $labels[$key] ) ) {
			$label = $labels[$key];
		} else {
			$label = $value;
		}

		$item_atts = array(
			'type' => $tag->basetype,
			'name' => $tag->name . ( $multiple ? '[]' : '' ),
			'value' => $value,
			'checked' => $checked ? 'checked' : '',
			'tabindex' => false !== $tabindex ? $tabindex : '',
		);

		$item_atts = wpcf7_format_atts( $item_atts );

		if ( $label_first ) { // put label first, input last
			$item = sprintf(
				'<span class="vhcf-check-label-before">%1$s</span><input %2$s class="form-check-input" />',
				esc_html( $label ), $item_atts );
		} else {
			$item = sprintf(
				'<input %2$s class="form-check-input" /><span class="vhcf-check-label-after">%1$s</span>',
				esc_html( $label ), $item_atts );
		}

		if ( false !== $tabindex
		and 0 < $tabindex ) {
			$tabindex += 1;
		}

		$class = 'wpcf7-list-item form-check';
		$count += 1;

		if ( 1 == $count ) {
			$class .= ' first';
		}

		if ( count( $values ) == $count ) { // last round
			$class .= ' last';

		}

		$item = '<span class="' . esc_attr( $class ) . '">' . $item . '</span>';
		$html .= $item;
	}

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_checkbox',
	'wpcf7_checkbox_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_checkbox*',
	'wpcf7_checkbox_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_radio',
	'wpcf7_checkbox_validation_filter', 10, 2 );

function wpcf7_checkbox_validation_filter( $result, $tag ) {
	$name = $tag->name;
	$is_required = $tag->is_required() || 'radio' == $tag->type;
	$value = isset( $_POST[$name] ) ? (array) $_POST[$name] : array();

	if ( $is_required and empty( $value ) ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	return $result;
}


/* Adding free text field */

add_filter( 'wpcf7_posted_data', 'wpcf7_checkbox_posted_data', 10, 1 );

function wpcf7_checkbox_posted_data( $posted_data ) {
	$tags = wpcf7_scan_form_tags(
		array( 'type' => array( 'checkbox', 'checkbox*', 'radio' ) ) );

	if ( empty( $tags ) ) {
		return $posted_data;
	}

	foreach ( $tags as $tag ) {
		if ( ! isset( $posted_data[$tag->name] ) ) {
			continue;
		}

		$posted_items = (array) $posted_data[$tag->name];

		$posted_data[$tag->name] = $posted_items;
	}

	return $posted_data;
}


/* Tag generator */

add_action( 'wpcf7_admin_init',
	'wpcf7_add_tag_generator_checkbox_and_radio', 30, 0 );

function wpcf7_add_tag_generator_checkbox_and_radio() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'checkbox', __( 'checkboxes', 'valhalla-contact-form' ),
		'wpcf7_tag_generator_checkbox' );
	$tag_generator->add( 'radio', __( 'radio buttons', 'valhalla-contact-form' ),
		'wpcf7_tag_generator_checkbox' );
}

function wpcf7_tag_generator_checkbox( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = $args['id'];

	if ( 'radio' != $type ) {
		$type = 'checkbox';
	}

	if ( 'checkbox' == $type ) {
		$description = __( "Generate a group of checkboxes. For more details, see %s.", 'valhalla-contact-form' );
	} elseif ( 'radio' == $type ) {
		$description = __( "Generate a group of radio buttons. For more details, see %s.", 'valhalla-contact-form' );
	}

	$desc_link = wpcf7_link( __( 'https://drive.google.com/open?id=1yWQ1kFJHHeGvyE5HJTh6sS4bYKs3Twoj', 'valhalla-contact-form' ), __( 'Form Builder', 'valhalla-contact-form' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
<?php if ( 'checkbox' == $type ) : ?>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'valhalla-contact-form' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'valhalla-contact-form' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'valhalla-contact-form' ) ); ?></label>
		</fieldset>
	</td>
	</tr>
<?php endif; ?>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'valhalla-contact-form' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><?php echo esc_html( __( 'Options', 'valhalla-contact-form' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'valhalla-contact-form' ) ); ?></legend>
		<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
		<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( "One option per line.", 'valhalla-contact-form' ) ); ?></span></label><br />
		<label><input type="checkbox" name="label_first" class="option" /> <?php echo esc_html( __( 'Put a label first, a checkbox last', 'valhalla-contact-form' ) ); ?></label><br />
<?php if ( 'checkbox' == $type ) : ?>
		<br /><label><input type="checkbox" name="exclusive" class="option" /> <?php echo esc_html( __( 'Make checkboxes exclusive', 'valhalla-contact-form' ) ); ?></label>
<?php endif; ?>
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
