<?php
require_once('../../../wp-blog-header.php');
$actiontotake = $_POST['action'];
if($actiontotake=="delete"){
$id= $_POST['id'];
global $wpdb;
$tejus_dj_cat_table_name = $wpdb->prefix."tejus_djcat";
$query = "DELETE FROM ".$tejus_dj_cat_table_name." WHERE catid =".$id;
$wpdb->query($query);
}
?>