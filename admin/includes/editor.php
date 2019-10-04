<?php

class WPCF7_Editor {

	private $contact_form;
	private $panels = array();

	public function __construct( WPCF7_ContactForm $contact_form ) {
		$this->contact_form = $contact_form;
	}

	public function add_panel( $id, $title, $callback ) {
		if ( wpcf7_is_name( $id ) ) {
			$this->panels[$id] = array(
				'title' => $title,
				'callback' => $callback,
			);
		}
	}

	public function display() {
		if ( empty( $this->panels ) ) {
			return;
		}

		echo '<ul id="contact-form-editor-tabs">';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<li id="%1$s-tab"><a href="#%1$s">%2$s</a></li>',
				esc_attr( $id ), esc_html( $panel['title'] ) );
		}

		echo '</ul>';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<div class="contact-form-editor-panel" id="%1$s">',
				esc_attr( $id ) );

			if ( is_callable( $panel['callback'] ) ) {
				$this->notice( $id, $panel );
				call_user_func( $panel['callback'], $this->contact_form );
			}

			echo '</div>';
		}
	}

	public function notice( $id, $panel ) {
		echo '<div class="config-error"></div>';
	}
}

function wpcf7_editor_panel_form( $post ) {
    $desc_link = wpcf7_link(
		__( 'https://drive.google.com/open?id=1f_sZbFGG7Dw_qvSEUtH3DgsHbDiTqq-h', 'valhalla-contact-form' ),
		__( 'Editing Form Template', 'valhalla-contact-form' ) );
	$description = __( "You can edit the form template here. For details, see %s.", 'valhalla-contact-form' );
	$description = sprintf( esc_html( $description ), $desc_link );
?>

<h2><?php echo esc_html( __( 'Form', 'valhalla-contact-form' ) ); ?></h2>

<fieldset>
<legend><?php echo $description; ?></legend>

<?php
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->print_buttons();
?>

<textarea id="wpcf7-form" name="wpcf7-form" cols="100" rows="24" class="large-text code" data-config-field="form.body"><?php echo esc_textarea( $post->prop( 'form' ) ); ?></textarea>
  </fieldset>
<?php
}

function wpcf7_editor_panel_mail( $post ) {
	wpcf7_editor_box_mail( $post );

	echo '<br class="clear" />';

	wpcf7_editor_box_mail( $post, array(
		'id' => 'wpcf7-mail-2',
		'name' => 'mail_2',
		'title' => __( 'Mail (2)', 'valhalla-contact-form' ),
		'use' => __( 'Use Mail (2)', 'valhalla-contact-form' ),
	) );
}

