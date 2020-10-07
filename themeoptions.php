
function add_theme_menu_item()
{
	add_menu_page("Add Mailing", "Add Mailing", "manage_options", "theme-panel", "theme_settings_page", null, 99);
}

add_action("admin_menu", "add_theme_menu_item");


function theme_settings_page()
{
    ?>
	    <div class="wrap">
	    <h1>Add Mailing</h1>
	    <form method="post" action="options.php">
	        <?php
	            settings_fields("section");
	            do_settings_sections("theme-options");      
	            submit_button(); 
	        ?>          
	    </form>
		</div>
	<?php
}


function display_titulo_add()
{
	?>
    	<input type="text" name="titulo_add" id="titulo_add" value="<?php echo get_option('titulo_add'); ?>" />
    <?php
}

function display_link_add()
{
	?>
    	<input type="text" name="link_add" id="link_add" value="<?php echo get_option('link_add'); ?>" />
    <?php
}

function media_selector_settings_page_callback() {



	wp_enqueue_media();

	?>
		<div class='image-preview-wrapper'>
			<img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'image_attachment_id' ) ); ?>' height='100'>
		</div>
		<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
		<input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'image_attachment_id' ); ?>'>
	<?php

}


add_action( 'admin_footer', 'media_selector_print_scripts' );

function media_selector_print_scripts() {

	$my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );

	?><script type='text/javascript'>

		jQuery( document ).ready( function( $ ) {

			// Uploading files
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this

			jQuery('#upload_image_button').on('click', function( event ){

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();

					// Do something with attachment.id and/or attachment.url here
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#image_attachment_id' ).val( attachment.id );

					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});

					// Finally, open the modal
					file_frame.open();
			});

			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});

	</script><?php

}


function display_theme_panel_fields()
{
	add_settings_section("section", "Configuraciones", null, "theme-options");
	
	add_settings_field("titulo_add", "Titulo del ADD", "display_titulo_add", "theme-options", "section");
    
    add_settings_field("link_add", "Link del ADD", "display_link_add", "theme-options", "section");
    
	add_settings_field("image_attachment_id", "Imagen add Mailing", "media_selector_settings_page_callback", "theme-options", "section");  

	 

    register_setting("section", "titulo_add");
    register_setting("section", "link_add");
	register_setting("section", "image_attachment_id");
	register_setting("section", "image-preview");
}

add_action("admin_init", "display_theme_panel_fields");