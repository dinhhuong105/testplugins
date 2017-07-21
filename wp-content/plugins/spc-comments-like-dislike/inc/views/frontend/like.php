<div class="cld-like-wrap  cld-common-wrap">
	<?php
	$liked_ips = get_comment_meta( $comment_id, 'cld_ips', true );
	$user_ip = $this->get_user_IP();
	if ( empty( $liked_ips ) ) {
		$liked_ips = array();
	}

	// $this->print_array($liked_ips);
	$user_ip_check = (in_array( $user_ip, $liked_ips )) ? 1 : 0;
	?>
	<div class="actionArea">
        <div class="good">
	<a href="javascript:void(0);" class="cld-like-trigger cld-like-dislike-trigger <?php echo ($user_ip_check == 1 || isset( $_COOKIE['cld_' . $comment_id] )) ? 'cld-prevent liked' : ''; ?>" title="<?php _e( 'Like', 'comments-like-dislike' ); ?>" data-comment-id="<?php echo $comment_id; ?>" data-trigger-type="like" data-restriction="ip<?php //echo esc_attr( $cld_settings['basic_settings']['like_dislike_resistriction'] ); ?>" data-user-ip="<?php echo $user_ip; ?>" data-ip-check="<?php echo $user_ip_check; ?>">
		<?php
		$template = 'template-2';//esc_attr( $cld_settings['design_settings']['template'] );
		switch ( $template ) {
			case 'template-1':
				?>
				<i class="fa fa-thumbs-up"></i>
				<?php
				break;
			case 'template-2':
				?>
				<i class="icon fa fa-heart" aria-hidden="true"></i>
				<?php
				break;
			case 'template-3':
				?>
				<i class="fa fa-check"></i>
				<?php
				break;
			case 'template-4':
				?>
				<i class="fa fa-smile-o"></i>
				<?php
				break;
			case 'custom':
				if ( $cld_settings['design_settings']['like_icon'] != '' ) {
					?>
					<img src="<?php echo esc_url( $cld_settings['design_settings']['like_icon'] ); ?>"/>
					<?php
				}
				break;
		}
		/**
		 * Fires when template is being loaded
		 *
		 * @param array $cld_settings
		 *
		 * @since 1.0.0
		 */
		do_action( 'cld_like_template', $cld_settings );
		?>
    	</a>
    	</div>


    	<?php
    		$post_type = get_post_type(get_the_ID());
    		if ($post_type == 'thread_post') :
    	?>
    	<!-- reply comment -->
    	<div class="reply">
            <a href="#send"><i class="fa fa-reply" aria-hidden="true"></i></a>
        </div>
        <!-- end reply comment -->
    	<?php endif; ?>

	</div>
	<div class="countArea">
        <i class="icon fa fa-heart" aria-hidden="true"></i><span class="cld-like-count-wrap cld-count-wrap">共感！<?php echo (empty( $like_count )) ? 0 : number_format( $like_count ); ?>件</span>
    </div>
	
</div>