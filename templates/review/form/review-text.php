<?php
/**
 * Review form textarea
 * 
 * @package  SuperBlogPack
 * @version  1.0
 */
?>
<p>
	<textarea rows="5" required="required"><?php echo esc_textarea( $user_review['_review'] ); ?></textarea>
</p>