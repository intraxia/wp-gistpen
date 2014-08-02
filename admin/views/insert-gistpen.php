<div id="wp-gistpen-insert-wrap">
	<form id="wp-gistpen-insert" action="" tabindex="-1">
		<div id="insert-existing">
			<p>Insert an existing Gistpen</p>
			<div class="gistpen-search-wrap">
				<label class="gistpen-search-label">
					<span class="search-label"><?php _e( 'Search Gistpens', 'wp-gistpen' ); ?></span>
					<input type="search" id="gistpen-search-field" class="search-field" />
					<div id="gistpen-search-btn" class="mce-btn">
						<button role="button">Search</button>
						<span class="spinner"></span>
					</div>
				</label>
			</div>
			<div id="select-gistpen" class="query-results">
				<div class="query-notice"><em><?php _e( 'Recent Gistpens.', 'wp-gistpen' ); ?></em></div>
				<ul class="gistpen-list">
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
								<div class="gistpen-radio"><input type="radio" name="gistpen_id" value="<?php the_ID(); ?>"></div>
								<div class="gistpen-title"><?php the_title();  ?></div>
							</li>
						<?php endwhile; ?>
					<?php endif; ?>
					<li class="create_new_gistpen">
						<div class="gistpen-radio"><input type="radio" name="gistpen_id" value="new_gistpen" checked="checked"></div>
						<div class="gistpen-title">Create a new Gistpen</div>
						<div class="clearfix"></div>
						<ul>
							<li>
								<label for="gistpen_title">Gistpen Title</label>
								<input type="text" name="gistpen_title">
							</li>
							<li>
								<label for="gistpen_language">Gistpen Language</label>
								<select name="gistpen_language">
									<?php foreach ( WP_Gistpen::$langs as $language => $slug ):?>
										<option value="<?php echo $slug; ?>"><?php echo $language; ?></option>
									<?php endforeach; ?>
								</select>
							</li>
							<li>
								<label for=gistpen_content"">Gistpen Content</label>
								<textarea type="text" rows="5" name="gistpen_content"></textarea>
							</li>
							<li>
								<label for="gistpen_description">Gistpen Description</label>
								<textarea type="text" rows="5" name="gistpen_description"></textarea>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</form>
</div>
