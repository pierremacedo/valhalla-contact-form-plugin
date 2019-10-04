<?php

class WPCF7_ContactFormTemplate {

	public static function get_default( $prop = 'form' ) {
		if ( 'form' == $prop ) {
			$template = self::form();
		} elseif ( 'mail' == $prop ) {
			$template = self::mail();
		} elseif ( 'mail_2' == $prop ) {
			$template = self::mail_2();
		} elseif ( 'messages' == $prop ) {
			$template = self::messages();
		} elseif ( 'google_maps' == $prop ) {
			$template = self::google_maps();
		} else {
			$template = null;
		}

		return apply_filters( 'wpcf7_default_template', $template, $prop );
	}

	public static function form() {
     $template = sprintf(
			'
            <div class="form-row form-group">
               <div class="col">
                  <label>%1$s</label> [text* your-name placeholder ""]
               </div>
               <div class="col">
                  <label>%2$s</label> [email* your-email placeholder ""]
               </div>
            </div>
      
            <div class="form-row form-group">
               <div class="col">
                  <label>%3$s</label> [tel your-phone placeholder ""]
               </div>
               <div class="col">
                  <label>%4$s</label> [text* your-subject placeholder ""]
               </div>
            </div>
      
            <div class="form-row form-group">
                <div class="col">
                   <label>%5$s</label> [textarea* your-message placeholder ""]
                </div>
            </div>
      
            <div class="form-row">
                <div class="col">
                   [submit "Send Message" ]
                </div>
            </div>',
			__( '[vhcf_name_field_label]', '' ),
			__( '[vhcf_email_field_label]', '' ),
			__( '[vhcf_phone_field_label]', '' ),
			__( '[vhcf_subject_field_label]', '' ),
			__( '[vhcf_message_field_label]', '' ),
			__( 'Send Message', '' ) );
		
		return trim( $template );
	}

	public static function mail() {
		$template = array(
			'subject' =>
				/* translators: 1: blog name, 2: [your-subject] */
				sprintf(
					_x( '%1$s "%2$s"', 'mail subject', 'valhalla-contact-form' ),
					get_bloginfo( 'name' ), '[your-subject]' ),
			'sender' => sprintf( '%s <%s>',
				get_bloginfo( 'name' ), self::from_email() ),
			'body' =>
				/* translators: %s: [your-name] <[your-email]> */
				sprintf( __( 'From: %s', 'valhalla-contact-form' ),
					'[your-name] <[your-email]>' ) . "\n"
                /* translators: %s: [your-phone] */  
                . sprintf( __( 'Phone: %s', 'valhalla-contact-form' ),
					'[your-phone]' ) . "\n\n"
				/* translators: %s: [your-subject] */
				. sprintf( __( 'Subject: %s', 'valhalla-contact-form' ),
					'[your-subject]' ) . "\n\n"
				. __( 'Message Body:', 'valhalla-contact-form' )
					. "\n" . '[your-message]' . "\n\n"
				. '-- ' . "\n"
				/* translators: 1: blog name, 2: blog URL */
				. sprintf(
					__( 'This e-mail was sent from a contact form on %1$s (%2$s)', 'valhalla-contact-form' ),
					get_bloginfo( 'name' ),
					get_bloginfo( 'url' ) ),
			'recipient' => get_option( 'admin_email' ),
			'additional_headers' => 'Reply-To: [your-email]',
			'attachments' => '',
			'use_html' => 0,
			'exclude_blank' => 0,
		);

		return $template;
	}

	public static function mail_2() {
		$template = array(
			'active' => false,
			'subject' =>
				/* translators: 1: blog name, 2: [your-subject] */
				sprintf(
					_x( '%1$s "%2$s"', 'mail subject', 'valhalla-contact-form' ),
					get_bloginfo( 'name' ), '[your-subject]' ),
			'sender' => sprintf( '%s <%s>',
				get_bloginfo( 'name' ), self::from_email() ),
			'body' =>
				__( 'Message Body:', 'valhalla-contact-form' )
					. "\n" . '[your-message]' . "\n\n"
				. '-- ' . "\n"
				/* translators: 1: blog name, 2: blog URL */
				. sprintf(
					__( 'This e-mail was sent from a contact form on %1$s (%2$s)', 'valhalla-contact-form' ),
					get_bloginfo( 'name' ),
					get_bloginfo( 'url' ) ),
			'recipient' => '[your-email]',
			'additional_headers' => sprintf( 'Reply-To: %s',
				get_option( 'admin_email' ) ),
			'attachments' => '',
			'use_html' => 0,
			'exclude_blank' => 0,
		);

		return $template;
	}