function wpcf7_editor_box_mail( $post, $args = '' ) {
	$args = wp_parse_args( $args, array(
		'id' => 'wpcf7-mail',
		'name' => 'mail',
		'title' => __( 'Mail', 'valhalla-contact-form' ),
		'use' => null,
	) );

	$id = esc_attr( $args['id'] );

	$mail = wp_parse_args( $post->prop( $args['name'] ), array(
		'active' => false,
		'recipient' => '',
        'apifield' => '',
		'sender' => '',
		'subject' => '',
		'body' => '',
		'additional_headers' => '',
		'attachments' => '',
		'use_html' => false,
		'exclude_blank' => false,
	) );

?>
<div class="contact-form-editor-box-mail" id="<?php echo $id; ?>">
<h2><?php echo esc_html( $args['title'] ); ?></h2>

<?php
	if ( ! empty( $args['use'] ) ) :
?>
<label for="<?php echo $id; ?>-active"><input type="checkbox" id="<?php echo $id; ?>-active" name="<?php echo $id; ?>[active]" class="toggle-form-table" value="1"<?php echo ( $mail['active'] ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( $args['use'] ); ?></label>
<p class="description"><?php echo esc_html( __( "Mail (2) is an additional mail template often used as an autoresponder.", 'valhalla-contact-form' ) ); ?></p>
<?php
	endif;
?>

<fieldset>
<legend>
<?php
	
	$desc_link = wpcf7_link(
		__( 'https://drive.google.com/open?id=1I-Z1EmmY9nf4UkZKDZJ3Zd2KQ3BY6NQ1', 'valhalla-contact-form' ),
		__( 'Setting up Mail', 'valhalla-contact-form' ) );
	$description = __( "You can edit the mail template here. For details, see %s.", 'valhalla-contact-form' );
	$description = sprintf( esc_html( $description ), $desc_link );
	echo $description;
	echo '<br />';

	echo esc_html( __( "In the following fields, you can use these mail-tags:",
		'valhalla-contact-form' ) );
	echo '<br />';
	$post->suggest_mail_tags( $args['name'] );
?>
</legend>
<table class="form-table">
<tbody>
	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-recipient"><?php echo esc_html( __( 'To', 'valhalla-contact-form' ) ); ?></label>
	</th>
	<td>
		<input type="text" id="<?php echo $id; ?>-recipient" name="<?php echo $id; ?>[recipient]" class="large-text code" size="70" value="<?php echo esc_attr( $mail['recipient'] ); ?>" data-config-field="<?php echo sprintf( '%s.recipient', esc_attr( $args['name'] ) ); ?>" />
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-sender"><?php echo esc_html( __( 'From', 'valhalla-contact-form' ) ); ?></label>
	</th>
	<td>
		<input type="text" id="<?php echo $id; ?>-sender" name="<?php echo $id; ?>[sender]" class="large-text code" size="70" value="<?php echo esc_attr( $mail['sender'] ); ?>" data-config-field="<?php echo sprintf( '%s.sender', esc_attr( $args['name'] ) ); ?>" />
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-subject"><?php echo esc_html( __( 'Subject', 'valhalla-contact-form' ) ); ?></label>
	</th>
	<td>
		<input type="text" id="<?php echo $id; ?>-subject" name="<?php echo $id; ?>[subject]" class="large-text code" size="70" value="<?php echo esc_attr( $mail['subject'] ); ?>" data-config-field="<?php echo sprintf( '%s.subject', esc_attr( $args['name'] ) ); ?>" />
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-additional-headers"><?php echo esc_html( __( 'Additional Headers', 'valhalla-contact-form' ) ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo $id; ?>-additional-headers" name="<?php echo $id; ?>[additional_headers]" cols="100" rows="4" class="large-text code" data-config-field="<?php echo sprintf( '%s.additional_headers', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $mail['additional_headers'] ); ?></textarea>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-body"><?php echo esc_html( __( 'Message Body', 'valhalla-contact-form' ) ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo $id; ?>-body" name="<?php echo $id; ?>[body]" cols="100" rows="18" class="large-text code" data-config-field="<?php echo sprintf( '%s.body', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $mail['body'] ); ?></textarea>

		<p><label for="<?php echo $id; ?>-exclude-blank"><input type="checkbox" id="<?php echo $id; ?>-exclude-blank" name="<?php echo $id; ?>[exclude_blank]" value="1"<?php echo ( ! empty( $mail['exclude_blank'] ) ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( __( 'Exclude lines with blank mail-tags from output', 'valhalla-contact-form' ) ); ?></label></p>

		<p><label for="<?php echo $id; ?>-use-html"><input type="checkbox" id="<?php echo $id; ?>-use-html" name="<?php echo $id; ?>[use_html]" value="1"<?php echo ( $mail['use_html'] ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( __( 'Use HTML content type', 'valhalla-contact-form' ) ); ?></label></p>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-attachments"><?php echo esc_html( __( 'File Attachments', 'valhalla-contact-form' ) ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo $id; ?>-attachments" name="<?php echo $id; ?>[attachments]" cols="100" rows="4" class="large-text code" data-config-field="<?php echo sprintf( '%s.attachments', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $mail['attachments'] ); ?></textarea>
	</td>
	</tr>
</tbody>
</table>
</fieldset>
</div>
<?php
}

function wpcf7_editor_panel_messages( $post ) {
	$desc_link = wpcf7_link(
		__( 'https://drive.google.com/open?id=1vibIHbEBPQBrQu2_8Sx6z4YHsy-UfPDq', 'valhalla-contact-form' ),
		__( 'Labels and Messages', 'valhalla-contact-form' ) );
	$description = __( "You can edit field names, placeholders, and the messages used in various situations here. For details, see %s.", 'valhalla-contact-form' );
	$description = sprintf( esc_html( $description ), $desc_link );

	$messages = wpcf7_messages();

	if ( isset( $messages['captcha_not_match'] )
	and ! wpcf7_use_really_simple_captcha() ) {
		unset( $messages['captcha_not_match'] );
	}

?>
<h2><?php echo esc_html( __( 'Labels and Messages', 'valhalla-contact-form' ) ); ?></h2>
<fieldset>
<legend><?php echo $description; ?></legend>
<?php

	foreach ( $messages as $key => $arr ) {
		$field_id = sprintf( 'wpcf7-message-%s', strtr( $key, '_', '-' ) );
		$field_name = sprintf( 'wpcf7-messages[%s]', $key );

?>
<p class="description">
<label for="<?php echo $field_id; ?>"><?php echo esc_html( $arr['description'] ); ?><br />
<input type="text" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" class="large-text" size="70" value="<?php echo esc_attr( $post->message( $key, false ) ); ?>" data-config-field="<?php echo sprintf( 'messages.%s', esc_attr( $key ) ); ?>" />
</label>
</p>
<?php
	}
?>
</fieldset>
<?php
}

function wpcf7_editor_panel_google_maps( $post, $args = '' ) {

    $args = wp_parse_args( $args, array(
		'name' => 'google_maps',
	) );
  
    $google_maps = wp_parse_args( $post->prop( $args['name'] ), array(
		'class' => "style='display:block'",
        'location' => "1600+Amphitheatre+Parkway+Mountain+View+CA",
	) );
    $desc_link = wpcf7_link(
		__( 'https://drive.google.com/open?id=11zkHGT21YV4XwKieMZw7QISE7e1qiuoB', 'valhalla-contact-form' ),
		__( 'Google Maps', 'valhalla-contact-form' ) );
	$description = __( "Here you can enable or disable the map shown with all forms by default and also enter a desired location. For details, see %s.", 'valhalla-contact-form' );
	$description = sprintf( esc_html( $description ), $desc_link );
    $description_location = __( "Type a location or an address without space and with a + sign between the words. For example, ''Eiffel Tower, Paris, France'' must become ''Eiffel+Tower+Paris+France'', and ''1419 Westwood Blvd Los Angeles'' becomes ''1419+Westwood+Blvd+Los+Angeles'':");
    $description_location = sprintf( esc_html( $description_location ));
    $description_enable = __( "Enable Google Maps in your form.");
    $description_enable = sprintf( esc_html( $description_enable ));
    $description_disable = __( "Disable Google Maps in your form.");
    $description_disable = sprintf( esc_html( $description_disable ));

?>
<h2><?php echo esc_html( __( 'Google Maps', 'valhalla-contact-form' ) ); ?></h2>
<fieldset>
<legend><?php echo $description; ?></legend>

<p class="description description-custom">
<label for="wpcf7-google-maps-class-enable"><input type="radio" class="toggle-form-table" id="wpcf7-google-maps-class-enable" name="wpcf7-google-maps[class]" value="style='display:block'" <?php checked( $google_maps['class'], "style='display:block'" ); ?> /><?php echo $description_enable; ?><br />
</label>
</p>  
<p class="description description-custom">
<label for="wpcf7-google-maps-class-disable"><input type="radio" class="toggle-form-table" id="wpcf7-google-maps-class-disable" name="wpcf7-google-maps[class]" value="style='display:none'" <?php checked( $google_maps['class'], "style='display:none'" ); ?> /><?php echo $description_disable; ?><br />
</label>
</p>    
<p class="description description-custom">
<label for="wpcf7-google-maps-location"><?php echo $description_location; ?><br />
<input type="text" id="wpcf7-google-maps-location" name="wpcf7-google-maps[location]" class="large-text code" size="70" value="<?php echo esc_attr( $google_maps['location'] ); ?>" />
</label>
</p>  
</fieldset>
<?php
}

function wpcf7_editor_panel_custom_style( $post ) {
    $desc_link = wpcf7_link(
		__( 'https://drive.google.com/open?id=1P72MDbXh6D269ml2s8JWY18XvJNLhmal', 'valhalla-contact-form' ),
		__( 'Custom Styles', 'valhalla-contact-form' ) );
	$description = __( "Here you can add your custom CSS styles to change the form appearance. For details, see %s.", 'valhalla-contact-form' );
	$description = sprintf( esc_html( $description ), $desc_link );

?>
<h2><?php echo esc_html( __( 'Custom Style', 'valhalla-contact-form' ) ); ?></h2>
<fieldset>
<legend><?php echo $description; ?></br> 
<button id="addcss" type="button" class="button" value="bg" onclick="generateCSS(this.value)">Background</button>  
<button id="addcss" type="button" class="button" value="label" onclick="generateCSS(this.value)">Labels</button> 
<button id="addcss" type="button" class="button" value="submit" onclick="generateCSS(this.value)">Submit button</button>  
<button id="addcss" type="button" class="button" value="width" onclick="generateCSS(this.value)">Form width</button>    
</legend>

<p class="description description-custom">
<label for="wpcf7-custom-style"><?php echo "Add your custom CSS here" ?><br />
<textarea id="wpcf7-custom-style" name="wpcf7-custom-style" cols="100" rows="8" class="large-text"><?php echo esc_textarea( $post->prop( 'custom_style' ) ); ?></textarea>
</label>
</p>  
</fieldset>
<?php
}
function wpcf7_editor_panel_additional_settings( $post ) {
	$desc_link = wpcf7_link(
		__( 'https://drive.google.com/open?id=1jB6y6zrQvAEhKkRNf-G5mV5N3-ANXv4c', 'valhalla-contact-form' ),
		__( 'Additional Settings', 'valhalla-contact-form' ) );
	$description = __( "You can add customization code snippets here. For details, see %s.", 'valhalla-contact-form' );
	$description = sprintf( esc_html( $description ), $desc_link );

?>
<h2><?php echo esc_html( __( 'Additional Settings', 'valhalla-contact-form' ) ); ?></h2>
<fieldset>
<legend><?php echo $description; ?></legend>
<textarea id="wpcf7-additional-settings" name="wpcf7-additional-settings" cols="100" rows="8" class="large-text" data-config-field="additional_settings.body"><?php echo esc_textarea( $post->prop( 'additional_settings' ) ); ?></textarea> 
</fieldset>
<?php
}
