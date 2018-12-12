<?php
/*
Plugin Name: Add Class to Link Popup
Plugin URI:
Description: Adds a checkbox to the insert link popup box to add 'btn' class
Version: 1.0
Author: Briteweb
Author URI:
Text Domain: bw-button-link
*/
add_action( 'after_wp_tiny_mce', function(){
	?>
	<script>
		var originalWpLink;
		// Ensure both TinyMCE, underscores and wpLink are initialized
		if ( typeof tinymce !== 'undefined' && typeof _ !== 'undefined' && typeof wpLink !== 'undefined' ) {
			// Ensure the #link-options div is present, because it's where we're appending our checkbox.
			if ( tinymce.$('#link-options').length ) {
				// Append our checkbox HTML to the #link-options div, which is already present in the DOM.
				tinymce.$('#link-options').append(<?php echo json_encode( '<div class="link-makebutton"><label><span></span><input type="checkbox" id="wp-link-makebutton" /> Make button link</label></div>' ); ?>);
				// Clone the original wpLink object so we retain access to some functions.
				originalWpLink = _.clone( wpLink );
				wpLink.makeButton = tinymce.$('#wp-link-makebutton');
				// Override the original wpLink object to include our custom functions.
				wpLink = _.extend( wpLink, {
					/**
					 * Fetch attributes for the generated link based on
					 * the link editor form properties.
					 *
					 * In this case, we're calling the original getAttrs()
					 * function, and then including our own behavior.
					 */
					getAttrs: function() {
						var attrs = originalWpLink.getAttrs();
						attrs.class = wpLink.makeButton.prop( 'checked' ) ? 'btn' : '';
						return attrs;
					},
					/**
					 * Build the link's HTML based on attrs when inserting
					 * into the text editor.
					 *
					 * In this case, we're completely overriding the existing
					 * function.
					 */
					buildHtml: function( attrs ) {
						var html = '<a href="' + attrs.href + '"';

						if ( attrs.target ) {
							html += ' target="' + attrs.target + '"';
						}
						if ( attrs.class ) {
							html += ' class="' + attrs.class + '"';
						}
						return html + '>';
					},
					/**
					 * Set the value of our checkbox based on the presence
					 * of the rel='nofollow' link attribute.
					 *
					 * In this case, we're calling the original mceRefresh()
					 * function, then including our own behavior
					 */
					mceRefresh: function( searchStr, text ) {
						originalWpLink.mceRefresh( searchStr, text );
						var editor = window.tinymce.get( window.wpActiveEditor )
						if ( typeof editor !== 'undefined' && ! editor.isHidden() ) {
							var linkNode = editor.dom.getParent( editor.selection.getNode(), 'a[href]' );
							if ( linkNode ) {
								wpLink.makeButton.prop( 'checked', 'btn' === editor.dom.getAttrib( linkNode, 'class' ) );
							}
						}
					}
				});
			}
		}
	</script>
	<style>
	#wp-link #link-options .link-makebutton {
		padding: 3px 0 0;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	#wp-link #link-options .link-makebutton label span {
		width: 83px;
	}

	.has-text-field #wp-link .query-results {
		top: 223px;
	}
	</style>
	<?php
});
