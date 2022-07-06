<?php

get_header();

$data;
$limit = 10;
$total;
$pageNumber = $_GET['page'] ? $_GET['page'] : 1;
$start = $pageNumber > 1 ? ($pageNumber - 1) * $limit : 0;
$sort = $_GET['sort'] ? $_GET['sort'] : '';
$direction = $_GET['direction'] ? $_GET['direction'] : '';
$requestUri = parse_url($_SERVER['REQUEST_URI']);
$queryArray;
parse_str($requestUri['query'], $queryArray);

$tags = getSelectedItems('tag', $queryArray);
$baseUrl = getBaseURL();

if(array_key_exists('s', $queryArray)) {
	$searchTerm = $queryArray['s'];
	$tagUrl = "";
	if(!empty($tags)) {
		foreach($tags as $tag) {
			$tagUrl .= "tag=$tag&";
		}
	}
	$url = "$baseUrl?q=$searchTerm&limit=$limit&start=$start&sort=$sort&direction=$direction&$tagUrl";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer IfjshG7UJ9215XIhje7C7BBN'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);
	$total = wp_remote_retrieve_header($response, 'Total-Results');
}

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
	<div class="container">
	<div class="row" style="margin-top: 5rem">
		<div class="col-lg-8">
			<div class="wrapper wrapper-content animated fadeInRight">

				<div class="ibox-content forum-container">
                    <?php 
					if(!empty($data)) {
						foreach($data as $k=>$v) {
							$url;
							if(!empty($v->data->DOI)) {
								$url = "https://doi.org/" . $v->data->DOI;
							} else {
								$url = $v->data->url;
							}
						
					?>

					<div class="forum-item">
						<div class="row">
							<div class="col-md-11">
								<div class="forum-icon">
									<?php echo (($pageNumber - 1) * $limit) + $k + 1 ?>
									<i class="fa fa-bookmark"></i>
								</div>
								<a target="_blank" href="<?php echo $url ?>" class="forum-item-title"><?php echo wp_trim_words($v->data->title, 12) ?></a>
								<div class="forum-sub-title" style="color:inherit;">
									<?php
										$creators = $v->data->creators;
										if($creators) {
											foreach($creators as $creator) {
												echo "$creator->firstName $creator->lastName, ";
											}
										}
									?>
								</div>
								<div class="forum-sub-title" style="color:inherit; font-weight:bold"><?php echo date("Y", strtotime($v->data->dateAdded)); ?></div>
								<div class="forum-sub-title"><?php echo wp_trim_words($v->data->abstractNote, 20) ?></div>
							</div>
							<div class="col-md-1">
								<i class="fas fa-heart"></i>
							</div>
						</div>
					</div>
                    <?php }} ?>
				</div>
			</div>						

            <nav aria-label="Page navigation example">
                <ul class="pagination">
					
					<?php if($pageNumber > 1) {
						$queryArray['page'] = $pageNumber - 1;
					?>
                    	<li class="page-item"><a class="page-link" href="<?php echo "/?" . http_build_query($queryArray) ?>">Previous</a></li>
                    <?php } ?>
					
					<li class="page-item"><a class="page-link active"><?php echo $pageNumber ?></a></li>
					
					<?php for($i = $pageNumber + 1; $i < $pageNumber + 5; $i++) {
						if($total && $i <= ceil($total/$limit)) {
						$queryArray['page'] = $i;
					?>
                    	<li class="page-item"><a class="page-link" href="<?php  echo "/?" . http_build_query($queryArray) ?>"><?php echo $i ?></a></li>
                    <?php }} ?>

					<?php if($total && $pageNumber < ceil($total/$limit)) { 
						$queryArray['page'] = $pageNumber + 1;
					?>
                    	<li class="page-item"><a class="page-link" href="<?php  echo "/?" . http_build_query($queryArray) ?>">Next</a></li>
					<?php } ?>
                </ul>
            </nav>
		</div>
		<div class="col-lg-4">
			<h3 style="margin-top: 3.2rem;">Filters</h3> <a class="btn btn-primary" href="<?php echo resetFilters($queryArray) ?>">Reset Filters</a>
			
			<h4>Sort by</h4>
			<div class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
					$sort = preg_replace('/(?<!\ )[A-Z]/', ' $0', $sort);
					echo $sort ? ucwords($sort) : "Select Option";
				?> <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<?php if(!empty($sort)) { ?>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort', 'select', $queryArray);
					?>">Select Option</a></li>
					<?php } ?>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort', 'dateAdded', $queryArray);
					?>">Date Added</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','dateModified', $queryArray);
					?>">Date Modified</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','title', $queryArray);
					?>">Title</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','creator', $queryArray);
					?>">Creator</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','itemType', $queryArray);
					?>">ItemType</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','date', $queryArray);
					?>">Date</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','publisher', $queryArray);
					?>">Publisher</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','publicationTitle', $queryArray);
					?>">Publication Title</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','journalAbbreviation', $queryArray);
					?>">Journal Abbreviation</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','language', $queryArray);
					?>">Language</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','accessDate', $queryArray);
					?>">Access Date</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','libraryCatalog', $queryArray);
					?>">Library Catalog</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','callNumber', $queryArray);
					?>">Call Number</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','rights', $queryArray);
					?>">Rights</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('sort','addedBy', $queryArray);
					?>">Added By</a></li>
				</ul>
			</div>
			<div class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
					$orderBy = $_GET['direction'];
					echo $orderBy ? $orderBy === 'asc' ? "Ascending" : "Descending" : "Select Order";
				?> <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('direction', 'asc', $queryArray);
					?>">Ascending</a></li>
					<li class="dropdown-item"><a href="<?php
						echo applyFilters('direction','desc', $queryArray);
					?>">Descending</a></li>
				</ul>
			</div>
			<h4>Collections</h4>
			<div class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
					$collection = $_GET['collection'];
					echo $collection ? getNameById($collection) : "Select Collection";
				?> <b class="caret"></b></a>
				<ul class="dropdown-menu">
				<?php
					$collections = getItems('collection');
					if(!empty($collection)) {
						echo '<li class="dropdown-item"><a href="' . applyFilters('collection', 'select', $queryArray) . '"> ' . "Select Collection" . "</a></li>";
					}
					foreach($collections as $k=>$v) {
						$name = $v->data->name;
						$numItems = $v->meta->numItems;
						echo '<li class="dropdown-item"><a href="' . applyFilters('collection', $v->key, $queryArray) . '"> ' . $name . " ($numItems)</a></li>";
					}
				?>
				</ul>
			</div>
			<h4>Tags</h4>
			<?php
				$tags = getItems('tags');
				foreach($tags as $k=>$v) {
				?>
				<a ><input href="<?php echo applyFilters('tag', urldecode($v->tag), $queryArray, true) ?>" type="checkbox" id="<?php echo urldecode($v->tag) ?>" name="<?php echo urldecode($v->tag) ?>" value="<?php echo $v->tag ?>" onclick="window.location = this.getAttribute('href')"  <?php echo includesSelf('tag', $queryArray, urldecode($v->tag)); ?> >
				<?php $numItems = $v->meta->numItems;
					echo " $v->tag ($numItems) </a> </br>"; }
				?>
			
			
		</div>	
	</div>
</div>

<?php
get_footer();
