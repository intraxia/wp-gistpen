<div id="wp-gistpen-insert-wrap">
	<form id="wp-gistpen-insert" action="" tabindex="-1">
		<?php wp_nonce_field( 'wp_gistpen', '_ajax_wp_gistpen', false ); ?>
		<div id="insert-existing">
			<p class="howto">Insert an existing Gistpen</p>
			<div class="gistpen-search-wrap">
				<label>
					<span class="search-label"><?php _e( 'Search Gistpens', 'wp-gistpen' ); ?></span>
					<input type="search" id="gistpen-search-field" class="search-field" />
					<span class="spinner"></span>
				</label>
			</div>
			<div id="search-results" class="query-results">
				<ul></ul>
				<div class="river-waiting">
					<span class="spinner"></span>
				</div>
			</div>
			<div id="most-recent-results" class="query-results">
				<div class="query-notice"><em><?php _e( 'No search term specified. Showing recent Gistpens.', 'wp-gistpen' ); ?></em></div>
				<ul>
					<?php
						$args = array(

							'post_type'      => 'gistpens',
							'post_status'    => 'publish',
							'order'          => 'DESC',
							'orderby'        => 'date',
							'posts_per_page' => 5,

						);

						$recent_gistpen_query = new WP_Query( $args );
					?>

					<?php if ( $recent_gistpen_query->have_posts()) : ?>
						<?php while ( $recent_gistpen_query->have_posts()) : $recent_gistpen_query->the_post(); ?>
							<li>
								<div class="gistpen-title"><?php the_title();  ?></div>
								<div class="gistpen-checkbox"><input type="radio" name="gistpen_id" value="<?php the_ID(); ?>"></div>
							</li>
						<?php endwhile; ?>
					<?php endif; ?>
					<li class="create_new_gistpen">
						<div class="gistpen-title">Create a new Gistpen</div>
						<div class="gistpen-checkbox"><input type="radio" name="gistpen_id" value="new_gistpen"></div>
					</li>
				</ul>
			</div>
		</div>
	</form>
</div>
