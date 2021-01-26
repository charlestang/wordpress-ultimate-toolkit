<?php
/**
 * Admin Panel: Options of excerption.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage admin
 */

/**
 * The class of auto excerption admin panel.
 */
class WUT_Admin_Excerption extends WUT_Admin_Panel {

	public function __construct() {
		parent::__construct( __( 'Auto Excerption', 'wut' ), 'excerpt' );
	}

	public function default_options() {
		return array(
			'enabled'      => true,
			'paragraphs'   => 3,
			'words'        => 250,
			'tip_template' => '<br/><br/><span class="readmore"><a href="%permalink%" title="%title%">Continue Reading--%total_words% words totally</a></span>',
		);
	}

	public function form( $options ) {
		?>
		<table class="form-table" role="presentation"><tbody>
			<tr valign="top">
				<th scope="row"><label for="excerpt_enabled"><?php _e( 'Enable This Feature ', 'wut' ); ?></label></th>
				<td><input 
						id="<?php echo $this->get_field_id( 'excerpt_enabled' ); ?>"
						name="<?php echo $this->get_field_name( 'enabled' ); ?>"
						type="hidden"
						value="0"/>
					<input 
						id="<?php echo $this->get_field_id( 'excerpt_enabled' ); ?>"
						name="<?php echo $this->get_field_name( 'enabled' ); ?>"
						type="checkbox"
						value="1"<?php checked( $options['enabled'] ); ?>/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="excerpt_paragraphs_number"><?php _e( 'Paragraphs Number', 'wut' ); ?></label></th>
				<td><input 
					id="<?php echo $this->get_field_id( 'excerpt_paragraphs_number' ); ?>"
					name="<?php echo $this->get_field_name( 'paragraphs' ); ?>"
					type="text" size="10" 
					value="<?php echo $options['paragraphs']; ?>"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="excerpt_words_number"><?php _e( 'Words Number', 'wut' ); ?></label></th>
				<td><input 
					id="<?php echo $this->get_field_id( 'excerpt_words_number' ); ?>"
					name="<?php echo $this->get_field_name( 'words' ); ?>"
					type="text" size="10" 
					value="<?php echo $options['words']; ?>"/></td>
			</tr>
			<tr>
				<th scope="row"><label for="excerpt_continue_reading_tip_template"><?php _e( '"Continue Reading" tip template:', 'wut' ); ?></label></th>
				<td><fieldset>
					<p>Use variables:</p>
					<ul>
						<li>%total_words% --- The number of words in the post.</li>
						<li>%title% --- Post title.</li>
						<li>%permalink --- The permanent link of the post.</li>
						<li><?php echo esc_html( '<br/>' ); ?> --- new line.</li>
					</ul>
					<p>HTML tags supported.</p>
					<p><textarea 
							id="<?php echo $this->get_field_id( 'excerpt_continue_reading_tip_template' ); ?>"
							name="<?php echo $this->get_field_name( 'tip_template' ); ?>"
							class="large-text code" 
							rows="3"><?php echo esc_attr( $options['tip_template'] ); ?></textarea></p>
				</filedset></td>
			</tr>
		</tbody></table>
		<?php
	}

	public function update( $new_options, $old_options ) {
		return $new_options;
	}
}
