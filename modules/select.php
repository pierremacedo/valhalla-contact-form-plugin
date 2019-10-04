<?php
/**
** A base module for [select] and [select*]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_select', 10, 0 );

function wpcf7_add_form_tag_select() {
	wpcf7_add_form_tag( array( 'select', 'select*' ),
		'wpcf7_select_form_tag_handler',
		array(
			'name-attr' => true,
			'selectable-values' => true,
		)
	);
}

function wpcf7_select_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$multiple = $tag->has_option( 'multiple' );
	$include_blank = $tag->has_option( 'include_blank' );

	if ( $data = (array) $tag->get_data_option() ) {
		$tag->values = array_merge( $tag->values, array_values( $data ) );
		$tag->labels = array_merge( $tag->labels, array_values( $data ) );
	}

	$values = $tag->values;
	$labels = $tag->labels;

	$default_choice = $tag->get_default_option( null, array(
		'multiple' => $multiple,
		'shifted' => $include_blank,
	) );

	if ( $include_blank
	or empty( $values ) ) {
		array_unshift( $labels, '---' );
		array_unshift( $values, '' );
	} 

	$html = '';
	$hangover = wpcf7_get_hangover( $tag->name );

	foreach ( $values as $key => $value ) {
		if ( $hangover ) {
			$selected = in_array( $value, (array) $hangover, true );
		} else {
			$selected = in_array( $value, (array) $default_choice, true );
		}

		$item_atts = array(
			'value' => $value,
			'selected' => $selected ? 'selected' : '',
		);

		$item_atts = wpcf7_format_atts( $item_atts );

		$label = isset( $labels[$key] ) ? $labels[$key] : $value;

		$html .= sprintf( '<option %1$s>%2$s</option>',
			$item_atts, esc_html( $label ) );
	}

	if ( $multiple ) {
		$atts['multiple'] = 'multiple';
	}

	$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_select', 'wpcf7_select_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_select*', 'wpcf7_select_validation_filter', 10, 2 );

function wpcf7_select_validation_filter( $result, $tag ) {
	$name = $tag->name;

	if ( isset( $_POST[$name] )
	and is_array( $_POST[$name] ) ) {
		foreach ( $_POST[$name] as $key => $value ) {
			if ( '' === $value ) {
				unset( $_POST[$name][$key] );
			}
		}
	}

	$empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];

	if ( $tag->is_required() and $empty ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	return $result;
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_menu', 25, 0 );

function wpcf7_add_tag_generator_menu() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'menu', __( 'drop-down list', 'valhalla-contact-form' ),
		'wpcf7_tag_generator_menu' );
}

function wpcf7_tag_generator_menu( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );

	$description = __( "Generate a drop-down list field. For more details, see %s.", 'valhalla-contact-form' );

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
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'valhalla-contact-form' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

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
		<label><input type="checkbox" name="multiple" class="option" /> <?php echo esc_html( __( 'Allow multiple selections', 'valhalla-contact-form' ) ); ?></label><br />
		<label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'valhalla-contact-form' ) ); ?></label>
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
	<input type="text" name="select" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'valhalla-contact-form' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'valhalla-contact-form' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
