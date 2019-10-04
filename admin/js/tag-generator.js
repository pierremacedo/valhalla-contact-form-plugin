( function( $ ) {

	'use strict';

	if ( typeof wpcf7 === 'undefined' || wpcf7 === null ) {
		return;
	}

	wpcf7.taggen = {};

	$( function() {
		$( 'form.tag-generator-panel' ).each( function() {
			wpcf7.taggen.update( $( this ) );
		} );
	} );

	$( 'form.tag-generator-panel' ).submit( function() {
		return false;
	} );

	$( 'form.tag-generator-panel .control-box :input' ).change( function() {
		var $form = $( this ).closest( 'form.tag-generator-panel' );
		wpcf7.taggen.normalize( $( this ) );
		wpcf7.taggen.update( $form );
	} );

	$( 'input.insert-tag' ).click( function() {
		var $form = $( this ).closest( 'form.tag-generator-panel' );
		var tag = $form.find( 'input.tag' ).val();
		wpcf7.taggen.insert( tag );
		tb_remove(); // close thickbox
		return false;
	} );

	wpcf7.taggen.update = function( $form ) {
		var id = $form.attr( 'data-id' );
		var name = '';
		var name_fields = $form.find( 'input[name="name"]' );

		if ( name_fields.length ) {
			name = name_fields.val();

			if ( '' === name ) {
				name = id + '-' + Math.floor( Math.random() * 1000 );
				name_fields.val( name );
			}
		}

		if ( $.isFunction( wpcf7.taggen.update[ id ] ) ) {
			return wpcf7.taggen.update[ id ].call( this, $form );
		}

		$form.find( 'input.tag' ).each( function() {
			var tag_type = $( this ).attr( 'name' );

			if ( $form.find( ':input[name="tagtype"]' ).length ) {
				tag_type = $form.find( ':input[name="tagtype"]' ).val();
			}

			if ( $form.find( ':input[name="required"]' ).is( ':checked' ) ) {
				tag_type += '*';
			}

			var components = wpcf7.taggen.compose( tag_type, $form );
			$( this ).val( components );
		} );

		$form.find( 'span.mail-tag' ).text( '[' + name + ']' );

		$form.find( 'input.mail-tag' ).each( function() {
			$( this ).val( '[' + name + ']' );
		} );
	};

	wpcf7.taggen.update.captcha = function( $form ) {
		var captchac = wpcf7.taggen.compose( 'captchac', $form );
		var captchar = wpcf7.taggen.compose( 'captchar', $form );

		$form.find( 'input.tag' ).val( captchac + ' ' + captchar );
	};

	wpcf7.taggen.compose = function( tagType, $form ) {
		var name = $form.find( 'input[name="name"]' ).val();
		var scope = $form.find( '.scope.' + tagType );

		if ( ! scope.length ) {
			scope = $form;
		}

		var options = [];

		scope.find( 'input.option' ).not( ':checkbox,:radio' ).each( function( i ) {
			var val = $( this ).val();

			if ( ! val ) {
				return;
			}

			if ( $( this ).hasClass( 'filetype' ) ) {
				val = val.split( /[,|\s]+/ ).join( '|' );
			}

			if ( $( this ).hasClass( 'color' ) ) {
				val = '#' + val;
			}

			if ( 'class' == $( this ).attr( 'name' ) ) {
				$.each( val.split( ' ' ), function( i, n ) {
					options.push( 'class:' + n );
				} );
			} else {
				options.push( $( this ).attr( 'name' ) + ':' + val );
			}
		} );

		scope.find( 'input:checkbox.option' ).each( function( i ) {
			if ( $( this ).is( ':checked' ) ) {
				options.push( $( this ).attr( 'name' ) );
			}
		} );

		scope.find( 'input:radio.option' ).each( function( i ) {
			if ( $( this ).is( ':checked' ) && ! $( this ).hasClass( 'default' ) ) {
				options.push( $( this ).attr( 'name' ) + ':' + $( this ).val() );
			}
		} );

		if ( 'radio' == tagType ) {
			options.push( 'default:1' );
		}

		options = ( options.length > 0 ) ? options.join( ' ' ) : '';

		var value = '';

		if ( scope.find( ':input[name="values"]' ).val() ) {
			$.each(
				scope.find( ':input[name="values"]' ).val().split( "\n" ),
				function( i, n ) {
					value += ' "' + n.replace( /["]/g, '&quot;' ) + '"';
				}
			);
		}

		var components = [];

		$.each( [ tagType, name, options, value ], function( i, v ) {
			v = $.trim( v );

			if ( '' != v ) {
				components.push( v );
			}
		} );

		components = $.trim( components.join( ' ' ) );
		components = '[' + components + ']';

		var content = scope.find( ':input[name="content"]' ).val();
		content = $.trim( content );

		if ( content ) {
			components += ' ' + content + ' [/' + tagType + ']';
		}

		return components;
	};

	wpcf7.taggen.normalize = function( $input ) {
		var val = $input.val();

		if ( $input.is( 'input[name="name"]' ) ) {
			val = val.replace( /[^0-9a-zA-Z:._-]/g, '' ).replace( /^[^a-zA-Z]+/, '' );
		}

		if ( $input.is( '.numeric' ) ) {
			val = val.replace( /[^0-9.-]/g, '' );
		}

		if ( $input.is( '.idvalue' ) ) {
			val = val.replace( /[^-0-9a-zA-Z_]/g, '' );
		}

		if ( $input.is( '.classvalue' ) ) {
			val = $.map( val.split( ' ' ), function( n ) {
				return n.replace( /[^-0-9a-zA-Z_]/g, '' );
			} ).join( ' ' );

			val = $.trim( val.replace( /\s+/g, ' ' ) );
		}

		if ( $input.is( '.color' ) ) {
			val = val.replace( /[^0-9a-fA-F]/g, '' );
		}

		if ( $input.is( '.filesize' ) ) {
			val = val.replace( /[^0-9kKmMbB]/g, '' );
		}

		if ( $input.is( '.filetype' ) ) {
			val = val.replace( /[^0-9a-zA-Z.,|\s]/g, '' );
		}

		if ( $input.is( '.date' ) ) {
			// 'yyyy-mm-dd' ISO 8601 format
			if ( ! val.match( /^\d{4}-\d{2}-\d{2}$/ ) ) {
				val = '';
			}
		}

		if ( $input.is( ':input[name="values"]' ) ) {
			val = $.trim( val );
		}

		$input.val( val );

		if ( $input.is( ':checkbox.exclusive' ) ) {
			wpcf7.taggen.exclusiveCheckbox( $input );
		}
	};

	wpcf7.taggen.exclusiveCheckbox = function( $cb ) {
		if ( $cb.is( ':checked' ) ) {
			$cb.siblings( ':checkbox.exclusive' ).prop( 'checked', false );
		}
	};

	wpcf7.taggen.insert = function( content ) {
		$( 'textarea#wpcf7-form' ).each( function() {
			this.focus();

			if ( document.selection ) { // IE
				var selection = document.selection.createRange();
				selection.text = content;
			} else if ( this.selectionEnd || 0 === this.selectionEnd ) {
				var val = $( this ).val();
				var end = this.selectionEnd;
				$( this ).val( val.substring( 0, end ) +
					content + val.substring( end, val.length ) );
				this.selectionStart = end + content.length;
				this.selectionEnd = end + content.length;
			} else {
				$( this ).val( $( this ).val() + content );
			}

			this.focus();
		} );
	};

} )( jQuery );

// Add Rows

function addRows() {
  jQuery("#tag-generator-list").addClass("hide-options");
  jQuery("#row-options").removeClass("hide-options");
  jQuery("#row-options").find('input').prop('checked', false);  
}

function countColumns(arg) { 
 
  if (arg == 1) {
  let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<div class="form-row form-group"><div class="col"></div></div>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#row-options").addClass("hide-options");
        
     
  } else {
  let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<div class="form-row form-group"><div class="col"></div><div class="col"></div></div>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#row-options").addClass("hide-options");
        
  }
}

// Add Labels

function addLabels() {
  jQuery("#tag-generator-list").addClass("hide-options");
  jQuery("#label-options").removeClass("hide-options");
  jQuery("#label-options").find('input').prop('checked', false);  
}

function getLabelOption(arg) { 
  
  if (arg == 'name') {
        let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<label>[vhcf_name_field_label]</label>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#label-options").addClass("hide-options");
        
     
  } else if (arg == 'email') {
        let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<label>[vhcf_email_field_label]</label>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#label-options").addClass("hide-options");
        
     
  } else if (arg == 'phone') {
        let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<label>[vhcf_phone_field_label]</label>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#label-options").addClass("hide-options");
        
     
  } else if (arg == 'subject') {
        let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<label>[vhcf_subject_field_label]</label>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#label-options").addClass("hide-options");
        
     
  } else if (arg == 'message') {
        let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<label>[vhcf_message_field_label]</label>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#label-options").addClass("hide-options");
        
     
  } else if (arg == 'blank') {
        let $txt = jQuery("#wpcf7-form");
        let caretPos = $txt[0].selectionStart;
        let textAreaTxt = $txt.val();
        let txtToAdd = '<label></label>';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
        jQuery("#tag-generator-list").removeClass("hide-options");
        jQuery("#label-options").addClass("hide-options");
        
     
  }
}