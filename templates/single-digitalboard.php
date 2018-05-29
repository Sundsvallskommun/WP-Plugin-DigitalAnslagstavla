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
					<div class="digitalboard-item__title" tabindex="12"><h2><?php _e( 'Organ', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value" tabindex="13"><p><?php echo Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_department', get_the_ID() ), 'digitalboard-department' )->name; ?></p></div>
				</div>
				<?php if(!empty ( get_field( 'digitalboard_date', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Sammanträdesdatum', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_date', get_the_ID() ); ?></p></div>
				</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_time', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><h2><?php _e( 'Tid', 'digitalboard_textdomain' ); ?></h2></div>
						<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_time', get_the_ID() ); ?></p></div>
					</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_place', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><h2><?php _e( 'Plats', 'digitalboard_textdomain' ); ?></h2></div>
						<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_place', get_the_ID() ); ?></p></div>
					</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_paragraph', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Paragrafer', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_paragraph', get_the_ID() ); ?></p></div>
				</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_date_adjust', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><h2><?php _e( 'Justeringsdatum', 'digitalboard_textdomain' ); ?></h2></div>
						<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_date_adjust', get_the_ID() ); ?></p></div>
					</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_date_up', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Datum då anslaget sätts upp', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_date_up', get_the_ID() ); ?></p></div>
				</div>
				<?php endif; ?>
				<?php if(!empty ( get_field( 'digitalboard_date_down', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Datum då anslaget tas ned', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_date_down', get_the_ID() ); ?></p></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_date_appeal_from', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><h2><?php _e( 'Möjlighet att överklaga beslut under perioden','digtialboard_textdomain' ); ?></h2></div>
						<div class="digitalboard-item__value"><p><?php printf('%s till och med %s', get_field( 'digitalboard_date_appeal_from', get_the_ID() ),get_field( 'digitalboard_date_appeal_to', get_the_ID() ) ) ;?></p></div>
					</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_storage', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Förvaringsplats för protokollet', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_storage', get_the_ID() ); ?></p></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_responsible', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Ansvarig', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_responsible', get_the_ID() ); ?></p></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_secretary', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><h2><?php _e( 'Sekreterare', 'digitalboard_textdomain' ); ?></h2></div>
						<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_secretary', get_the_ID() ); ?></p></div>
					</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_contact', get_the_ID() ) ) ) : ?>
					<div class="digitalboard-item">
						<div class="digitalboard-item__title"><h2><?php _e( 'Kontakt', 'digitalboard_textdomain' ); ?></h2></div>
						<div class="digitalboard-item__value"><p><?php echo Digitalboard_Public::get_taxonomy_name( get_field( 'digitalboard_contact', get_the_ID() ), 'digitalboard-department', true ); ?></p></div>
					</div>
				<?php endif; ?>

				<?php if( have_rows('digitalboard_related_docs') ): ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Relaterade dokument', 'digitalboard_textdomain' ); ?></h2></div>
					<?php while ( have_rows('digitalboard_related_docs') ) : the_row();
						$link_to_file = get_sub_field('digitalboard_related_docs_file');
						$link = get_sub_field('digitalboard_related_docs_link');
						if( !empty( $link )){
							$file['url'] = $link;
						}else{
							$file['url'] = $link_to_file;
						}
						?>
						<div class="digitalboard-item__value"><p><a <?php echo !empty( get_field('digitalboard_settings_file_target', 'options' ) ) ? 'target="_blank"' : null;?>href="<?php echo !empty( $file['url'] ) ? $file['url'] : '#';?>"><?php echo get_sub_field( 'digitalboard_related_docs_title' ); ?></a></p></div>
					<?php endwhile; ?>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_text', get_the_ID() ) ) ) : ?>
				<div class="digitalboard-item">
					<div class="digitalboard-item__title"><h2><?php _e( 'Information', 'digitalboard_textdomain' ); ?></h2></div>
					<div class="digitalboard-item__value"><p><?php echo get_field( 'digitalboard_text', get_the_ID() ); ?></p></div>
				</div>
				<?php endif; ?>

				<?php if(!empty ( get_field( 'digitalboard_settings_appeal_url', 'options' ) ) ) : ?>
					<div class="digitalboard-appeal">
						<p><a href="<?php echo get_field( 'digitalboard_settings_appeal_url', 'options' ); ?>"><?php echo get_field( 'digitalboard_settings_appeal_text', 'options' ); ?></a></p>
					</div>
				<?php endif; ?>



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
