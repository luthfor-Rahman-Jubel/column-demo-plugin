<?php
/*
Plugin Name: Column Demo
Plugin URI: https://jubayer.me.bd
Description: This plugin Add new column And Unset previous Column
Version: 1.0
Author: Jubel Ahmed
Author URI: https://jubel.com.bd
License: GPL2 or Later
Text Domain: column-demo
Domain Path: /lang/
 */

 function coldemo_bootstrap(){
     load_plugin_textdomain("column-demo",false,dirname(__FILE__)."/lang");
 }

 add_action('plugins_loaded','coldemo_bootstrap');
 function coldemo_post_column($column){
     print_r($column);
     unset($column['categories']);
     unset($column['tags']);
     unset($column['author']);
     unset($column['date']);
     unset($column['comments']);
     $column['id'] = __('Post ID','column-demo');
     $column['wordcount'] = __('Word Count','column-demo');
     $column['author']="Author";
     $column['date']="Date";
     $column['categories']="Categories";
     $column['comments']="Comments";
     $column['thumbnail']=__('Thumbnail','column-demo');


     return $column;
    
    }
    add_filter('manage_posts_columns','coldemo_post_column');

    function coldemo_page_column($column){
        unset($column['date']);
        unset($column['comments']);
        $column['id']=__('Pages ID','column-demo');
        $column['thumbnail']=__('Thumbnail','column-demo');
        $column['date']="Date";
        $column['comments']="Comments";
        return $column;
    }
    add_filter('manage_pages_columns','coldemo_page_column');
 function coldemo_post_column_data($column,$post_id){
     if('id'==$column){
        echo $post_id;
     }elseif ('thumbnail'==$column) {
        $thumbnail = get_the_post_thumbnail($post_id,array(80,80));
        echo $thumbnail;

     }elseif ('wordcount'==$column) {
         
        /* $_post   = get_post($post_id);
         $content = $_post->post_content;
         $wordn   = str_word_count(strip_tags($content));
        echo $wordn; */
         $wordn   = get_post_meta($post_id,'wordn',true);
        echo $wordn;
     }

     

 }
 add_action('manage_posts_custom_column','coldemo_post_column_data',10,2);

 function column_demo_sortable_column($column){
    $column['wordcount']='wordn';
    return $column;
 }
 add_filter('manage_edit-post_sortable_columns','column_demo_sortable_column');


 function coldemo_pages_column_data($column,$post_id){
    if('id'==$column){
        echo $post_id;
    }elseif ('thumbnail'==$column) {
       $thumbnail = get_the_post_thumbnail($post_id,array(80,80));
       echo $thumbnail;
    }
 }

 add_action('manage_pages_custom_column','coldemo_pages_column_data',10,2);

 /* function coldemo_set_word_count(){
$_posts = get_posts(array(
    'posts_per_page'=>-1,
    'post_type'=>'post'
));
foreach ($_posts as $p) {
    $content = $p->post_content;
    $wordn   = str_word_count(strip_tags($content));
    update_post_meta($p->ID,'wordn',$wordn);
}

}
 add_action('init','coldemo_set_word_count'); */

function coldemo_set_column_data( $wpquery ){
    if(! is_admin()){
        return;
    }
    $orderby = $wpquery->get( 'orderby' );
    if( 'wordn' == $orderby ){
        $wpquery->set( 'meta_key','wordn');
        $wpquery->set( 'orderby','meta_value_num');
    }
}
 add_action('pre_get_posts','coldemo_set_column_data');

