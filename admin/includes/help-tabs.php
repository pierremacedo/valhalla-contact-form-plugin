<?php

class WPCF7_Help_Tabs {

	private $screen;

	public function __construct( WP_Screen $screen ) {
		$this->screen = $screen;
	}

	public function set_help_tabs( $type ) {
		switch ( $type ) {
			case 'list':
				$this->screen->add_help_tab( array(
					'id' => 'list_overview',
					'title' => __( 'Overview', 'valhalla-contact-form' ),
					'content' => $this->content( 'list_overview' ) ) );

				$this->screen->add_help_tab( array(
					'id' => 'list_available_actions',
					'title' => __( 'Available Actions', 'valhalla-contact-form' ),
					'content' => $this->content( 'list_available_actions' ) ) );

				$this->sidebar();

				return;
			  case 'edit':
				$this->screen->add_help_tab( array(
					'id' => 'edit_overview',
					'title' => __( 'Overview', 'valhalla-contact-form' ),
					'content' => $this->content( 'edit_overview' ) ) );

				$this->screen->add_help_tab( array(
					'id' => 'edit_mail_tags',
					'title' => __( 'Mail-tags', 'valhalla-contact-form' ),
					'content' => $this->content( 'edit_mail_tags' ) ) );

				$this->sidebar();

				return;
			case 'integration':
				/* $this->screen->add_help_tab( array(
					'id' => 'integration_overview',
					'title' => __( 'Overview', 'valhalla-contact-form' ),
					'content' => $this->content( 'integration_overview' ) ) ); */

				$this->sidebar();

				return;
		}
	}

	private function content( $name ) {
		$content = array();

		$content['list_overview'] = '<p>' . __( "On this screen, you can manage contact forms provided by Valhalla Contact Form. You can manage an unlimited number of contact forms. Each contact form has a unique ID and Valhalla Contact Form shortcode ([valhalla-contact-form ...]). To insert a contact form into a post or a text widget, insert the shortcode into the target.", 'valhalla-contact-form' ) . '</p>';

		$content['list_available_actions'] = '<p>' . __( "Hovering over a row in the contact forms list will display action links that allow you to manage your contact form. You can perform the following actions:", 'valhalla-contact-form' ) . '</p>';
		$content['list_available_actions'] .= '<p>' . __( "<strong>Edit</strong> - Navigates to the editing screen for that contact form. You can also reach that screen by clicking on the contact form title.", 'valhalla-contact-form' ) . '</p>';
		$content['list_available_actions'] .= '<p>' . __( "<strong>Duplicate</strong> - Clones that contact form. A cloned contact form inherits all content from the original, but has a different ID.", 'valhalla-contact-form' ) . '</p>';

		$content['edit_overview'] = '<p>' . __( "On this screen, you can edit a contact form. A contact form is comprised of the following components:", 'valhalla-contact-form' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Title</strong> is the title of a contact form. This title is only used for labeling a contact form, and can be edited.", 'valhalla-contact-form' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Form</strong> is a content of HTML form. You can use arbitrary HTML, which is allowed inside a form element. You can also use shortcodes here.", 'valhalla-contact-form' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Mail</strong> manages a mail template (headers and message body) that this contact form will send when users submit it. You can use Valhalla Contact Form&#8217;s mail-tags here.", 'valhalla-contact-form' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Mail (2)</strong> is an additional mail template that works similar to Mail. Mail (2) is different in that it is sent only when Mail has been sent successfully.", 'valhalla-contact-form' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "In <strong>Labels and Messages</strong>, you can edit field names and various types of messages used for this contact form. These messages are relatively short messages, like a validation error message you see when you leave a required field blank.", 'valhalla-contact-form' ) . '</p>';
        $content['edit_overview'] .= '<p>' . __( "In <strong>Google Maps</strong>, you can enable or disable the map shown with all forms by default and also enter a desired location.", 'valhalla-contact-form' ) . '</p>';
        $content['edit_overview'] .= '<p>' . __( "In <strong>Custom Style</strong>, you can customize a form by using some useful CSS classes included in Valhalla Contact Form", 'valhalla-contact-form' ) . '</p>';
		$content['edit_overview'] .= '<p>' . __( "<strong>Additional Settings</strong> provides a place where you can customize the behavior of this contact form by adding code snippets.", 'valhalla-contact-form' ) . '</p>';

		$content['edit_mail_tags'] = '<p>' . __( "A mail-tag is a field name enclosed in square brackets that you can use in every Mail and Mail (2) field. A mail-tag represents a user input value through an input field of a corresponding form field.", 'valhalla-contact-form' ) . '</p>';
		$content['edit_mail_tags'] .= '<p>' . __( "There are also special mail-tags that have specific names, but aren't related to any form field. They are used to represent meta information of form submissions like the submitter&#8217;s IP address or the URL of the page.", 'valhalla-contact-form' ) . '</p>';

		if ( ! empty( $content[$name] ) ) {
			return $content[$name];
		}
	}

	public function sidebar() {
		$content = '<p><strong>' . __( 'For more information:', 'valhalla-contact-form' ) . '</strong></p>';
		$content .= '<p>' . wpcf7_link( __( 'https://drive.google.com/drive/u/5/folders/1d677KlewoKLqpnmswlnn4McGxVYyjcwp', 'valhalla-contact-form' ), __( 'Docs', 'valhalla-contact-form' ) ) . '</p>';

		$this->screen->set_help_sidebar( $content );
	}
}
