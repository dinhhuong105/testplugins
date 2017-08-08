<?php $nowthemes = get_template_directory(); ?>
<?php if( cf_is_mobile()) : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/related-entries-sp.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/related-entries-sp.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/related-entries-sp.php' );
        }
	?>
<?php else : ?>
	<?php
		$nowthemesfile = file_exists($nowthemes.'/related-entries-pc.php');
		if ($nowthemesfile) {
            include_once($nowthemes.'/related-entries-pc.php');
        } else {
            include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/related-entries-pc.php' );
        }
	?>
<?php endif; ?>
