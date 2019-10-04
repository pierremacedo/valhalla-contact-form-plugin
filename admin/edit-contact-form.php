<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function wpcf7_admin_save_button( $post_id ) {
	static $button = '';

	if ( ! empty( $button ) ) {
		echo $button;
		return;
	}

	$nonce = wp_create_nonce( 'wpcf7-save-contact-form_' . $post_id );

	$onclick = sprintf(
		"this.form._wpnonce.value = '%s';"
		. " this.form.action.value = 'save';"
		. " return true;",
		$nonce );

	$button = sprintf(
		'<input type="submit" class="button-primary" name="wpcf7-save" value="%1$s" onclick="%2$s" />',
		esc_attr( __( 'Save', 'valhalla-contact-form' ) ),
		$onclick );

	echo $button;
}

?><div class="wrap" id="wpcf7-contact-form-editor">

<h1 class="wp-heading-inline"><?php
	if ( $post->initial() ) {
		echo esc_html( __( 'Add New Contact Form', 'valhalla-contact-form' ) );
	} else {
		echo esc_html( __( 'Edit Contact Form', 'valhalla-contact-form' ) );
	}
?></h1>

<?php
	if ( ! $post->initial()
	and current_user_can( 'wpcf7_edit_contact_forms' ) ) {
		echo wpcf7_link(
			menu_page_url( 'wpcf7-new', false ),
			__( 'Add New', 'valhalla-contact-form' ),
			array( 'class' => 'page-title-action' )
		);
	}
?>

<hr class="wp-header-end">

<?php
	do_action( 'wpcf7_admin_warnings',
		$post->initial() ? 'wpcf7-new' : 'wpcf7',
		wpcf7_current_action(),
		$post
	);

	do_action( 'wpcf7_admin_notices',
		$post->initial() ? 'wpcf7-new' : 'wpcf7',
		wpcf7_current_action(),
		$post
	);
?>

<?php
if ( $post ) :

	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
		$disabled = '';
	} else {
		$disabled = ' disabled="disabled"';
	}
?>

<form method="post" action="<?php echo esc_url( add_query_arg( array( 'post' => $post_id ), menu_page_url( 'wpcf7', false ) ) ); ?>" id="wpcf7-admin-form-element"<?php do_action( 'wpcf7_post_edit_form_tag' ); ?>>
<?php
	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
		wp_nonce_field( 'wpcf7-save-contact-form_' . $post_id );
	}
?>
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" id="wpcf7-locale" name="wpcf7-locale" value="<?php echo esc_attr( $post->locale() ); ?>" />
<input type="hidden" id="hiddenaction" name="action" value="save" />
<input type="hidden" id="active-tab" name="active-tab" value="<?php echo isset( $_GET['active-tab'] ) ? (int) $_GET['active-tab'] : '0'; ?>" />

<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content">
<div id="titlediv">
<div id="titlewrap">
	<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo esc_html( __( 'Enter title here', 'valhalla-contact-form' ) ); ?></label>
<?php
	$posttitle_atts = array(
		'type' => 'text',
		'name' => 'post_title',
		'size' => 30,
		'value' => $post->initial() ? '' : $post->title(),
		'id' => 'title',
		'spellcheck' => 'true',
		'autocomplete' => 'off',
		'disabled' =>
			current_user_can( 'wpcf7_edit_contact_form', $post_id ) ? '' : 'disabled',
	);

	echo sprintf( '<input %s />', wpcf7_format_atts( $posttitle_atts ) );
?>
</div><!-- #titlewrap -->

<div class="inside">
<?php
	if ( ! $post->initial() ) :
?>
	<p class="description">
	<label for="wpcf7-shortcode"><?php echo esc_html( __( "Copy this shortcode and paste it into your post, page, or text widget content:", 'valhalla-contact-form' ) ); ?></label>
	<span class="shortcode wp-ui-highlight"><input type="text" id="wpcf7-shortcode" onfocus="this.select();" readonly="readonly" class="large-text code" value="<?php echo esc_attr( $post->shortcode() ); ?>" /></span>
	</p>
<?php
		if ( $old_shortcode = $post->shortcode( array( 'use_old_format' => true ) ) ) :
