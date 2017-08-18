<?php get_header(); ?>

<div class="main-wrap hd-contents">
	<div class="hd-login">
	    <section class="hd-login-form">

        <?php if ( is_user_logged_in() ) : ?>
            <script type="text/javascript">window.location.href="<?php echo home_url('/my-page'); ?>"</script>
            <?php echo exit;?>
        <?php else: ?>
            
            <div id="hd-login-form">
                <div class="login-form">
                    <form id="loginform" method="post" action="">
                        <p class="login-group login-name">
                            <label for="username" class="required">Username/Email</label>
                            <input type="text" name="username" id="username" class="input" value="" size="20">
                        </p>
                        <p class="login-group login-pass">
                            <label for="password" class="required">Password</label>
                            <input type="password" name="password" id="password" class="input" value="" size="20">
                        </p>
                        
                        <p class="login-group login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" checked="checked"> Remember Me</label></p>
                        <p class="login-group login-submit">
                            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Log In">
                            <input type="hidden" name="redirect_to" value="<?php echo home_url('/my-page'); ?>">
                            <?php wp_nonce_field( 'wphd-kuchikomi-nonce', 'security' ); ?>
                        </p>
                    </form>
                </div>
                <div class="clear"></div>
            </div>

        <?php endif; ?> 
		</section>
	</div>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>