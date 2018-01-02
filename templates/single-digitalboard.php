<?php sk_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
	<div class="container-fluid">
		<div class="single-post__row">

			<aside class="sk-sidebar single-post__sidebar">

				<a href="#post-content" class="focus-only"><?php _e('Hoppa över sidomeny', 'sk_tivoli'); ?></a>

				<?php do_action('sk_page_helpmenu'); ?>

			</aside>

			<div class="single-post__content" id="post-content">

				<?php do_action('sk_before_page_title'); ?>

				<h1 class="single-post__title"><?php echo Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_type', get_the_ID() ), 'digitalboard-notice' )->name; ?> : <?php the_title();?></h1>

				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Organ', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_department', get_the_ID() ), 'digitalboard-department' )->name; ?></div>
				</div>
				<?php if(!empty ( get_field( 'digitalboard_date', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Sammanträdesdatum', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_date', get_the_ID() ); ?></div>
				</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_time', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><?php _e( 'Tid', 'digitalboard_textdomain' ); ?></div>
						<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_time', get_the_ID() ); ?></div>
					</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_place', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><?php _e( 'Plats', 'digitalboard_textdomain' ); ?></div>
						<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_place', get_the_ID() ); ?></div>
					</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_paragraph', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Paragrafer', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_paragraph', get_the_ID() ); ?></div>
				</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_date_up', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Datum då anslaget sätts upp', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_date_up', get_the_ID() ); ?></div>
				</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_date_down', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Datum då anslaget tas ned', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_date_down', get_the_ID() ); ?></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_date_appeal_from', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><?php _e( 'Möjlighet att överklaga beslut under perioden','digtialboard_textdomain' ); ?></div>
						<div class="digitalboard-item__value"><?php printf('%s till och med %s', get_field( 'digitalboard_date_appeal_from', get_the_ID() ),get_field( 'digitalboard_date_appeal_to', get_the_ID() ) ) ;?></div>
					</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_storage', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Förvaringsplats för protokollet', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_storage', get_the_ID() ); ?></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_responsible', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Ansvarig', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_responsible', get_the_ID() ); ?></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_secretary', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><?php _e( 'Sekreterare', 'digitalboard_textdomain' ); ?></div>
						<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_secretary', get_the_ID() ); ?></div>
					</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_contact', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><?php _e( 'Kontakt', 'digitalboard_textdomain' ); ?></div>
						<div class="digitalboard-item__value"><?php echo Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_contact', get_the_ID() ), 'digitalboard-department', true ); ?></div>
					</div>
				<?php endif; ?>

				<?php if( have_rows('digitalboard_related_docs') ): ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Relaterade dokument', 'digitalboard_textdomain' ); ?></div>
					<?php while ( have_rows('digitalboard_related_docs') ) : the_row();
						$file = get_sub_field('digitalboard_related_docs_file');
						$link = get_sub_field('digitalboard_related_docs_link');
						if( !empty( $link )){
							$file['url'] = $link;
						}


						?>
						<div class="digitalboard-item__value"><a <?php echo !empty( get_field('digitalboard_settings_file_target', 'options' ) ) ? 'target="_blank"' : null;?>href="<?php echo !empty( $file['url'] ) ? $file['url'] : '#';?>"><?php echo get_sub_field( 'digitalboard_related_docs_title' ); ?></a></div>
					<?php endwhile; ?>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_text', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><?php _e( 'Övrig information', 'digitalboard_textdomain' ); ?></div>
					<div class="digitalboard-item__value"><?php echo get_field( 'digitalboard_text', get_the_ID() ); ?></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_settings_appeal_url', 'options' ) ) ) : ?>
					<div class="digitalboard-appeal">
						<a href="<?php echo get_field( 'digitalboard_settings_appeal_url', 'options' ); ?>"><?php echo get_field( 'digitalboard_settings_appeal_text', 'options' ); ?></a>
					</div>
				<?php endif; ?>

				<div class="digitalboard-archive digitalboard-link">
					<a href="<?php echo get_post_type_archive_link( $post_type ); ?>" title="<?php _e( 'Klicka här för gå till arkiverade anslag', 'digitalboard_textdomain' ); ?>"><?php _e( 'Klicka här för gå till arkiverade anslag', 'digitalboard_textdomain' ); ?></a>
				</div><!-- .digitalboard-archive -->




				<?php do_action('sk_after_page_title'); ?>

				<?php do_action('sk_before_page_content'); ?>

				<?php the_content(); ?>

				<div class="clearfix"></div>

				<?php do_action('sk_after_page_content'); ?>

			</div>

		</div> <?php //.row ?>

	</div> <?php //.container-fluid ?>

<?php endwhile; ?>

<?php get_footer(); ?>
