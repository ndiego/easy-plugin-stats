<?php

defined( 'WPINC' ) or die;


?>
<div id="eps_popup_backdrop" style="display: none"></div>
<div id="eps_popup_wrap" style="display: none" role="dialog" aria-labelledby="">
	<form id="eps_popup_form" tabindex="-1">
		<h1 id="eps_popup_title"><?php _e( 'Easy Plugin Stats', 'easy-plugin-stats' ) ?></h1>
		<button type="button" id="eps_popup_close"><span class="screen-reader-text"><?php _e( 'Close', 'easy-plugin-stats' ); ?></span></button>

		<div id="eps_popup_content_wrap">
		
			<div class="eps-main-container">
				
				<p class="howto" id="eps_choose_stat_type"><?php _e( 'Choose the type of stat you are looking to display. Either a stat from a single plugin, or aggregate stats from multiple plugins.', 'easy-plugin-stats' ); ?></p>
				<div id="eps_stat_type_wrap">
					<label>
						<span><?php _e( 'Stat Type', 'column-class-builder' ); ?></span>
						<select id="eps_stat_type" aria-describedby="eps_choose_stat_type">
							<option value="single"><?php _e( 'Single', 'easy-plugin-stats' ); ?></option>
							<option value="aggregate"><?php _e( 'Aggregate', 'easy-plugin-stats' ); ?></option>
						</select>
					</label>
				</div>
				
				<p class="howto" id="eps_plugin_slug_field"></p>
				<div id="eps_plugin_slug_wrap">
					<label>
						<span><?php _e( 'Plugin Slug', 'easy-plugin-stats' ); ?></span>
						<input type="text" id="eps_plugin_slug" placeholder="easy-plugin-stats" aria-describedby="eps_plugin_slug_field" value="">
					</label>
				</div>
				<div>
					<label>
						<span><?php _e( 'Stat Field', 'easy-plugin-stats' ); ?></span>
						<select id="eps_stat_field" aria-describedby="eps_plugin_slug_field"></select>
					</label>
				</div>

			</div>
			
			<div class="eps-toggle-advanced-container">
				<a id="eps_toggle_advanced"><?php _e( 'Show Advanced Settings', 'easy-plugin-stats' ); ?></a>
			</div>
			
			<div class="eps-advanced-container">
			
				<p class="howto" id="eps_plugin_slug_field"><?php _e( 'Plugin data retrieved from WordPress.org is cached for 12 hours by default.', 'easy-plugin-stats' ); ?></p>
				<div>
					<label>
						<span><?php _e( 'Cache Time', 'easy-plugin-stats' ); ?></span>
						<input type="text" id="eps_cache_time" value="" placeholder="43200" class="small" aria-describedby="eps_plugin_slug_field"><span class="seconds"><?php _e( 'Seconds', 'easy-plugin-stats' ); ?></span>
					</label>
				</div> 
				
				<p class="howto" id="eps_enter_before_after_html"><?php echo sprintf( __( 'Output valid HTML directly before or after the stat content. %sNote:%s Double quotes (i.e. ") will be replaced with single quotes to comply with shortcode markup.', 'easy-plugin-stats' ), '<strong>', '</strong>' ); ?></p>
				<div>
					<label>
						<span><?php _e( 'Before HTML', 'easy-plugin-stats' ); ?></span>
						<textarea id="eps_before_html" rows="3" aria-describedby="eps_enter_before_after_html"></textarea>
					</label>
				</div>
				<div>
					<label>
						<span><?php _e( 'After HTML', 'easy-plugin-stats' ); ?></span>
						<textarea id="eps_after_html" rows="3" aria-describedby="eps_enter_before_after_html"></textarea>
					</label>
				</div>
				
			</div>
			
		</div>


		<div id="eps_popup_footer">
			<div id="eps_popup_cancel">
				<button type="button" class="button"><?php _e( 'Cancel', 'easy-plugin-stats' );?></button>
			</div>
			<div id="eps_popup_reset">
				<button type="button" class="button"><?php _e( 'Reset', 'easy-plugin-stats' );?></button>
			</div>
			<div id="eps_popup_insert">
				<button type="button" class="button button-primary"><?php _e( 'Insert Shortcode', 'easy-plugin-stats' );?></button>
			</div> 
		</div>
	</form>
</div>
<?php ?>