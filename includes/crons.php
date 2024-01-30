<?php

use Carbon\Carbon;
use ImproveSEO\Spintax;
use ImproveSEO\Models\Task;

// Add every minute schedule
function improveseo_add_every_minute($schedules) {
     $schedules['every_minute'] = array(
        'interval'  => 60,
        'display'   => __( 'Every Minute', 'textdomain' )
    );

    return $schedules;
}
add_filter('cron_schedules', 'improveseo_add_every_minute');


// Schedules
function improveseo_parse_tasks() {
	global $wpdb;

	$model = new Task();

	$projects = $model->getActive();
	foreach ($projects as $project) {
		$options = $project->options;
		$posts = 1;
		$geo = isset($options['local_geo_country']);

		// Use Dripfeed
		if (isset($options['dripfeed_type'])) {
			switch ($options['dripfeed_type']) {
				case 'per-day':
					$interval = 1440 / $options['dripfeed_x'];
					break;
				case 'over-days':
					$interval = $project->max_iteration / (1440 * $options['dripfeed_x']);
					break;
			}
		} else {
			$interval = 2 / 30;
		}

		if ($interval < 2) {
			$posts = floor(2 / $interval);
		}

		//$spintax = Spintax::parse($content->content);

		$last_update = Carbon::parse($project->updated_at);
		$now = Carbon::now();

		// Time to post
		if ($now->diffInMinutes($last_update) >= $interval) {
			$data = json_decode(base64_decode($project->content), true);

			$title = $data['title'];
			$content = $data['content'];

			$titleSpintax = Spintax::parse($data['title']);
			$titleMax = Spintax::count($titleSpintax);

			$contentSpintax = Spintax::parse($content);
			$contentMax = Spintax::count($contentSpintax);

			$posts = min($posts, $project->max_iterations);

			if (isset($options['exif_locations'])) {
				$options['exif_locations'] = str_replace('\"', '"', $options['exif_locations']);

				$exifLocations = json_decode($options['exif_locations']);
			}

			for ($i = 1; $i <= $posts; $i++) {
				if ($project->iteration == $project->max_iterations + 1) {
					$project->iteration = $project->max_iterations;
					break;
				}

				if ($geo) {
					$geoIteration = ceil($project->iteration / $project->spintax_iterations);
					$geoData = improveseo_get_geodata($options['local_geo_country'], $options['local_geo_locations'][$geoIteration - 1]);
				}

				// Get current spintax iteration
				$spintaxIteration = improveseo_get_current_subiteration($project->iteration, $project->spintax_iterations);

				// Get current iteration for each field
				$titleIteration = improveseo_get_spintax_subiteration($titleMax, $project, $spintaxIteration);
				$contentIteration = improveseo_get_spintax_subiteration($contentMax, $project, $spintaxIteration);

				$titleText = Spintax::make($title, $titleIteration, $titleSpintax);
				if ($geo) $titleText = Spintax::geo($titleText, $geoData);

				$contentText = Spintax::make($content, $contentIteration, $contentSpintax);
				if ($geo) $contentText = Spintax::geo($contentText, $geoData);

				// Images EXIF
				if (isset($options['exif_locations'])) {
					$locationIteration = improveseo_get_current_subiteration($project->iteration, sizeof($exifLocations)) - 1;
					$address = $exifLocations[$locationIteration]->address;

					if (!isset($options['exif_cache'])) $options['exif_cache'] = [];
					if (!isset($options['exif_cache'][$address])) $options['exif_cache'][$address] = [];

					preg_match_all('/src=\\\"(.*)\\\" alt=\\\"(.*)\\\" width/ui', $contentText, $exif);

					foreach ($exif[1] as $idx => $image) {
						if (!isset($options['exif_cache'][$address][$image])) {
							$image = str_replace(':8000', '', $image); // Fix only for local dev
							$filename = sha1($address .'-'. $image .'-exif') .'.jpg';

							$exploded = explode('.', $image);
						    $ext = $exploded[count($exploded) - 1];

						    if (preg_match('/jpg|jpeg/i', $ext))
						        $imageSrc = $image;
						    else if (preg_match('/png/i', $ext))
						        $imageSrc = imagecreatefrompng($image);
						    else if (preg_match('/gif/i', $ext))
						        $imageSrc = imagecreatefromgif($image);
						    else if (preg_match('/bmp/i', $ext))
						        $imageSrc = imagecreatefrombmp($image);

						    $imagedir = 'uploads/'. date('Y') .'/'. date('m') .'/'. $filename;
							improveseo_check_dir($imagedir);

						    addGpsInfo(
						    	$imageSrc,
						    	WP_CONTENT_DIR .'/'. $imagedir,
						    	$exif[2][$idx],
						    	'Work Horse Comment',
						    	'Work Horse',
						    	$exifLocations[$locationIteration]->location->lng,
						    	$exifLocations[$locationIteration]->location->lat,
						    	0,
						    	date('Y-m-d H:i:s')
					    	);

					    	$savedir = "/wp-content/$imagedir";

						    // local dev fix
						    $image = str_replace('.app', '.app:8000', $image);

					    	$options['exif_cache'][$address][$image] = $savedir;
						} else {
							$savedir = $options['exif_cache'][$address][$image];
						}

				    	$contentText = str_replace($image, $savedir, $contentText);
					}
				}

				$post_id = wp_insert_post([
		            'post_title' => $titleText,
		            'post_name' => sanitize_title($titleText),
		            'post_author' => 1,
		            'post_content' => $contentText,
		            'post_status' => 'publish',
		            'post_type' => $data['post_type'],
		            'comment_status' => 'closed',
		            'ping_status' => 'closed'
				]);

				add_post_meta($post_id, 'improveseo_project_id', $project->id);

				// On-Page SEO Section
				if (isset($options['custom_title'])) {
					$customTitleText = improveseo_spintax_the_field($options['custom_title'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_custom_title', $customTitleText);
				}
				if (isset($options['custom_description'])) {
					$customDescriptionText = improveseo_spintax_the_field($options['custom_description'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_custom_description', $customDescriptionText);
				}
				if (isset($options['custom_keywords'])) {
					$customKeywordsText = improveseo_spintax_the_field($options['custom_keywords'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_custom_keywords', $customKeywordsText);
				}

				// Schema Section
				if (isset($options['schema_business'])) {
					$schemaBusinessText = improveseo_spintax_the_field($options['schema_business'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_schema_business', $schemaBusinessText);
				}
				if (isset($options['schema_description'])) {
					$schemaDescriptionText = improveseo_spintax_the_field($options['schema_description'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_schema_description', $schemaDescriptionText);
				}
				if (isset($options['schema_email'])) {
					$schemaEmailText = improveseo_spintax_the_field($options['schema_email'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_schema_email', $schemaEmailText);
				}
				if (isset($options['schema_telephone'])) {
					add_post_meta($post_id, 'improveseo_schema_telephone', $options['schema_telephone']);
				}
				if (isset($options['schema_social'])) {
					$schemaSocialText = improveseo_spintax_the_field($options['schema_social'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_schema_social', $schemaSocialText);
				}
				if (isset($options['schema_rating'])) {
					add_post_meta($post_id, 'improveseo_schema_rating', $options['schema_rating']);
				}
				if (isset($options['schema_address'])) {
					$schemaAddressText = improveseo_spintax_the_field($options['schema_address'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'improveseo_schema_address', $schemaAddressText);
				}

				$project->iteration++;
			}

			// Save project changes
			$update = array(
				'iteration' => $project->iteration
			);

			if ($project->iteration == $project->max_iterations) {
				$update['finished_at'] = date('Y-m-d H:i:s');
			}

			if (isset($options['exif_cache'])) {
				$update['options'] = base64_encode(json_encode($options));
			}

			$model->update($update, $project->id);
		}
	}
}
add_action('improveseo_parse_tasks_hook', 'improveseo_parse_tasks');
