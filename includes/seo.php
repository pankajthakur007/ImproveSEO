<?php

if (!is_admin()) {
	add_action('init', 'improveseo_seo_buffer_start');
	add_action('wp_head', 'improveseo_seo_buffer_end');

	add_filter('the_content', 'improveseo_seo_filter_content');

	function improveseo_seo_buffer_start() { 
		ob_start("improveseo_seo_callback");
	}

	function improveseo_seo_buffer_end() { 
		ob_end_flush();
	}

	function improveseo_seo_callback($buffer) {
		global $wp_query;

		if (!is_single() && !is_tag()) {
			return $buffer;
		}

		if (is_tag()) {
			$tag = $wp_query->queried_object;
			$meta = get_term_meta($tag->term_id);
			
			if (isset($meta['improveseo_noindex_tag'])) {
				$buffer = str_replace('</title>', "</title>\n<meta name=\"robots\" content=\"noindex, nofollow\">", $buffer);
			}

			return $buffer;
		}

		$title = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_custom_title', true));
		$description = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_custom_description', true));
		$keywords = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_custom_keywords', true));

		$meta_seo = array();

		// Meta description
		if (!empty($description)) {
			$meta_seo[] = '<meta name="description" content="'. $description .'">';
		} 
		// TODO: Add automatic generation
		else {
			$description = strlen($wp_query->post->post_content) > 156 ? substr($wp_query->post->post_content, 0, 152) .'...' : $wp_query->post->post_content;
			$meta_seo[] = '<meta name="description" content="'. esc_attr($description) .'">';
		}

		// Meta keywords
		if (!empty($keywords)) {
			$meta_seo[] = '<meta name="keywords" content="'. $keywords .'">';
		}

		$buffer = str_replace('</title>', "</title>\n". implode("\n", $meta_seo), $buffer);
		
		// Custom Title
		if (!empty($title)) {
			$buffer = preg_replace("/<title>[^<]*<\/title>/u", "<title>$title</title>", $buffer);
		}

		return $buffer;
	}

	function improveseo_seo_filter_content($content) {
		global $wp_query;

		if (is_single()) {
			$business = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_business', true));
			$description = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_description', true));
			$email = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_email', true));
			$telephone = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_telephone', true));
			$social = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_social', true));
			$rating_object = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_rating_object', true));
			$rating = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_rating', true));
			$rating_count = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_rating_count', true));
			$address = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_schema_address', true));
			$hidden = esc_attr(get_post_meta($wp_query->post->ID, 'improveseo_hide_schema', true));
			
			if ($business || $description || $email || $telephone || $social || $rating || $address) {
				$schema = '<div itemscope>';
				if ($business) $schema .= '<span itemprop="name">'. $business .'</span>';
				
				if ($email) $schema .= 'E-mail: <span itemprop="email">'. $email .'</span>';
				if ($telephone) $schema .= 'Tel:<span itemprop="telephone">'. $telephone .'</span>';
				if ($social) $schema .= 'Social:<span itemprop="social">'. $social .'</span>';
				if ($rating) {
					$schema .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
						<span itemprop="itemReviewed">'. $rating_object .'</span>
						<span itemprop="ratingValue">'. $rating .'</span>
						<span itemprop="ratingCount">'. $rating_count .'</span>
					</div>';
				}

				if ($address) {
					$schema .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
						Main address:
							<span itemprop="streetAddress">'. $address .'</span>
					</div>';
				}

				if ($description) $schema .= '<span itemprop="description">'. $description .'</span>';

				$schema .= '</div>';

				if ($hidden) {
					$schema = '<div id="maincontainer"><div id="linkcontainer" style="position:relative;z-index:-10000000000000000">'. $schema .'</div></div>';
				}

				// Rendering schema
				$content = $content . $schema;
			}
		}

		return $content;
	}
}