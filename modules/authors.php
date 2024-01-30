<?php

use ImproveSEO\View;
use ImproveSEO\FlashMessage;

function improveseo_authors() {
	global $wpdb;

	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$limit = isset($_GET['limit']) ? $_GET['limit'] : 50;
	$offset = isset($_GET['paged']) ? $_GET['paged'] * $limit - $limit : 0;

	if ($action == 'index'):
		$results = count_users();

		$users = get_users(array(
			'role' => 'improveseo_user',
			'offset' => $offset,
			'number' => $limit
		));

		$pages = ceil($results['avail_roles']['improveseo_user'] / $limit);
		$page = floor($offset / $limit) + 1;

		View::render('authors.index', compact('users', 'results', 'page', 'pages'));
	elseif ($action == 'create'):
		View::render('authors.create');
	elseif ($action == 'do_create'):
		$total = $_POST['users'];

		@set_time_limit(0);

		include_once IMPROVESEO_ROOT .'/includes/authors/firstnames.php';
		include_once IMPROVESEO_ROOT .'/includes/authors/lastnames.php';

		$firstnamesTotal = sizeof($firstnames);
		$lastnamesTotal = sizeof($lastnames);
		$url = parse_url(get_option('siteurl'));

		for ($i = 1; $i <= $total; $i++) {
			$fidx = rand(0, $firstnamesTotal - 1);
			$lidx = rand(0, $lastnamesTotal - 1);

			$firstname = $firstnames[$fidx];
			$lastname = $lastnames[$lidx];

			$email = $firstname . $lastname .'@'. $url['host'];

			if (email_exists($email)) {
				$total++;
				continue;
			}

			$formatData = array(
                'user_login'    =>  $firstname . $lastname,
                'user_pass'     =>  $firstname . $lastname . time() . rand(0, 1000000),
                'user_email'    =>  $email,
                'display_name'	=>	$firstname .' '. $lastname,
                'role'          =>  'improveseo_user'
            );

            $user_id = wp_insert_user($formatData);
            add_user_meta($user_id, 'improveseo_user', 1);
		}

		FlashMessage::success('All '. $total .' authors created.');
		wp_redirect(admin_url('admin.php?page=improveseo_authors'));
		exit;

	endif;
}
