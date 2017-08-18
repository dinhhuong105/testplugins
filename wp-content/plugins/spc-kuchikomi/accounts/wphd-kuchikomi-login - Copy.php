<?php get_header(); ?>

<div class="main-wrap hd-contents">
	<div class="hd-login">
	    <section class="hd-login-form">

	    <?php
	    	$login  = (isset($_GET['login']) ) ? $_GET['login'] : 0;
	    	if ( $login === "failed" ) {
			  	echo '<p class="login-msg"><strong>ERROR:</strong> Invalid username and/or password.</p>';
			} elseif ( $login === "empty" ) {
			  	echo '<p class="login-msg"><strong>ERROR:</strong> Username and/or Password is empty.</p>';
			} elseif ( $login === "false" ) {
			  	echo '<p class="login-msg"><strong>ERROR:</strong> You are logged out.</p>';
			}
	    ?>


        <?php if ( is_user_logged_in() ) : ?>
            <script type="text/javascript">window.location.href="<?php echo home_url('/my-page'); ?>"</script>
            <?php echo exit;?>
        <?php else: 
                // Login form arguments.
                $args = array(
                    'redirect'       => home_url( '/my-page' ),
                    'form_id'        => 'loginform',
                    'label_username' => __( 'Username' ),
                    'label_password' => __( 'Password' ),
                    'label_remember' => __( 'Remember Me' ),
                    'label_log_in'   => __( 'Log In' ),
                    'id_username'    => 'user_login',
                    'id_password'    => 'user_pass',
                    'id_remember'    => 'rememberme',
                    'id_submit'      => 'wp-submit',
                    'remember'       => true,
                    'value_username' => NULL,
                    'value_remember' => true
                );
                
                // Calling the login form.
                wp_login_form( $args );
            endif;
        ?> 
		</section>
	</div>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>