?>
	<p class="description">
	<label for="wpcf7-shortcode-old"><?php echo esc_html( __( "You can also use this old-style shortcode:", 'valhalla-contact-form' ) ); ?></label>
	<span class="shortcode old"><input type="text" id="wpcf7-shortcode-old" onfocus="this.select();" readonly="readonly" class="large-text code" value="<?php echo esc_attr( $old_shortcode ); ?>" /></span>
	</p>
<?php
		endif;
	endif;
?>
</div>
</div><!-- #titlediv -->
</div><!-- #post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<?php if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) : ?>
<div id="submitdiv" class="postbox">
<h3><?php echo esc_html( __( 'Status', 'valhalla-contact-form' ) ); ?></h3>
<div class="inside">
<div class="submitbox" id="submitpost">

<div id="minor-publishing-actions">

<div class="hidden">
	<input type="submit" class="button-primary" name="wpcf7-save" value="<?php echo esc_attr( __( 'Save', 'valhalla-contact-form' ) ); ?>" />
</div>

<?php
	if ( ! $post->initial() ) :
		$copy_nonce = wp_create_nonce( 'wpcf7-copy-contact-form_' . $post_id );
?>
	<input type="submit" name="wpcf7-copy" class="copy button" value="<?php echo esc_attr( __( 'Duplicate', 'valhalla-contact-form' ) ); ?>" <?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
<?php endif; ?>
</div><!-- #minor-publishing-actions -->

<div id="misc-publishing-actions">
<?php do_action( 'wpcf7_admin_misc_pub_section', $post_id ); ?>
</div><!-- #misc-publishing-actions -->

<div id="major-publishing-actions">

<?php
	if ( ! $post->initial() ) :
		$delete_nonce = wp_create_nonce( 'wpcf7-delete-contact-form_' . $post_id );
?>
<div id="delete-action">
	<input type="submit" name="wpcf7-delete" class="delete submitdelete" value="<?php echo esc_attr( __( 'Delete', 'valhalla-contact-form' ) ); ?>" <?php echo "onclick=\"if (confirm('" . esc_js( __( "You are about to delete this contact form.\n  'Cancel' to stop, 'OK' to delete.", 'valhalla-contact-form' ) ) . "')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
</div><!-- #delete-action -->
<?php endif; ?>

<div id="publishing-action">
	<span class="spinner"></span>
	<?php wpcf7_admin_save_button( $post_id ); ?>
</div>
<div class="clear"></div>
</div><!-- #major-publishing-actions -->
</div><!-- #submitpost -->
</div>
</div><!-- #submitdiv -->
<?php endif; ?>

<div id="informationdiv" class="postbox">
<h3><?php echo esc_html( __( "Do you need help?", 'valhalla-contact-form' ) ); ?></h3>
<div class="inside">
	<p><?php echo esc_html( __( "Here are some available options to help solve your problems.", 'valhalla-contact-form' ) ); ?></p>
	<ol>
		<li><?php echo wpcf7_link(
			__( 'https://drive.google.com/open?id=1f_sZbFGG7Dw_qvSEUtH3DgsHbDiTqq-h', 'valhalla-contact-form' ),
			__( 'Editing Form Template', 'valhalla-contact-form' )
		); ?></li>
		<li><?php echo wpcf7_link(
			__( 'https://drive.google.com/open?id=1yWQ1kFJHHeGvyE5HJTh6sS4bYKs3Twoj', 'valhalla-contact-form' ),
			__( 'Form Builder', 'valhalla-contact-form' )
		); ?></li>
        <li><?php echo wpcf7_link(
			__( 'https://drive.google.com/open?id=1I-Z1EmmY9nf4UkZKDZJ3Zd2KQ3BY6NQ1', 'valhalla-contact-form' ),
			__( 'Setting up Mail', 'valhalla-contact-form' )
		); ?></li>
        <li><?php echo wpcf7_link(
			__( 'https://drive.google.com/open?id=1vibIHbEBPQBrQu2_8Sx6z4YHsy-UfPDq', 'valhalla-contact-form' ),
			__( 'Labels and Messages', 'valhalla-contact-form' )
		); ?></li>
        <li><?php echo wpcf7_link(
			__( 'https://drive.google.com/file/d/11zkHGT21YV4XwKieMZw7QISE7e1qiuoB', 'valhalla-contact-form' ),
			__( 'Google Maps', 'valhalla-contact-form' )
		); ?></li>
        <li><?php echo wpcf7_link(
			__( 'https://drive.google.com/file/d/1P72MDbXh6D269ml2s8JWY18XvJNLhmal', 'valhalla-contact-form' ),
			__( 'Custom Style', 'valhalla-contact-form' )
		); ?></li>
        <li><?php echo wpcf7_link(
			__( 'https://drive.google.com/open?id=1jB6y6zrQvAEhKkRNf-G5mV5N3-ANXv4c', 'valhalla-contact-form' ),
			__( 'Additional Settings', 'valhalla-contact-form' )
		); ?></li>
        <li><?php echo wpcf7_link(
			__( 'https://drive.google.com/open?id=1jm2B3i0PdkRro2Fwu3wwKO96LHtPiRpy', 'valhalla-contact-form' ),
			__( 'Solving Problems', 'valhalla-contact-form' )
		); ?></li>
	</ol>
