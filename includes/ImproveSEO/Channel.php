<?php

namespace ImproveSEO;

class Channel
{
	const STATE_CHANNEL = 'state';
	const CITY_CHANNEL = 'city';

	protected $id;

	protected $name;

	protected $project_id;

	protected $type;

	protected $typeName;

	protected $content;
	
	/**
	 * __construct
	 *
	 * @param  mixed $project_id
	 * @param  mixed $geoData
	 * @param  mixed $type
	 * @return void
	 */
	public function __construct($project_id, $geoData, $type) 
	{
		$this->project_id = $project_id;
		$this->name = sanitize_title("{$type}-{$geoData[$type]}-{$project_id}");
		$this->type = $type;
		$this->typeName = $geoData[$type];
	}
	
	/**
	 * getId
	 *
	 * @return void
	 */
	public function getId() 
	{
		return $this->id;
	}
	
	/**
	 * getName
	 *
	 * @return void
	 */
	public function getName() 
	{
		return $this->name;
	}
	
	/**
	 * getContent
	 *
	 * @return void
	 */
	public function getContent() 
	{
		return $this->content;
	}
	
	/**
	 * create
	 *
	 * @param  mixed $content
	 * @param  mixed $parent
	 * @return void
	 */
	public function create($content, $parent = null) 
	{
		global $wpdb;
		global $wp_rewrite;

		$content["{$this->type}_channel_title"] = str_replace("@{$this->type}", $this->typeName, $content["{$this->type}_channel_title"]);
		$content["{$this->type}_channel_page"] = str_replace("@{$this->type}", $this->typeName, $content["{$this->type}_channel_page"]);

		/*$channel_id = wp_insert_post([
            'post_title' => $content["{$this->type}_channel_title"],
            'post_name' => $this->name,
            'post_date' => date('Y-m-d H:i:s'),
            'post_author' => 1,
            'post_content' => $content["{$this->type}_channel_page"],
            'post_status' => 'publish',
            'post_type' => 'channel',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_parent' => $parent ? $parent->getId() : 0
		]);*/

		$wpdb->query($wpdb->prepare("INSERT IGNORE INTO `".$wpdb->prefix."posts` 
			(post_author, post_content, post_title, comment_status, ping_status, post_name, post_type,
			 post_date, post_date_gmt, post_status, post_parent) 
			VALUES (%s, %s, %s, 'closed', 'closed', %s, %s, %s, %s, %s, %s)", 1, $content["{$this->type}_channel_page"], 
			$content["{$this->type}_channel_title"], $this->name, 'channel', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), 'publish', $parent ? $parent->getId() : 0));
		
		$channel_id = $wpdb->insert_id;

		add_post_meta($channel_id, 'improveseo_project_id', $this->project_id);
		add_post_meta($channel_id, 'improveseo_channel', 1);

		// Rebuild rewrite rules
		$wp_rewrite->flush_rules();

		$wpdb->query("COMMIT;");
		
		$this->id = $channel_id;
		$this->content = $content["{$this->type}_channel_page"];

		return $channel_id;
	}
	
	/**
	 * find
	 *
	 * @return void
	 */
	public function find() 
	{
		global $wpdb;

		$channel_check = $wpdb->get_row("
				SELECT ID, post_content FROM {$wpdb->prefix}posts AS p 
					INNER JOIN {$wpdb->prefix}postmeta AS pm ON pm.post_id = p.ID 
				WHERE pm.meta_key = 'improveseo_project_id' AND pm.meta_value = '{$this->project_id}' AND p.post_type = 'channel' AND p.post_name = '{$this->getName()}' AND p.post_status = 'publish'");

		if (!$channel_check || ($channel_check && !$channel_check->ID)) {
			return false;
		}

		$this->id = $channel_check->ID;
		$this->content = $channel_check->post_content;

		return true;
	}
	
	/**
	 * getLink
	 *
	 * @param  mixed $post_id
	 * @param  mixed $geoData
	 * @return void
	 */
	public function getLink($post_id, $geoData) 
	{
		$name = $this->type == 'state' ? "{$geoData['city']}, {$geoData['state']}" : "{$geoData['zip']}, {$geoData['city']}";

		return '<a href="'. get_permalink($post_id) .'">'. $name .'</a>';
	}
	
	/**
	 * notExists
	 *
	 * @param  mixed $link
	 * @return void
	 */
	public function notExists($link) 
	{
		return strstr($this->content, $link) === false;
	}
	
	/**
	 * save
	 *
	 * @param  mixed $links
	 * @return void
	 */
	public function save($links) 
	{
		$tag = $this->type == 'state' ? '@citylist' : '@ziplist';

		$this->content = str_replace($tag, implode("\r\n", $links) ."\r\n$tag", $this->content);
		
		wp_update_post(array(
			'ID' => $this->id,
			'post_content' => $this->content
		));
	}
}