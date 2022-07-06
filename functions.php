<?php 

// Load main style css
function academix_child_custom_page_style(){
	wp_enqueue_style( 'academix-parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'academix-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'academix-parent-style' ) );
}
add_action( 'wp_enqueue_scripts', 'academix_child_custom_page_style', 155 );

function fetchWithZotero($results, $searchId, $isAjax, $args) {
	$searchTerm = urlencode_deep($args['s']);
	$url = "https://api.zotero.org/users/9650041/items?q=$searchTerm&limit=4";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer IfjshG7UJ9215XIhje7C7BBN'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);
	$totalResults = wp_remote_retrieve_header($response, 'Total-Results');
	foreach($data as $k=>$v) {
		$url;
		if(!empty($v->data->DOI)) {
			$url = "https://doi.org/" . $v->data->DOI;
		} else {
			$url = $v->data->url;
		}
		$obj = (object)['title' => $v->data->title, 'content' => '', 'link' => $url];
		$key = $v->key;
		$results = array_merge([$key => $obj], $results);
	}
	return $results;
}

add_filter( 'asp_results', 'fetchWithZotero', 3, 10);

function collectionPageCheck() {
	
	global $wp_query;
	$requestUri = parse_url($_SERVER['REQUEST_URI']);
	$path = basename( untrailingslashit($requestUri['path']));
	$limit = 5;
	if (strpos( $requestUri['path'], '/collections') === 0) {
		$pageNumber = $_GET['page'];
		$singleView = $_GET['id'];
		$start = $pageNumber > 1 ? ($pageNumber - 1) * $limit : 0;
		
		$reqArgs = array(
			'headers' => array(
				'Authorization' => 'Bearer IfjshG7UJ9215XIhje7C7BBN'
			)
		);

		if(!empty($singleView)) {
			$url = "https://api.zotero.org/users/9650041/items/$singleView";
			$response = wp_remote_get($url, $reqArgs);
			$data = json_decode($response['body']);
			echo get_template_part('template-parts/collection-single', null, ['data' => $data]);
		} else {
			$url = "https://api.zotero.org/users/9650041/items?limit=$limit&start=$start";
			$response = wp_remote_get($url, $reqArgs);
			$data = json_decode($response['body']);
			$totalResults = wp_remote_retrieve_header($response, 'Total-Results');
			echo get_template_part('template-parts/collections', null, ['data' => $data, 'total' => $totalResults, 'limit' => $limit]);
		}
		exit;
	}
}


add_action( 'pre_handle_404', 'collectionPageCheck');


function applyFilters(string $type, string $by, array $queryArray, bool $allowMultiple = false) : string {
	if(!empty($queryArray)) {
		$queryArray['page'] = 1;
		if(array_key_exists($type, $queryArray)) {
			if($allowMultiple) {
				$currentBy = $queryArray[$type];
				$currentByArray = explode("--", $currentBy);

				if(in_array($by, $currentByArray)) {
					// time to unselect, remove
					$currentByArray = array_diff($currentByArray, [$by]);
				} else {
					// time to select, add
					$currentByArray[] = $by;
				}

				$queryArray[$type] = join("--", $currentByArray);
			} else {
				if($by === 'select') {
					$queryArray[$type] = '';
				} else {
					$queryArray[$type] = $by;
				}
				
			}
			return "/?" . http_build_query($queryArray);
		} else {
			return $_SERVER['REQUEST_URI'] . "&$type=$by";
		}
	} else {
		return "/?$type=$by";
	}
}

function getSelectedItems(string $type, array $queryArray) : array {
	$currentBy = $queryArray[$type];
	return explode("--", $currentBy);
}

function includesSelf(string $type, array $queryArray, string $self) : string {
	$selectedItems = getSelectedItems($type, $queryArray);
	return in_array($self, $selectedItems) ? 'checked' : '';
}

function getBaseURL() : string {
	$collection = $_GET['collection'];
	if(!empty($collection)) {
		return "https://api.zotero.org/users/9650041/collections/$collection/items/top";
	}

	return 'https://api.zotero.org/users/9650041/items';
}

function getItems(string $type) : array {
	$urlPart;
	if($type === 'collection') {
		$urlPart = 'collections';
	} else {
		$urlPart = '/items/tags';
	}
	$url = "https://api.zotero.org/users/9650041/$urlPart";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer IfjshG7UJ9215XIhje7C7BBN'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);

	return $data;
}

function getNameById(string $id) : string {
	
	$url = "https://api.zotero.org/users/9650041/collections/$id";
	$reqArgs = array(
		'headers' => array(
			'Authorization' => 'Bearer IfjshG7UJ9215XIhje7C7BBN'
		)
	);
	$response = wp_remote_get($url, $reqArgs);
	$data = json_decode($response['body']);
	return $data->data->name . ' (' . $data->meta->numItems . ')';
}

function resetFilters($queryArray) : string {
	$queryArray['page'] = 1;
	$queryArray['tag'] = '';
	$queryArray['collection'] = '';
	$queryArray['sort'] = '';
	$queryArray['direction'] = '';
	$queryArray['s'] = '';

	return "/?" . http_build_query($queryArray);
}