<div class="digitalboard-list">
	<?php if(!empty($title)) : ?>
		<h2><?php echo $title; ?></h2>
	<?php endif; ?>
	<div class="list-group">
	<?php if(!empty($posts)) : foreach ( $posts as $post ) : ?>
		<a href="<?php echo get_permalink( $post->ID ); ?>" class="list-group-item list-group-item-action"><strong><?php echo $post->post_title; ?>, <?php echo get_field('digitalboard_date', $post->ID ); ?></strong><span class="read-more"><?php the_icon('arrow-right-circle')?></span></a>
	<?php endforeach; else : ?>
		<p class="list-group__no-result"><?php printf( __( 'Det finns för närvarande inga %s publicerade.', 'digitalboard-textdomain' ), ! empty( $title ) ? mb_strtolower( $title ) : __( 'anslag', 'digitalboard-textdomain' ) ); ?></p>
		<?php endif; ?>
	</div><!-- .list-group -->
</div><!-- .digitalboard-list -->