function update_coldemo_wordcount_on_save_post($post_id){
    $_post   = get_post($post_id);
    $content = $_post->post_content;
    $wordn   = str_word_count(strip_tags($content));
    update_post_meta($_post->ID,'wordn',$wordn);

 }
 add_action('save_post','update_coldemo_wordcount_on_save_post');










 function coldemo_filter(){
     if(isset($_GET['post_type']) && $_GET['post_type']!='post' ){ //display only for posts page
         return;
     }
     $filter_value = isset($_GET['DEMOFILTER'] ) ? $_GET['DEMOFILTER'] : '';
     $values = array(
        '0'=>__('Select Status','column-demo'),
        '1'=>__('Some posts','column-demo'),
        '2'=>__('Some Extra posts','column-demo'),
        '3'=>__('New Demo posts','column-demo')
     );
     ?>
     <select name="DEMOFILTER" >
     <?php 
     foreach ($values as $key => $value) {
         printf("<option value='%s' %s>%s</option>",$key,
         $key == $filter_value ? "Selected = 'selected'" : '',
         $value 
        );
     }
     ?>
     </select>
     <?php 
 }
 add_action('restrict_manage_posts','coldemo_filter');

 function coldemo_filter_data( $wpquery ){
    if(! is_admin() ){
        return;
    }

    $filter_value = isset($_GET['DEMOFILTER'] ) ? $_GET['DEMOFILTER'] : '';
    if('1' == $filter_value){
        $wpquery->set('post__in',array(353,295,348) );
    }elseif ('2' == $filter_value) {
        $wpquery->set('post__in',array(160,165,170,174));
    }elseif ('3' == $filter_value) {
        $wpquery->set('post__in',array(97,99,103));
    }
 }
 add_action('pre_get_posts','coldemo_filter_data');


function coldemo_thumbnail_filter(){
if(isset($_GET['post_type']) && $_GET['post_type']!='post'){
    return;
}
$filter_value = isset($_GET['THFILTER'])? $_GET['THFILTER'] : '';
$values = array(
    '0'=>__('Thumbnail Status','column-demo'),
    '1'=>__('Has Thumbnails','column-demo'),
    '2'=>__('No Thumbnails ','column-demo')

);
?>
<select name="THFILTER">
<?php 
    foreach ($values as $key => $value) {
       printf("<option value='%s' %s>%s</option>",$key,
       $key == $filter_value ? "Selected='selected'" : '',
       $value 
        );
    }
?>
</select>
<?php 
}
 add_action('restrict_manage_posts','coldemo_thumbnail_filter');

function coldemo_wc_filter(){
    if(isset($_GET['post_type']) && $_GET['post_type']!='post'){
        return;
    }
    $filter_value = isset($_GET['WCFILTER'])?$_GET['WCFILTER'] : '';
    $values = array(
        '0'  => __('Word Count','column-demo'),
        '1'  => __('Above 400','column-domo'),
        '2'  => __('200 to  400','column-domo'),
        '3'  => __('200 or Below','column-domo')
    );
    ?>
    <select name="WCFILTER" >
        <?php 
        foreach ($values as $key => $value) {
            printf(" <option value='%s' %s>%s</option> ",$key,
            $key == $filter_value ? "Selected='selected'" : '',
            $value 
            );
        }
        ?>
    </select>
    <?php 
}
 add_action('restrict_manage_posts','coldemo_wc_filter');



function coldemo_thumbnail_data( $wpquery ){
if(! is_admin()){
    return;
}
$filter_value = isset($_GET['THFILTER'])?$_GET['THFILTER'] : '';
 //$wpquery->set('posts_per_page',5);
    if('1' == $filter_value){
        $wpquery->set('meta_query',array(
            array(
                'key'      => '_thumbnail_id',
                'compare'  => 'EXISTS'
            )
        ) );
    }elseif ('2' == $filter_value) {
        $wpquery->set('meta_query',array(
            array(
                'key' => '_thumbnail_id',
                'compare'=>'NOT EXISTS'
            )
            ));
    }
}
add_action('pre_get_posts','coldemo_thumbnail_data');

function coldemo_wc_data( $wpquery ){
    if(! is_admin()){
    return;
    }
    $filter_value = isset($_GET['WCFILTER'])? $_GET['WCFILTER'] : '';

    if('1'== $filter_value){
        $wpquery->set('meta_query',array(
            array(
                'key'     => 'wordn',
                'value'   => '400',
                'compare' => '>=',
                'type'    => 'NUMERIC'

            )
        ));
    }elseif ('2' == $filter_value) {
        $wpquery->set('meta_query',array(
           array(
                'key'    => 'wordn',
                'value'  => array(200,400),
                'compare'=> 'BETWEEN',
                'type'   => 'NUMERIC'
                
            )
        ));
    }elseif ('3'==$filter_value) {
        $wpquery->set('meta_query',array(
            array(
                'key'      => 'wordn',
                'value'    => 200,
                'compare'  => '<=',
                'type'     => 'NUMERIC'
            )
        ));
    }

}
add_action('pre_get_posts','coldemo_wc_data');











?>
