<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 4/7/17
 * Time: 10:47 AM
 */
?>
<style type="text/css">
    .form-table th {font-weight: normal!important; width:270px;}
</style>
<div class="wrap">
    <h1>掲示板、アンケートの設定</h1>
    <form method="post" name="spc_setting">
        <h2>掲示板設定</h2>
        <table class="form-table">
            <tbody>
            <tr>
                <th scop="row"><label>スレッドの作成</label></th>
                <td>
                    <input type="radio" name="spc_options[allowpost]" value="0" <?php echo ($spc_option['allowpost']==0 ? "checked" : ""); ?> id="allowpost0"/><label for="allowpost0">管理者のみ</label>
                    <input type="radio" name="spc_options[allowpost]" value="1" <?php echo ($spc_option['allowpost']==1 ? "checked" : ""); ?> id="allowpost1"/><label for="allowpost1">誰でもOK</label>
                </td>
            </tr>
            <tr>
                <th scop="row"><label for="img_of_thread">スレッド本文に挿入できる画像の数</label></th>
                <td><input type="number" name="spc_options[thread_img_no]" value="<?php echo esc_attr( $spc_option['thread_img_no'] ); ?>" placeholder="枚数を入力" id="img_of_thread"/><label for="img_of_thread">枚</label></td>
            </tr>
            <tr>
                <th scop="row"><label for="img_of_less">レス本文に挿入できる画像の数</label></th>
                <td><input type="number" name="spc_options[less_img_no]" value="<?php echo esc_attr( $spc_option['less_img_no'] ); ?>" placeholder="枚数を入力" id="img_of_less"/><label for="img_of_less">枚</label></td>
            </tr>
            </tbody>
        </table>
        <h2>アンケート設定</h2>
        <table class="form-table">
            <tbody>
            <tr>
                <th scop="row"><label for="img_of_q">アンケート本文に挿入できる画像の数</label></th>
                <td><input type="number" name="spc_options[q_img_no]" value="<?php echo esc_attr( $spc_option['q_img_no'] ); ?>" placeholder="枚数を入力" id="img_of_q"/><label for="img_of_q">枚</label></td>
            </tr>
            <tr>
                <th scop="row"><label for="img_of_a">アンケートの回答に挿入できる画像の数</label></th>
                <td><input type="number" name="spc_options[a_img_no]" value="<?php echo esc_attr( $spc_option['a_img_no'] ); ?>" placeholder="枚数を入力" id="img_of_a"/><label for="img_of_a">枚</label></td>
            </tr>
            </tbody>
        </table>
        <h2>その他の設定</h2>
        <table class="form-table">
            <tbody>
            <tr>
                <th scop="row"><label for="report_email">通報おしらせメールの送付先</label></th>
                <td><input type="text" name="spc_options[report_email]" value="<?php echo esc_attr( $spc_option['report_email'] ); ?>" placeholder="メールアドレスを入力" id="report_email"/></td>
            </tr>
            <tr>
                <th scop="row"><label for="num_of_report">一覧画面の表示件数</label></th>
                <td><input type="number" name="spc_options[report_no]" value="<?php echo esc_attr( $spc_option['report_no'] ); ?>" placeholder="件数を入力" id="num_of_report"/><label for="num_of_report">件</label></td>
            </tr>
            <tr>
                <th scop="row"><label for="notice_slug">掲示板一覧用スラッグ</label></th>
                <td><input type="text" name="spc_options[notice_slug]" value="<?php echo esc_attr( $spc_option['notice_slug'] ); ?>" placeholder="掲示板一覧用スラッグ" id="notice_slug"/></td>
            </tr>
            <tr>
                <th scop="row"><label for="num_of_report">新規トピック作成ページ用スラッグ</label></th>
                <td><input type="text" name="spc_options[add_thread_slug]" value="<?php echo esc_attr( $spc_option['add_thread_slug'] ); ?>" placeholder="新規トピック作成ページ用スラッグ" id="add_thread_slug"/></td>
            </tr>
            </tbody>
        </table>
        <h2>Option</h2>
        <table class="form-table">
            <tbody>
            <tr>
                <th scop="row"><label for="list_unit1">単位１の選択肢</label></th>
                <td><input type="text" name="spc_options[list_unit1]" value="<?php echo esc_attr( $spc_option['list_unit1'] ); ?>" placeholder="単位１の選択肢" id="list_unit1"/><label for="list_unit1">Input options separate by comma(,)</label></td>
            </tr>
            <tr>
                <th scop="row"><label for="list_unit2">単位2の選択肢</label></th>
                <td><input type="text" name="spc_options[list_unit2]" value="<?php echo esc_attr( $spc_option['list_unit2'] ); ?>" placeholder="単位2の選択肢" id="list_unit2"/><label for="list_unit2">Input options separate by comma(,)</label></td>
            </tr>
            </tbody>
        </table>



        <h2>Setting image for front</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scop="row"><label for="upload_max_filesize">Upload max filesize</label></th>
                    <td><input type="number" min="0" max="<?php echo (int) ini_get('post_max_size') ;?>" name="spc_options[upload_max_filesize]" value="<?php echo isset($spc_option['upload_max_filesize']) ? esc_attr( $spc_option['upload_max_filesize'] ) : ''; ?>" placeholder="" id="upload_max_filesize"/><label for="upload_max_filesize"> MB (Upload max filesize : <?php echo ini_get('post_max_size') ;?>)</label></td>
                </tr>
                <tr>
                    <th scop="row"><label>Thumbnail size</label></th>
                    <td>
                        <label for="file_thumbnail_size_w">Width</label> <input class="small-text" type="number" min="0" name="spc_options[file_thumbnail_size_w]" value="<?php echo isset($spc_option['file_thumbnail_size_w']) ? esc_attr( $spc_option['file_thumbnail_size_w'] ) : ''; ?>" placeholder="" id="file_thumbnail_size_w"/> &nbsp;
                        <!-- <label for="file_thumbnail_size_h">Height</label> <input class="small-text" type="number" min="0" name="spc_options[file_thumbnail_size_h]" value="<?php echo isset($spc_option['file_thumbnail_size_h']) ? esc_attr( $spc_option['file_thumbnail_size_h'] ) : ''; ?>" placeholder="" id="file_thumbnail_size_h"/> -->
                    </td>
                </tr>
                <tr>
                    <th scop="row"><label for="file_resize">Size for resizing</label></th>
                    <td>
                        <label for="file_resize_w">Width</label> <input class="small-text" type="number" min="0" name="spc_options[file_resize_w]" value="<?php echo isset($spc_option['file_resize_w']) ? esc_attr( $spc_option['file_resize_w'] ) : ''; ?>" placeholder="" id="file_resize_w"/> &nbsp;
                        <!-- <label for="file_resize_h">Height</label> <input class="small-text" type="number" min="0" name="spc_options[file_resize_h]" value="<?php echo isset($spc_option['file_resize_h']) ? esc_attr( $spc_option['file_resize_h'] ) : ''; ?>" placeholder="" id="file_resize_h"/> -->
                    </td>
                </tr>
            </tbody>
        </table>


        <p class="submit"><input type="submit" id="submit" class="button button-primary" value="Save Setting"/></p>
    </form>
</div>
