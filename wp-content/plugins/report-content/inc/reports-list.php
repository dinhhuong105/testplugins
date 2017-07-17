<?php

if (!class_exists('WPRC_Table')) {
	require_once('class-wprc-table.php');
}

function wprc_add_menu_items()
{
	$permission_options = get_option('wprc_permissions_settings');
	$menu_page_permission = (isset($permission_options['minimum_role_view'])) ? $permission_options['minimum_role_view'] : 'activate_plugins';
	add_menu_page('通報一覧', '通報一覧', $menu_page_permission, 'wprc_reports_page', 'wprc_render_list_page', 'dashicons-format-aside', 10);
}

function wprc_add_notification_menu(){
    global $wpdb;
    global $menu;
    $query = $wpdb->prepare( "SELECT COUNT(*) FROM wp_contentreports WHERE status=%s;", 'new' );
    $count_new = $wpdb->get_var( $query );
    $key_report = false;
    foreach($menu as $key=>$parent_menu){
        if($menu[$key][0] == '通報一覧'){
            $key_report = $key;
            break;
        }
    }
    if($key_report){
        $menu[$key_report][0] .= $count_new>0?"<span class='update-plugins count-1 report-count'><span class='update-count'> $count_new </span></span>":'';
    }
}

add_action('admin_menu', 'wprc_add_menu_items');
add_action('admin_menu', 'wprc_add_notification_menu');

function wprc_db_change_admin_notice()
{
	if (!isset($_GET['report']) || !isset($_GET['action']))
		return;
	if ($_GET['action'] === 'delete')
		$message = count($_GET['report']) . " record(s) deleted from database";
	elseif ($_GET['action'] === 'change_status')
		$message = count($_GET['report']) . " record(s) marked as resolved";
	?>
	<div class="updated">
		<p><?php echo $message; ?></p>
	</div>
	<?php
}

add_action('admin_notices', 'wprc_db_change_admin_notice');

function wprc_render_list_page()
{
	$reportsTable = new WPRC_Table();
	$reportsTable->prepare_items();
	?>
	<div class="wrap">
		<div id="icon-users" class="icon32"><br/></div>
		<h2>通報一覧</h2>
		<style type="text/css">
			.fixed .column-status {
				text-align: center;
			}

			.new-report, .old-report {
				font-size: 24px;
			}

			.new-report {
				color: #C30000;
			}

			.old-report {
				color: green;
			}
		</style>
		<form id="reports-filter" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<?php $reportsTable->display() ?>
		</form>
	</div>
	<?php
}