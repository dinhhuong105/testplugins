
<?php
    global $post;
    // アンケート公開ディレクトリまでのURL
    $questionary_url = home_url()."/questionary/public/";
    $recommend_class = "articleList plusList";

    // 最遠親カテゴリまたはカテゴリ自身のIDが1かどうか判断し、代入
    $postCat = get_the_category();
    usort( $postCat , '_usort_terms_by_ID');
    $count = count($postCat);
    $catIdGrandson = '';
    $catNameGrandson = '';

    if ($count) {
        if ( $count === 3) {//カテが3
            $catNameGrandson = $postCat[2]->cat_name;
            $catIdGrandson = $postCat[2]->cat_ID;
        } elseif ( $count === 2) {//カテが2
            $catNameGrandson = $postCat[1]->cat_name;
            $catIdGrandson = $postCat[1]->cat_ID;
        } else {
            $catNameGrandson = $postCat[0]->cat_name;
            $catIdGrandson = $postCat[0]->cat_ID;
        }
    }

    $args = array (
        'posts_per_page' => 5,
        'post_type' => $post->post_type, #投稿種別を絞る
        'cat' => $catIdGrandson,
    );

    // 検索条件の共通項を追加
    $args += array(
        'orderby' => 'rand',
    );

    $query = new WP_Query($args);
?>

<section class="plusArea">
    <?php echo '<h2 class="heading"><span>あ</span><span>わ</span><span>せ</span><span>て</span><span>読</span><span>み</span><span>た</span><span>い</span></h2>'; ?>
     <ul class="<?php echo($recommend_class)?>">
     <?php if( $query -> have_posts() ): ?>
     <?php while ($query -> have_posts()) : $query -> the_post(); ?>
         <?php
             $author = get_userdata($post->post_author);
             $authorRoles = $author->roles;
             usort( $authorRoles , '_usort_terms_by_ID');
             $author_id = $post->post_author;
             $thumbnail_id = get_post_thumbnail_id();
             $image = wp_get_attachment_image_src( $thumbnail_id, 'list_thumbnail' );
             if($post->post_type == 'thread_post' && !$image[0]){
                 // $image[0] = get_template_directory_uri()."/images/noimage-thumbnail-sp.png";
                 $image[0] = '';
             }
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
                        <p class="pv">
                            <?php
                                if ( function_exists ( 'wpp_get_views' ) ) {
                                    echo '<i class="fa fa-heart" aria-hidden="true"></i>';
                                    echo wpp_get_views ( get_the_ID() );
                                }
                            ?>
                        </p>
                    </div>
                </div>
            </a>
        </li>
     <?php endwhile; else: ?>
        <li class="none">該当する記事がありません。</li>
     <?php endif; ?>
     <?php wp_reset_postdata(); ?>
     </ul>
</section>

<?php
// 関連アンケートの表示。投稿種別によっては非表示
// if( $post->post_type !== "movingimage_post" && $post->post_type !== "item_post"):
//     global $wpdb;
//
//     // dbからデータを取得する
//     $query = "
//         SELECT
//                *
//         FROM
//                `boards`
//         WHERE
//                `term_id` LIKE {$catId}
//                AND `status` LIKE 0
//         ORDER BY
//                RAND() ASC
//         LIMIT
//                4
//     ";
//     $results = $wpdb->get_results( $query, OBJECT );

    ?>
    <?php //if($catId !== 1):?>
    <!-- <section class="plusArea">
        <h1 class="heading">
            <span>関</span><span>連</span><span>の</span><span>あ</span><span>る</span><span>ア</span><span>ン</span><span>ケ</span><span>ー</span><span>ト</span>
        </h1>
       <ul class="articleList plusList">
            <?php //foreach($results as $board):?>
            <?php
                // $img_dir = $questionary_url.'/assets/img/';
                // $thumb_dir = $img_dir.'uploads/thumb/';
                //
                // if(  is_file($thumb_dir.$board->image) )
                // {
                //     $img_class = "imgArea";
                //     $img_html = "<img src=\"{$thumb_dir}{$board->image}\">";
                // }
                // else
                // {
                //     $img_class = "imgArea noImage";
                //
                //     $cat = get_category($board->term_id);
                //     $cat_list = get_ancestors( $board->term_id, 'category' );
                //     $term_id = !empty($cat) && $cat->parent ? end($cat_list) : $board->term_id;
                //
                //     switch($term_id)
                //     {
                //         case 2: //こどものこと
                //             $img_html = "<img src=\"{$img_dir}babyBig.png\" class=\"catChild\">";
                //             break;
                //
                //         case 3: //ままのこと
                //             $img_html = "<img src=\"{$img_dir}mamaBig.png\" class=\"catMama\">";
                //             break;
                //
                //             default:
                //             $img_html = "<img src=\"{$img_dir}mamaBig.png\" class=\"catMama\">";
                //     }
                //  }

            ?>
                <li>
                    <a href="<?php// echo(home_url()."/questionary/board/view/{$board->id}")?>">
                        <div class="<?php //echo( $img_class ) ?>">
                            <?php// echo( $img_html ) ?>
                        </div>
                        <div class="content">
                            <div class="articleData">
                                <p class="data"><?php //echo(date('Y/m/d',$board->created_at)); ?></p>
                                <p class="cat"><?php// echo $cat->cat_name; ?></p>
                            </div>
                            <h2><?php //echo( $board->title ); ?></h2>
                            <div class="articleData">
                                <p class="name"></p>
                                <p class="pv">
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                    <?php// echo( $board->count ); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </li>
                <?php //endforeach;?>
                <?php //if( empty($results) ): ?>
                    <li class="none">該当するアンケートがありません。</li>
                <?php //endif;?>
       </ul>
    </section> -->
    <?php // endif ?>
<?php //endif ?>
