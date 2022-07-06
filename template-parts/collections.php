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

get_header(); 
$data = $args['data'];
$total = $args['total'];
$limit = $args['limit'];
$pageNumber = $_GET['page'];
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
	<div class="container" style="margin-top: 10rem;">
	<div class="row" style="margin-top: 5rem">
        <?php echo do_shortcode('[wpdreams_ajaxsearchpro id=3]'); ?>
		<div class="col-lg-12">
			<div class="wrapper wrapper-content animated fadeInRight">

				<div class="ibox-content forum-container">
                    <?php foreach($data as $k=>$v) { ?>
					<div class="forum-item">
						<div class="row">
							<div class="col-md-7">
								<div class="forum-icon">
									<i class="fa fa-bookmark"></i>
								</div>
								<a href="<?php echo "/collections?page=$pageNumber&id=$v->key" ?>" class="forum-item-title"><?php echo wp_trim_words($v->data->title, 12) ?></a>
								<div class="forum-sub-title"><?php echo wp_trim_words($v->data->abstractNote) ?></div>
							</div>
							<div class="col-md-2 forum-info">
								<div>
									<small>Date Added </small>
								</div> 
                            <span class="views-number">
                                <?php echo $v->data->dateAdded ?>
                            </span>

							</div>
							<div class="col-md-2 forum-info">
								<div>
									<small>Creator(s)</small>
								</div>
                            <span class="views-number">
                                <?php
                                    $creators = $v->data->creators;
                                    foreach($creators as $creator) {
                                        echo "$creator->creatorType: $creator->firstName $creator->lastName,";
                                    }
                                ?>
                            </span>

							</div>
						</div>
					</div>
                    <?php } ?>
				</div>
			</div>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="<?php $pageNumber--; echo "/collections?page=$pageNumber" ?>">Previous</a></li>
                    <?php for($i = 1; $i <= ceil($total/$limit); $i++) { ?>
                    <li class="page-item"><a class="page-link" href="<?php echo "/collections?page=$i" ?>"><?php echo $i ?></a></li>
                    <?php } ?>
                    <li class="page-item"><a class="page-link" href="<?php $pageNumber++; echo "/collections?page=$pageNumber" ?>">Next</a></li>
                </ul>
            </nav>
		</div>
	</div>
</div>

<?php
get_footer();
