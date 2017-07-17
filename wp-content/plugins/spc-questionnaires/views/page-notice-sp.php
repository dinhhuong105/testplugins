       <?php get_header(); ?>
        <div id="sb-site" class="qa wrapper new">
            <?php breadcrumb(); ?>
            <section class="newArea qaArea">
                <h1>質問掲示板</h1>
                <p class="detail">子育て奮闘中のママさん達が感じた疑問質問やお役立ち情報まで自由に語り合おう♪</p>
                <?php $spc_thread   = is_plugin_active( 'spc-threads/spc-threads.php' ); ?>
                <?php $spc_question = is_plugin_active( 'spc-questionnaires/spc-questionnaires.php' ); ?>
                <?php if ( $spc_thread ) : ?>
                <?php $spc_option = get_option('spc_options'); ?>
                <?php if( $spc_option['allowpost']) : ?>
                <div class="btnArea">
                    <a href="<?php echo home_url(); ?>/add-thread">新規スレッドを立てる</a>
                </div>
                <?php endif; ?>
                <?php endif; ?>


                <p class="all">スレッド一覧</p>
                <ul class="articleList newList">
                    <?php

                        $wphd_types = array();
                        if ($spc_thread) {
                            $wphd_types[] = 'thread_post';
                        }

                        if ($spc_question) {
                            $wphd_types[] = 'question_post';
                        }

                        $posts_per_page = 15;
                        $val = new WP_Query([
                            'posts_per_page' => $posts_per_page,
                        ]);
                        $query = new WP_Query([
                            // 'cat' =>array(-1, -281),
                            'post_type' => $wphd_types,
                            'posts_per_page' => $posts_per_page,
                            'paged' => ($paged > $val->max_num_pages) ? $val->max_num_pages : $paged,
                        ]);
                        $total_pages = ceil($query->found_posts/$posts_per_page);
                    ?>
                    <?php
                        if ($query -> have_posts()) :
                        while($query -> have_posts()) : $query -> the_post(); ?>
                        <?php
                            $post_cat = get_the_category();
                                usort( $post_cat , '_usort_terms_by_ID');
                            $count = count($post_cat);
                            if($count === 3) {//カテが3
                                $catNameGrandson = $post_cat[2]->cat_name;
                            }elseif($count === 2){//カテが2
                                $catNameGrandson = $post_cat[1]->cat_name;
                            }else{
                                $catNameGrandson = $post_cat[0]->cat_name;
                            }
                            $thumbnail_id = get_post_thumbnail_id();
                            $image = wp_get_attachment_image_src( $thumbnail_id, 'list_thumbnail' );
                            if($post->post_type == 'thread_post' && !$image[0]){
                                // $image[0] = get_template_directory_uri()."/images/noimage-thumbnail-sp.png";
                                $image[0] = '';
                            }
                            $author = get_userdata($post->post_author);
                            $authorRoles = $author->roles;
                            usort( $authorRoles , '_usort_terms_by_ID');
                            $author_id = $post->post_author;
                        ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <?php if (!empty($image[0])) : ?>
                                <div class="imgArea" style="background-image: url(<?php echo $image[0]; ?>);">
                                    <img src='data:image/gif;base64,R0lGODlhAQABAIAAAP//////zCH5BAEHAAAALAAAAAABAAEAAAICRAEAOw=='>
                                </div>
                            <?php else : ?>
                                <div class="imgArea noimage-thumbnail">
                                    <i class="fa fa-picture-o" aria-hidden="true"></i>
                                </div>
                            <?php endif;?>
                            <div class="content">
                                <div class="articleData">
                                    <p class="data"><?php the_time('Y/m/d'); ?></p>
                                    <p class="cat"><?php echo $catNameGrandson; ?></p>
                                </div>
                                <h2><?php the_title(); ?></h2>
                                <div class="articleData">
                                    <?php if($author_id !== '66') { ?>
                                        <p class="name">
                                            <?php
                                                 if  ($authorRoles[0] == 'editor' ) {
                                                     echo '<span class="icon-mugyuu"></span>';
                                                 }
                                            ?>
                                            <?php the_author(); ?>
                                        </p>
                                    <?php } ?>
                                    <div class="pv">
                                        <i class="fa fa-comments" aria-hidden="true"></i>
                                        <?php echo wp_count_comments( get_the_ID() )->total_comments; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php endwhile; else: ?>
                    <p class="none">該当する記事がありません。</p>
                    <?php endif; ?>
                </ul>
                <?php
                    if(function_exists("pagination")) {
                        pagination($total_pages);
                    }
                ?>
                <?php wp_reset_postdata(); ?>
            </section>
    <?php get_footer(); ?>
