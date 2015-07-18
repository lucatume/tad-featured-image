# Featured Image label replacer

*Easy replacement of the built-in WordPress Featured Image meta box link labels and title.*

## Installation
Download the archive and put it in the WordPress plugin folder; activate it.

## Usage
The class exposes one single static method:
	
	function replace_book_featured_image_labels(){
		$args = array(
			'title' => 'Cover picture',
			'set_label' => 'Pick a cover picture',
			'remove_label' => 'Remove the cover picture',
			'context' => 'normal',
			'priority' => 'high'
		);
		tad_Featured_Image::set_labels('book', $args);
	}	
	add_action('plugins_loaded', 'replace_book_featured_image_labels');

that's really all of it.
