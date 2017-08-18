<?php get_header(); ?>

<div class="main-wrap hd-contents">
	<div class="hd-login">
		<!-- section -->
	    <section class="aa_loginForm">
	        <?php 
	            global $user_login;
	            // In case of a login error.
	            if ( isset( $_GET['login'] ) && $_GET['login'] == 'failed' ) : ?>
    	            <div class="aa_error">
    		            <p><?php _e( 'FAILED: Try again!', 'AA' ); ?></p>
    	            </div>
	            <?php endif;
            	// If user is already logged in.
            	if ( is_user_logged_in() ) : 
                    echo 'Welcome to my page';

                	echo '<pre>';
                	print_r($current_user);
                	echo '</pre>';

                    echo '<br/><a href="'. wp_logout_url(home_url('/login')) .'">Logout</a>';

                else: 
                    // wp_redirect(home_url( '/my-page' ));exit;
                    echo '<script type="text/javascript">window.location.href="'. home_url('/login') .'"</script>';exit;
                endif;
        	?> 
		</section>
		<!-- /section -->
	</div>
	<div class="clear"></div>
</div>


<?php get_footer(); ?>