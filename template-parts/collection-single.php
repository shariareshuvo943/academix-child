<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package academix
 */

$data = $args['data'];
global $academix_options;
global $post;
$prefix = '_academix_';

$el_check = get_post_meta( $post->ID , '_elementor_data', true );
$display_metabox_page_banner = get_post_meta( $post->ID,  $prefix . 'display_page_banner', true );
$display_page_breadcrumbs = get_post_meta( $post->ID,  $prefix . 'display_page_breadcrumbs', true );

if( $el_check == true ){
    $el_class = '';
} else{
    $el_class = 'site-padding';
}

?>
	<div class="container" style="min-height: 100vh;">
    <img src="https://thelibrary.santhigramschool.com/wp-content/uploads/2022/06/banner-2.jpg" style="width: 98vw; height: auto; margin-top: -15rem" />
	<div class="row">
        
        <h2 style="margin-top: -10rem; padding: 2rem; margin-bottom: 7rem; font-size: 3rem; font-weight: bold; color: white;"><?php echo $data->data->title ?></h2>
		<div class="col-lg-12">
			<div class="wrapper wrapper-content animated fadeInRight">
            <p>Creator(s): <?php
                $creators = $data->data->creators;
                foreach($creators as $creator) {
                    echo "$creator->creatorType: $creator->firstName $creator->lastName, ";
                }
            ?></p>
            <h3>Abstract</h3>
            <?php echo $data->data->abstractNote ?>
			</div>
		</div>
	</div>
</div>
