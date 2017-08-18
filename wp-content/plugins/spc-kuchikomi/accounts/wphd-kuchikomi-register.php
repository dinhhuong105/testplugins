<?php get_header(); ?>

<?php #echo get_avatar( get_user_meta('wphd_profile_picture'), 12 ); ?>

<?php
    
    // $url = apply_filters( 'get_avatar_url', get_user_meta(12, 'wphd_profile_picture')[0], 12 );
    // echo $url;exit;

    // $args = array(

    // );
    // echo get_avatar_url( 12, $args );exit;

    // // $user_meta = get_user_meta(12);
    

    // echo '<pre>';
    // print_r(get_user_meta(12, 'wphd_profile_picture')[0]);
    // echo '</pre>';
    // exit;
?>

<div class="main-wrap hd-contents">
	<div class="hd-register">
	    <section class="hd-register-form">

        <?php if ( is_user_logged_in() ) : ?>
            <script type="text/javascript">window.location.href="<?php echo home_url('/my-page'); ?>"</script>
            <?php echo exit;?>
        <?php else: ?>
            
            <div id="hd-register-form">
                <div class="register-form">
                    <form id="registerform" method="post" action="">
                        <section class="register-blog active" id="register-blog-form">
                            <div class="register-group register-avatar">
                                <label for="user-register-picture">Avatar</label>
                                <div class="avatar-preview" for="user-register-picture">
                                    <img id="preview-image" alt="no avatar" for="user-register-picture" src="<?php echo WPHD_KUCHIKOMI_PLUGIN_IMAGES . 'no_avatar.png'; ?>" width="200px" height="200px" />
                                </div>
                                
                            
                                <label class="img-btn">
                                    <i class="fa fa-camera" aria-hidden="true"></i>画像を選択
                                    <input type="file" name="picture" id="user-register-picture" class="input">
                                </label>

                            </div>
                            <div class="register-group register-nickname">
                                <label for="user-nickname" class="required">Nickname</label>
                                <input type="text" name="nickname" placeholder="Nickname" id="user-nickname" class="input" value="" size="20">
                            </div>
                            <div class="register-group register-email">
                                <label for="user-email" class="required">Email</label>
                                <input type="email" name="email" placeholder="Email" id="user-email" class="input" value="" size="20">
                            </div>
                            <div class="register-group register-pass">
                                <label for="user-pass" class="required">Password</label>
                                <input type="password" name="password" placeholder="password" id="user-pass" class="input" value="" size="20" autocomplete="off">
                            </div>
                            <div class="register-group register-gender">
                                <label for="user-gender" class="required">Gender</label>
                                <div class="gender-group">
                                    <label class="item" for="user-gender-female"><input type="radio" name="gender" id="user-gender-female" class="input" value="female"> Female &nbsp;</label>
                                    <label class="item" for="user-gender-male"><input type="radio" name="gender" id="user-gender-male" class="input" value="male"> Male</label>
                                </div>
                            </div>

                            <div class="register-group register-birthday">
                                <label for="user-birthday" class="required">Birthday</label>
                                <div class="birthday-group">
                                    <select class="birthday_y" name="birthday[year]">
                                        <option value="">0000</option>
                                        <?php $current_year = date('Y', time()); ?>
                                        <?php for ($i = $current_year; $i > 1970; $i--) : ?>
                                            <option value="<?php echo $i?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <label class="birthday-label">年</label>

                                    <select class="birthday_m" name="birthday[month]">
                                        <option value="">00</option>
                                        <?php for ($i = 1; $i <= 12 ; $i++) : ?>
                                            <?php $month = ($i < 10) ? '0' . $i : $i; ?>
                                            <option value="<?php echo $i; ?>"><?php echo $month; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <label class="birthday-label">月</label>

                                    <select class="birthday_d" name="birthday[day]">
                                        <option value="">00</option>
                                        <?php for ($i = 1; $i <= 31 ; $i++) : ?>
                                            <?php $day = ($i < 10) ? '0' . $i : $i; ?>
                                            <option value="<?php echo $i; ?>"><?php echo $day; ?></option>
                                        <?php endfor; ?> 
                                    </select>
                                    <label class="birthday-label">日</label>
                                </div>
                            </div>
                            <div class="register-group register-num-of-child">
                                <label for="user-gender" class="required">Number of child</label>
                                <select name="num_of_child">
                                    <option value="">0</option>
                                    <?php for ($i = 1; $i <= 10 ; $i++) : ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="register-group register-submit">
                                <input type="button" name="wp-next" id="wp-next" class="button button-primary" value="Register">
                                <?php wp_nonce_field( 'wphd-kuchikomi-register-nonce', 'register_nonce' ); ?>
                            </div>
                            <div class="clear"></div>
                        </section>


                        <section class="register-blog" id="register-blog-confirm">
                            <div class="register-group register-avatar">
                                <label for="user-register-picture">Avatar</label>
                                <div class="avatar-preview" for="user-register-picture">
                                    <img id="preview-image" alt="no avatar" for="user-register-picture" src="<?php echo WPHD_KUCHIKOMI_PLUGIN_IMAGES . 'no_avatar.png'; ?>" width="200px" height="200px" />
                                </div>
                            </div>
                            <div class="register-group register-nickname">
                                <label for="user-nickname">Nickname</label>
                                <p class="confirm-nickname"></p>
                            </div>
                            <div class="register-group register-email">
                                <label for="user-email">Email</label>
                                <p class="confirm-email"></p>
                            </div>
                            <div class="register-group register-pass">
                                <label for="user-pass">Password</label>
                                <p class="confirm-pass"></p>
                            </div>
                            <div class="register-group register-gender">
                                <label for="user-gender">Gender</label>
                                <div class="gender-group">
                                    <p class="confirm-gender"></p>
                                </div>
                            </div>

                            <div class="register-group register-birthday">
                                <label for="user-birthday">Birthday</label>
                                <p class="confirm-birthday"></p>
                            </div>
                            <div class="register-group register-num-of-child">
                                <label for="user-gender">Number of child</label>
                                <p class="confirm-number-of-child"></p>
                            </div>
                            
                            <div class="register-group register-submit">
                                <input type="button" name="wp-back" id="wp-back" class="button button-primary" value="Back">
                                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Register">
                            </div>
                            <div class="clear"></div>
                        </section>
                        

                        <section class="register-blog" id="register-blog-finish">
                            <div class="register-group register-nickname">
                                <h1 class="finish-notice">投稿完了しました</h1>
                            </div>
                            <div class="register-group register-submit">
                                <input type="hidden" name="action" value="register_account_front" />
                                <input type="hidden" name="submitted" id="submitted" value="true" />
                                <a href="<?php echo home_url('/my-page'); ?>">My page</a>
                                <a href="<?php echo home_url('/'); ?>">Home page</a>
                            </div>
                            <div class="clear"></div>
                        </section>
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
<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>