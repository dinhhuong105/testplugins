<?php get_header(); ?>
       <?php
            $postCat = get_the_category();
            usort( $postCat , '_usort_terms_by_ID');
            $author_id = $post->post_author;
            $author = get_userdata($post->post_author);
        ?>

        <div id="sb-site" class="wrapper single qaSingle">
            <?php if(count(get_the_category())>0):?>
            	<?php breadcrumb(); ?>
            <?php else: ?>
            	<div id="breadcrumb">
            		<ul class="breadcrumbList">
            			<li><a href="<?php echo home_url('/'); ?>">トップ</a></li>
                        <li><i class="fa fa-angle-right arrowIcon"></i><a href="<?php echo home_url('/'); ?>notice"><span>質問掲示板</span></a></li>
            		</ul>
            	</div>
            <?php endif; ?>
            <article class="singleArea qaSingleArea">
                <section>
                    <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                    <?php
                        $postCat = get_the_category();
                        usort( $postCat , '_usort_terms_by_ID');
                        $childCat = '';
                        $catNameGrandson = '';
                        $catIdGrandson = '';
                        $count = count($postCat);
                        
                        if ($count) {
                            if ($count === 3) {//カテが3
                                $childCat = $postCat[1]->cat_name;
                                $catNameGrandson = $postCat[2]->cat_name;
                                $catIdGrandson = $postCat[2]->cat_ID;
                            } elseif ($count === 2) {//カテが2
                                $catNameGrandson = $postCat[1]->cat_name;
                                $catIdGrandson = $postCat[1]->cat_ID;
                            } else {
                                $catNameGrandson = $postCat[0]->cat_name;
                                $catIdGrandson = $postCat[0]->cat_ID;
                            }
                        }

                        $author_id = $post->post_author;
                        $author = get_userdata($post->post_author);
                        $userLebel = $author -> roles;
                        usort( $userLebel , '_usort_terms_by_ID');
                        $slug_name = $post->post_name;
                        $thumbnail_id = get_post_thumbnail_id();
                        $image = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
                    ?>

                    <div class="top">
                        <div class="imgArea noImage">
                            <img src="<?php echo $image[0]; ?>">
                        </div>
                        <div class="articleData">
                            <div class="dataArea">
                               <p class="data"><?php the_time('Y/m/d'); ?></p>
                               <p class="pv">
                                    <?php
                                        if ( function_exists ( 'wpp_get_views' ) ) {
                                            echo '<i class="fa fa-heart" aria-hidden="true"></i>';
                                            echo wpp_get_views ( get_the_ID() ); 
                                        }
                                    ?>
                               </p>
                            </div>
                            <h1><?php the_title(); ?></h1>
                        </div>
                   </div>

                    <div class="content">
                        <?php the_content(); ?>
                        <div class="articleData">
                            <p class="commentCount"><i class="fa fa-comments" aria-hidden="true"></i><?php echo wp_count_comments( get_the_ID() )->total_comments; ?></p>
                        </div>
                    </div>
                    <div class="btnArea">
                        <a href="#send">アンケートに答える
                        </a>
                    </div>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <p class="none">記事が見つかりませんでした。</p>
                    <?php endif; ?>
                </section>
            </article>
            <?php comments_template(); ?>
            <?php include_once( SPCV_CUSTOME_PLUGIN_DIR . 'views/related-entries.php' ); ?>
    <?php get_footer(); ?>