</div>
</div><!-- #informationdiv -->

</div><!-- #postbox-container-1 -->

<div id="postbox-container-2" class="postbox-container">
<div id="contact-form-editor">
<div class="keyboard-interaction"><?php
	echo sprintf(
		/* translators: 1: ◀ ▶ dashicon, 2: screen reader text for the dashicon */
		esc_html( __( '%1$s %2$s keys switch panels', 'valhalla-contact-form' ) ),
		'<span class="dashicons dashicons-leftright" aria-hidden="true"></span>',
		sprintf(
			'<span class="screen-reader-text">%s</span>',
			/* translators: screen reader text */
			esc_html( __( '(left and right arrow)', 'valhalla-contact-form' ) )
		)
	);
?></div>

<?php

	$editor = new WPCF7_Editor( $post );
	$panels = array();

	if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
		$panels = array(
			'form-panel' => array(
				'title' => __( 'Form', 'valhalla-contact-form' ),
				'callback' => 'wpcf7_editor_panel_form',
			),
			'mail-panel' => array(
				'title' => __( 'Mail', 'valhalla-contact-form' ),
				'callback' => 'wpcf7_editor_panel_mail',
			),
			'messages-panel' => array(
				'title' => __( 'Labels and Messages', 'valhalla-contact-form' ),
				'callback' => 'wpcf7_editor_panel_messages',
			),
            'google-maps-panel' => array(
				'title' => __( 'Google Maps', 'valhalla-contact-form' ),
				'callback' => 'wpcf7_editor_panel_google_maps',
			),
           'custom-style-panel' => array(
				'title' => __( 'Custom Style', 'valhalla-contact-form' ),
				'callback' => 'wpcf7_editor_panel_custom_style',
			),
		);

		$additional_settings = trim( $post->prop( 'additional_settings' ) );
		$additional_settings = explode( "\n", $additional_settings );
		$additional_settings = array_filter( $additional_settings );
		$additional_settings = count( $additional_settings );

		$panels['additional-settings-panel'] = array(
			'title' => $additional_settings
				/* translators: %d: number of additional settings */
				? sprintf(
					__( 'Additional Settings (%d)', 'valhalla-contact-form' ),
					$additional_settings )
				: __( 'Additional Settings', 'valhalla-contact-form' ),
			'callback' => 'wpcf7_editor_panel_additional_settings',
		);
	}

	$panels = apply_filters( 'wpcf7_editor_panels', $panels );

	foreach ( $panels as $id => $panel ) {
		$editor->add_panel( $id, $panel['title'], $panel['callback'] );
	}

	$editor->display();
?>
</div><!-- #contact-form-editor -->

<?php if ( current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) : ?>
<p class="submit"><?php wpcf7_admin_save_button( $post_id ); ?></p>
<?php endif; ?>

</div><!-- #postbox-container-2 -->

</div><!-- #post-body -->
<br class="clear" />
</div><!-- #poststuff -->
</form>

<?php endif; ?>

</div><!-- .wrap -->

<?php

	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->print_panels( $post );

	do_action( 'wpcf7_admin_footer', $post );
