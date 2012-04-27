<?php
/*
Plugin Name: category image
Plugin URI: http://www.tejuscreative.com/tejus_cat_img.html
Description: a plugin to add images to category
Version: 2.0
Author:Dhananjay singh.
Author URI: http://www.tejuscreative.com
License:  GPL2
.
Any other notes about the plugin go here
.
*/
?>
<?php
/* call a function named tejus_dj_cat_myplugin_activate to create a datbase at the activation of plugin*/
register_activation_hook( __FILE__, 'tejus_dj_cat_myplugin_activate' );
function tejus_dj_cat_myplugin_activate(){
global $wpdb;
$tejus_dj_cat_table_name = $wpdb->prefix."tejus_djcat";
$sql = "CREATE TABLE  ".$tejus_dj_cat_table_name." (
         catid INT NOT NULL PRIMARY KEY,
		 path VARCHAR(100)
       );" ;
$wpdb->query($sql);
}
/* call a function named tejus_dj_cat_myplugin_deactivate to delete already craeted datbase at the deactivation of plugin*/
register_deactivation_hook( __FILE__, 'tejus_dj_cat_myplugin_deactivate' );
function tejus_dj_cat_myplugin_deactivate(){
global $wpdb;
$tejus_dj_cat_table_name = $wpdb->prefix."tejus_djcat";
 $sql= "DROP TABLE IF EXISTS ".$tejus_dj_cat_table_name ;
 $wpdb->query($sql);
}
/* here to include javasript and css files*/
add_action('init', 'tejus_dj_cat_myplugin_init');
/* function to include javascript file*/
function tejus_dj_cat_myplugin_init() {
wp_enqueue_script( 'jquery' );
$tejus_dj_cat_paths = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
wp_enqueue_script('tejus_dj_catjs', $tejus_dj_cat_paths . "js/tejus_dj_catjs.js");
wp_enqueue_style( 'tejus_dj_catcss',  $tejus_dj_cat_paths . "css/tejus_dj_catcss.css");
}
add_action('admin_menu','tejus_dj_cat_toaddmymenu');
/* to add various menus of plugin to admin panel*/
function tejus_dj_cat_toaddmymenu(){
/* adding top level menu */
add_menu_page( 'add images to categories', 'Tejus cat Img', 'manage_options', 'dj_createformimage', 'tejus_dj_cat_callitmyfunction',
plugins_url( '/images/wp-icon.png',__FILE__ ) );
}
/* function to handle uploading of image*/
function tejus_dj_cat_callitmyfunction(){
?>
<?php if($_POST['updatevalueform']){
$catiddj = $_POST['cat'];
//$djcatformimages = $_POST['djcatimages'];
if(isset($_FILES[ 'djcatimages' ]) && ($_FILES[ 'djcatimages']['size'] > 0)) {
require_once( ABSPATH . 'wp-admin/includes/file.php' );
// Get the type of the uploaded file. This is returned as "type/extension"
 $arr_file_type = wp_check_filetype(basename($_FILES['djcatimages']['name']));
 $uploaded_file_type = $arr_file_type['type'];

// Set an array containing a list of acceptable formats
 $allowed_file_types = array('image/jpg','image/jpeg','image/gif','image/png');
 // If the uploaded file is the right format
 if(in_array($uploaded_file_type, $allowed_file_types)) {

 // Options array for the wp_handle_upload function. 'test_upload' => false
 $upload_overrides = array( 'test_form' => false ); 
 // Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
  $returnigurlofimage = wp_handle_upload($_FILES['djcatimages'], $upload_overrides);
 }
if($returnigurlofimage['url']){ $savethisimageurl = $returnigurlofimage['url'] ; 
//echo $catiddj;
//echo $savethisimageurl;
global $wpdb;
$tejus_dj_cat_table_name = $wpdb->prefix."tejus_djcat";
$sql ='SELECT catid FROM '.$tejus_dj_cat_table_name.' WHERE catid ='.$catiddj;
$djcatimageexists = $wpdb->get_row($sql, ARRAY_A);
if($djcatimageexists['catid']){
$sql = "UPDATE ".$tejus_dj_cat_table_name." SET path = '".$savethisimageurl."' WHERE catid= ".$catiddj;
$wpdb->query($sql);
}
else{
$sql = "INSERT INTO ".$tejus_dj_cat_table_name."  VALUES ({$catiddj},'{$savethisimageurl}') ";
$wpdb->query($sql);
}
}
 }
}
?>
<div class="djinfomationaboutplugin">
<p>
<h3>To call image of any category in any template file you will need to call this function.</h3>
<br/>
tejus_dj_getcatimg($catid);
<br/>
$catid is the id of that category.
<br/>
it will return url of image associated with that category if ,that category has no associated image it will return 0.
</p>
</div>
<div class="wholeform">
<form name="createform" method="post" enctype="multipart/form-data">
<input name="djcatimages" type="file" value="" size="100" >
<?php
/*to print drop down of categories*/
$args = array(
'orderby'            => 'name', 
'order'              => 'ASC',  
'hierarchical'       => 1, 
'echo'				 => 0,
'class'              => 'cat',
'selected'			 => $sel,
'taxonomy'			 => 'category',
'hide_empty'		 => false
);
$dropdown = wp_dropdown_categories( $args );
echo $dropdown;
?>
<input type="hidden" value="1" name="updatevalueform"/>
<input type="submit" name="createbutton" value="upload" />
</form>
</div>
<?php
/*get all available category with images*/
global $wpdb;
$tejus_dj_cat_table_name = $wpdb->prefix."tejus_djcat";
$sql= "SELECT * FROM ". $tejus_dj_cat_table_name;
$djalladdedimagestocats = $wpdb->get_results($sql, ARRAY_A);
?>
<div class="wholecategorywimagebox">
<table class="widefat" style="width: 80%">
<thead>
<tr>
<th>CATEGORY ID</th>
<th>IMAGE</th>
<th>CATEGORY NAME</th>
<th>DELETE</th>
</tr>
</thead>
<tfoot>
<tr>
<th>CATEGORY ID</th>
<th>IMAGE</th>
<th>CATEGORY NAME</th>
<th>DELETE</th>
</tr>
</tfoot>
<tbody>
<?php
foreach($djalladdedimagestocats as $djalladdedimagestocat){
echo '<tr>';
echo  '<td id="imageidis'.$djalladdedimagestocat['catid'].'">'.$djalladdedimagestocat['catid'].'</td><td><img src="'.$djalladdedimagestocat['path'].'" width="50" height="50"/></td><td>'.get_cat_name($djalladdedimagestocat['catid']).'</td><td><input type="button" name="delete" value="delete" id = "dj_image_delete'.$djalladdedimagestocat['catid'].'"  ></td>';
echo '</tr>';
}
?>
</tbody>
</table>
</div>
<script>
 jQuery(function($){ 
 $.ajaxSetup({
  error:function(x,e){
   if(x.status==0){
   alert('You are offline!!\n Please Check Your Network.');
   }else if(x.status==404){
   alert('Requested URL not found.');
   }else if(x.status==500){
   alert('Internel Server Error.');
   }else if(e=='parsererror'){
   alert('Error.\nParsing JSON Request failed.');
   }else if(e=='timeout'){
   alert('Request Time out.');
   }else {
   alert('Unknow Error.\n'+x.responseText);
   }
  }
 });
<?php
$tejus_dj_cat_paths = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
global $wpdb;
$tejus_dj_cat_table_name = $wpdb->prefix."tejus_djcat";
$sql= "SELECT * FROM ". $tejus_dj_cat_table_name;
$djalladdedimagestocats = $wpdb->get_results($sql, ARRAY_A);
foreach($djalladdedimagestocats as $djalladdedimagestocat){
?>
 $("#dj_image_delete<?php echo $djalladdedimagestocat['catid']; ?>").click( function (){
var id<?php echo $djalladdedimagestocat['catid']; ?> = $("#<?php echo 'imageidis'.$djalladdedimagestocat['catid']; ?>").html();
var datastring<?php echo $djalladdedimagestocat['catid']; ?> = "id="+id<?php echo $djalladdedimagestocat['catid']; ?>+"&action=delete";
//alert(datastring<?php echo $djalladdedimagestocat['catid']; ?>);
 $.ajax({
      type: "POST",
	  url: "<?php echo $tejus_dj_cat_paths; ?>dj_cat_update.php",
      data: datastring<?php echo $djalladdedimagestocat['catid']; ?>,

      success: function(html) {
       
       window.location.replace("<?php echo $_SERVER['REQUEST_URI']; ?>");
       
      }
     });
});
<?php } ?>
 });
</script>
<?php } ?>
<?php
/*function to be used to get image value associated with categories*/
function tejus_dj_getcatimg($catid){
global $wpdb;
$tejus_dj_cat_table_name = $wpdb->prefix."tejus_djcat";
$sql ='SELECT path FROM '.$tejus_dj_cat_table_name.' WHERE catid ='.$catid;
$djcatimageexists = $wpdb->get_row($sql, ARRAY_A);
if($djcatimageexists['path']){
$returnthisvalue = $djcatimageexists['path'];
}
else{
$returnthisvalue =0;
}
return $returnthisvalue;
}
?>