	public static function from_email() {
		$admin_email = get_option( 'admin_email' );
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );

		if ( wpcf7_is_localhost() ) {
			return $admin_email;
		}

		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		if ( strpbrk( $admin_email, '@' ) == '@' . $sitename ) {
			return $admin_email;
		}

		return 'wordpress@' . $sitename;
	}

	public static function messages() {
		$messages = array();

		foreach ( wpcf7_messages() as $key => $arr ) {
			$messages[$key] = $arr['default'];
		}

		return $messages;
	}
  
    public static function google_maps() {
		$template = array(
			'class' => "style='display:block'",
			'location' => "1600+Amphitheatre+Parkway+Mountain+View+CA",
		);

		return $template;
	}
   
}
 
function wpcf7_messages() {
	$messages = array(
        'name_field_label' => array(
			'description'
				=> __( "Name field", 'valhalla-contact-form' ),
			'default'
				=> __( "Name", 'valhalla-contact-form' ),
		),
		'email_field_label' => array(
			'description'
				=> __( "Email field", 'valhalla-contact-form' ),
			'default'
				=> __( "Email", 'valhalla-contact-form' ),
		),
		'phone_field_label' => array(
			'description'
				=> __( "Phone number field", 'valhalla-contact-form' ),
			'default'
				=> __( "Phone", 'valhalla-contact-form' ),
		),
		'subject_field_label' => array(
			'description'
				=> __( "Subject field", 'valhalla-contact-form' ),
			'default'
				=> __( "Subject", 'valhalla-contact-form' ),
		),
		'message_field_label' => array(
			'description'
				=> __( "Message textarea", 'valhalla-contact-form' ),
			'default'
				=> __( "Message", 'valhalla-contact-form' ),
		),
		'mail_sent_ok' => array(
			'description'
				=> __( "Sender's message was sent successfully", 'valhalla-contact-form' ),
			'default'
				=> __( "Thank you for your message. It has been sent.", 'valhalla-contact-form' ),
		),

		'mail_sent_ng' => array(
			'description'
				=> __( "Sender's message failed to send", 'valhalla-contact-form' ),
			'default'
				=> __( "There was an error trying to send your message. Please try again later.", 'valhalla-contact-form' ),
		),

		'validation_error' => array(
			'description'
				=> __( "Validation errors occurred", 'valhalla-contact-form' ),
			'default'
				=> __( "One or more fields have an error. Please check and try again.", 'valhalla-contact-form' ),
		),

		'spam' => array(
			'description'
				=> __( "Submission was referred to as spam", 'valhalla-contact-form' ),
			'default'
				=> __( "There was an error trying to send your message. Please try again later.", 'valhalla-contact-form' ),
		),

		'accept_terms' => array(
			'description'
				=> __( "There are terms that the sender must accept", 'valhalla-contact-form' ),
			'default'
				=> __( "You must accept the terms and conditions before sending your message.", 'valhalla-contact-form' ),
		),

		'invalid_required' => array(
			'description'
				=> __( "There is a field that the sender must fill in", 'valhalla-contact-form' ),
			'default'
				=> __( "The field is required.", 'valhalla-contact-form' ),
		),

		'invalid_too_long' => array(
			'description'
				=> __( "There is a field with input that is longer than the maximum allowed length", 'valhalla-contact-form' ),
			'default'
				=> __( "The field is too long.", 'valhalla-contact-form' ),
		),

		'invalid_too_short' => array(
			'description'
				=> __( "There is a field with input that is shorter than the minimum allowed length", 'valhalla-contact-form' ),
			'default'
				=> __( "The field is too short.", 'valhalla-contact-form' ),
		)
	);

	return apply_filters( 'wpcf7_messages', $messages );
}

