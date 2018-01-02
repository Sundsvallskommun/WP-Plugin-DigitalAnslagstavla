<?php
sk_header();
$posts = Digitalboard_Public::get_archive_posts();
?>
<div class="container-fluid archive">

	<div class="row">
		<div class="col-md-2">

		</div>
		<div class="col-md-10">
			<h1 class="archive__title"><?php _e( 'Arkiverade anslag', 'digitalboard_textdomain' ); ?></h1>

			<div class="digitalboard-filter">


				<form action="" method="post">
					<div class="row">
						<div class="form-group col-md-4">
							<label for="digitalboard-date-from">Från datum</label>
							<input id="digitalboard-date-from" type="text" name="digitalboard_date_from"
							       class="form-control datepicker"
							       data-date-format="yyyy-mm-dd"
							       placeholder="<?php _e( 'Publiceringsdatum' ); ?>"
							       value="<?php echo isset( Digitalboard_Public::$filter['digitalboard_date'][0] ) ? date( 'Y-m-d', strtotime( Digitalboard_Public::$filter['digitalboard_date'][0] ) ) : date( 'Y-m-d', strtotime( '-1 year' ) ); ?>">
						</div>

						<div class="form-group col-md-4">
							<label for="digitalboard-date-to">Till datum</label>
							<input id="digitalboard-date-to" type="text" name="digitalboard_date_to"
							       class="form-control datepicker"
							       data-date-format="yyyy-mm-dd"
							       placeholder="<?php _e( 'Publiceringsdatum' ); ?>"
							       value="<?php echo isset( Digitalboard_Public::$filter['digitalboard_date'][1] ) ? date( 'Y-m-d', strtotime( Digitalboard_Public::$filter['digitalboard_date'][1] ) ) : date( 'Y-m-d' ); ?>">
						</div>

						<div class="form-group col-md-4">
							<label for="digitalboard-type">Typ av anslag</label>
							<select name="digitalboard_type" class="form-control" id="digitalboard-type">
								<option value=""><?php _e( 'Visa alla', 'digitalboard-textdomain' ); ?></option>
								<?php foreach ( Digitalboard_Public::get_custom_terms( 'digitalboard-notice' ) as $type ) : ?>
									<option
										value="<?php echo $type->slug; ?>" <?php selected( $type->slug, isset( Digitalboard_Public::$filter['digitalboard-notice'] ) ? Digitalboard_Public::$filter['digitalboard-notice'] : null, true ); ?>><?php echo $type->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-group col-md-12">
							<button type="submit" class="btn btn-primary"><?php _e( 'Filtrera', 'digitalboard-textdomain' ); ?></button>
						</div>

					</div>
					<input type="hidden" name="digitalboard_filter" value="">
				</form>

			</div><!-- .digitalboard-filter -->

			<div class="digitalboard-list">
				<?php if ( ! empty( $title ) ) : ?>
					<h2><?php echo $title; ?></h2>
				<?php endif; ?>
				<div class="list-group">
					<?php if ( $posts->have_posts() ): while ( $posts->have_posts() ): $posts->the_post();?>
						<a href="<?php echo get_permalink( $post->ID ); ?>"
						   class="list-group-item list-group-item-action"><strong><?php echo Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_type', get_the_ID() ), 'digitalboard-notice' )->name; ?> : <?php echo $post->post_title; ?>, <?php echo get_field( 'digitalboard_date', $post->ID ); ?></strong><span class="read-more"><?php the_icon('arrow-right-circle')?></span></a>
					<?php endwhile; else : ?>
						<p><?php _e( 'Sökningen genererade inga resultat.', 'digitalboard_textdomain' ); ?></p>

					<?php endif; ?>
				</div>
			</div><!-- .digitalboard-list -->

			<div class="digitalboard-pagination mt-3">
				<?php
					$big        = 999999999; // need an unlikely integer
					$translated = __( 'Sida', 'digitalboard-textdomain' ); // Supply translatable string

					echo paginate_links( array(
						'base'               => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'             => '?paged=%#%',
						'current'            => max( 1, get_query_var( 'paged' ) ),
						'total'              => $posts->max_num_pages,
						'before_page_number' => '<span class="sr-only">' . $translated . ' </span>'
					) );
				?>
			</div>

		</div>
	</div>


</div>
<?php get_footer(); ?>
