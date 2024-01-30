<?php
/*
Plugin Name: Improve SEO
Plugin URI: 
Description: Creates a large number of pages/posts and customize them to rank in Google.
Author: Improve SEO Team
Version: 2.0.11
*/
define("IMPROVESEO_VERSION", "2.0.11");
define('IMPROVESEO_ROOT', dirname(__FILE__));
define('IMPROVESEO_DIR', untrailingslashit(plugin_dir_url( __FILE__ )));


define( 'WT_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );
define( 'WT_URL' ,  untrailingslashit(plugin_dir_url( __FILE__ )) );

/* 
**========== Files Load =========== 
*/
if( file_exists( dirname(__FILE__).'/includes/helpers.php' )) include_once dirname(__FILE__).'/includes/helpers.php';
// if( file_exists( dirname(__FILE__).'/includes/admin.php' )) include_once dirname(__FILE__).'/includes/admin.php';

include_once 'bootstrap.php';

register_activation_hook(__FILE__, 'improveseo_install');
register_activation_hook(__FILE__, 'improveseo_install_data');

// Features

register_deactivation_hook(__FILE__, 'improveseo_uninstall');

function improveseo_load_media_files() {
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'improveseo_load_media_files' );

//add_action( 'init', "workdex_init" );

add_filter('jpeg_quality', function($arg){return 75;});

//adding buttons to content editor
add_action('media_buttons', 'add_my_media_button');
function add_my_media_button() {

	if(function_exists('get_current_screen')){ 

	$my_current_screen = get_current_screen();
	$allowed_bases = array('improve-seo_page_improveseo_posting');
	if(!in_array($my_current_screen->base, $allowed_bases)){
		return;
	}
	
	}


    $html = '';
    $html .= '<select class="sw-editor-selector" style="text-align:left !important;">
                    <option value="addshortcode">Add Shortcode</option>
                    <option value="testimonial">Testimonials</option>
                    <option value="googlemap">Google Maps</option>
                    <option value="button">Buttons</option>
                    <option value="video">Videos</option>
                    <option value="list">Lists</option>
             </select> &nbsp;';
    $html .= '<a type="button" class="btn btn-primary btn-outline-primary" data-toggle="modal" data-target="#exampleModal" >Generate AI Content</a>';

  	$seo_list = improve_seo_lits();

    if(!empty($seo_list)){
        foreach($seo_list as $li){
            $list.= '<button data-action="list" class="add-seolistshortcode button" id='.$li.'>@list:'.$li.'</button>';
        }   
    }

$html.= '
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/jquery.smartWizard.min.js"></script>';
 
$html.= '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">';

$html.='<style>	.modal { max-width: unset; } #exampleModal { z-index: 9999; } .modal-backdrop { height:unset; } .input-group > .form-control { width: 100%; } </style>';

$html.= '<link href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/smart_wizard.min.css" rel="stylesheet" type="text/css" /> 
<link href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/smart_wizard_theme_dots.min.css" rel="stylesheet" type="text/css" />';

$html.= '<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Generate AI Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="smartwizard">
                        <ul style="margin: 0px 30px 5px 30px;">
                            <li style="width: 25%;">
                                <a href="#step-1">
                                    Step 1<br />
                                    <small>Keyword Input</small>
                                </a>
                            </li>
                            <li style="width: 25%;">
                                <a href="#step-2">
                                    Step 2<br />
                                    <small>Content Setting</small>
                                </a>
                            </li>
                            <li style="width: 25%;">
                                <a href="#step-3">
                                    Step 3<br />
                                    <small>Add Media</small>
                                </a>
                            </li>
                            <li style="width: 25%;">
                                <a href="#step-4">
                                    Step 4<br />
                                    <small>Send Request To ChatGPT</small>
                                </a>
                            </li>
                        </ul>
                        <div>
                            <div id="step-1">
                                
                               
                                <div class="row">
                                <div class="form-group col-md-1"></div>
                                    <div class="form-group col-md-5">
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="keyword_selection" value="seed" checked>Seed Keyword
                                            </label>
                                        </div>
                                    </div>
                                   <div class="form-group col-md-1"></div>
                                    <div class="form-group col-md-5">
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="keyword_selection" value="select_exisiting">Select from keyword list
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                <div class="form-group col-md-1"></div>
                                    <div class="form-group col-md-11 desc" id="seed">
                                    
                                        
                                            <select id="seed_select" name="seed_options" class="form-control" style="max-width: 84% !important;">
                                                    <option value="">Select</option>

                                                    



                                                    <option value="seed_option1">USE KEYWORD AS IS IN TITLE [A.I. will build content]</option>
                                                    <option value="seed_option2">CREATE BEST TITLE FROM KEYWORD [A.I. will choose/build content]</option>
                                                    <option value="seed_option3">CREATE BEST QUESTION FROM KEYWORD [A.I. will choose/build content]</option>
                                                    
                                            </select>
                                            <div style="clear: both"> </div>
                                            <textarea class="form-control" style="margin-top: 20px; width: 84%; display: none; resize:none;" placeholder="Enter Seed Keyword" name="seed_keyword"></textarea>
                                    </div>
                                    <div class="form-group col-md-11 desc" id="select_exisiting" style="display: none;">
                                            <select id="existing_select" name="select_exisiting_options" class="form-control" style="max-width: 84% !important;">
                                                    <option value="">Select</option>
                                                    <option value="seed_option1">USE KEYWORD AS IS IN TITLE [A.I. will build content]</option>
                                                    <option value="seed_option2">CREATE BEST TITLE FROM KEYWORD [A.I. will choose/build content]</option>
                                                    <option value="seed_option3">CREATE BEST QUESTION FROM KEYWORD [A.I. will choose/build content]</option>
                                            </select>
                                            <div style="margin-top: 20px; display: none;" class="show_lists">'.$list.'</div>
                                            <textarea placeholder="Context Prompt" style="margin-top: 20px; width: 84%; display: none; resize:none;" class="form-control" name="existing_keyword" /></textarea>
                                        </div>
                                    </div>
                                




                            </div>
                            <div id="step-2">
                                
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="sel1">Number of words for Content</label>
                                        <input type="text" class="form-control" placeholder="Number of words for content" required />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="language">Select Language</label>
                                        <select class="form-control" id="language">
                                            <option value="">-Select Language-</option>
                                            <option value="english_us">English (US)</option>
                                            <option value="english_uk">English (Uk)</option>
                                            <option value="German">German</option>
                                            <option value="French">French</option>
                                            <option value="Italian">Italian</option>
                                            <option value="Spanish">Spanish</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                            <div id="step-3" class="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <select class="sw-editor-selector" style="text-align:left !important;">
                                            <option value="addshortcode">Add Shortcode</option>
                                            <option value="testimonial">Testimonials</option>
                                            <option value="googlemap">Google Maps</option>
                                            <option value="button">Buttons</option>
                                            <option value="video">Videos</option>
                                            <option value="list">Lists</option>
                                         </select>

                                    </div>
                                </div>
                            </div>
                            <div id="step-4" class="">
                                <div class="row">
                                    <div class="col-md-12">
                                        All Content will show here
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><style>.sw-theme-dots>ul.step-anchor:before { top:58px !important;}</style>';

	?>

	 
	<script>
    jQuery(document).ready(function(){

		jQuery('#smartwizard').smartWizard({
		        selected: 0,
		        theme: 'dots',
		        autoAdjustHeight:true,
		        transitionEffect:'fade',
		        showStepURLhash: false,
		     
		});

	});



	jQuery(document).ready(function(){
		  jQuery("input[name$='keyword_selection']").click(function() {
		  	var test = jQuery(this).val();
		  	console.log(test);

		  	jQuery("div.desc").hide();
	        jQuery("#" + test).show();

	        jQuery("#seed_select option[value='']").attr('selected', true);
	        jQuery('textarea[name="seed_keyword"]').hide();

	        jQuery("#existing_select option[value='']").attr('selected', true)
	        jQuery('.show_lists').hide();
	  		jQuery('textarea[name="existing_keyword"]').hide();
		  });

		  jQuery('select[name="seed_options"]').change(function(){
		  	var selvalue = jQuery(this).val();

		  	if (selvalue=='')
		  	{
		  		jQuery('textarea[name="seed_keyword"]').hide();
		  	}
		  	else
		  	{
		  		jQuery('textarea[name="seed_keyword"]').show();
		  	}
		  });

		  jQuery('select[name="select_exisiting_options"]').change(function(){
		  	var selvalue = jQuery(this).val();

		  	if (selvalue=='')
		  	{
		  		jQuery('.show_lists').hide();
		  		jQuery('textarea[name="existing_keyword"]').hide();
		  	}
		  	else
		  	{
		  		jQuery('.show_lists').show();
		  		jQuery('textarea[name="existing_keyword"]').show();
		  	}
		  });
	  });
	</script>

	<?php

    $saved_rnos =  get_option('get_saved_random_numbers');
    
	if(!empty($saved_rnos)){
		foreach($saved_rnos as $id){
			
			//testimonials        
			$testimonial = get_option('get_testimonials_'.$id);
			if(!empty($testimonial)){
				$display_name = $id;
				$data_name = '';
				if(isset($testimonial['tw_testi_shortcode_name'])){
					if($testimonial['tw_testi_shortcode_name']!=""){
						$data_name = $display_name = $testimonial['tw_testi_shortcode_name'];
					}
				}
				$html .= '<button data-action="testimonial" data-name="'.$data_name.'" id="'.$id.'" class="sw-hide-btn button">Add Testimonial - '.$display_name.'</button>';   
			}
			
			//buttons        
			$buttons = get_option('get_buttons_'.$id);
			if(!empty($buttons)){
				$display_name = $id;
				$data_name = '';
				if(isset($buttons['tw_button_shortcode_name'])){
					if($buttons['tw_button_shortcode_name']!=""){
						$data_name = $display_name = $buttons['tw_button_shortcode_name'];
					}
				}
				$html .= '<button data-action="button" data-name="'.$data_name.'" id="'.$id.'" class="sw-hide-btn button">Add Button - '.$display_name.'</button>';   
			}
			
			//googlemaps        
			$google_map = get_option('get_googlemaps_'.$id);
			if(!empty($google_map)){
				$display_name = $id;
				$data_name = '';
				if(isset($google_map['tw_maps_shortcode_name'])){
					if($google_map['tw_maps_shortcode_name']!=""){
						$data_name = $display_name = $google_map['tw_maps_shortcode_name'];
					}
				}
				$html .= '<button data-action="googlemap" data-name="'.$data_name.'" id="'.$id.'" class="sw-hide-btn button">Add GoogleMap - '.$display_name.'</button>';   
			}

			//videos
			$videos = get_option('get_videos_'.$id);
			if(!empty($videos)){
				$display_name = $id;
				$data_name = '';
				if(isset($videos['video_shortcode_name'])){
					if($videos['video_shortcode_name']!=""){
						$data_name = $display_name = $videos['video_shortcode_name'];
					}
				}
				$html .= '<button data-action="video" data-name="'.$data_name.'" id="'.$id.'" class="sw-hide-btn button">Add Video - '.$display_name.'</button>';   
			}
		}
	}
	

    $seo_list = improve_seo_lits();
    if(!empty($seo_list)){
        foreach($seo_list as $li){
            $html .= '<button data-action="list" class="sw-hide-btn add-seolistshortcode button" id='.$li.'>@list:'.$li.'</button>';
        }   
        
    }
    echo $html;
}

function improve_seo_lits(){

    global $wpdb;
    $list_names = array();
    $sql = "SELECT * FROM " . $wpdb->prefix . "improveseo_lists ORDER BY name ASC";
    $lists = $wpdb->get_results($sql);
    foreach($lists as $li){
        $list_names[] = $li->name;;    
    }
    return $list_names;
    
}

add_action('init' , 'updating_post_status_to_publish');
function updating_post_status_to_publish(){
    
    // improveseo_project_id
    wp_enqueue_style('tmm_stlye_css', WT_URL."/assets/css/wt-style.css",  true);
    $args = array(
        'post_status' => array('future')    
    );
    $query = new WP_Query($args);
    $all_posts = $query->posts;
    
    $post_data = array();
    foreach($all_posts as $key => $value){
        
        $post_data[] = array(
                'post_id'   => $value->ID,
                'post_date' => $value->post_date,
            );
    }
    
    foreach($post_data as $i => $v){
        
        $post_id = $v['post_id'];
        $post_date = $v['post_date'];
        $post_status = get_post_status($post_id);
        if($post_status !='future'){
            continue;
        }
        $date_now = new DateTime();
        $date_op    = new DateTime($post_date);
        
        if ($date_now > $date_op) {
            change_post_status($post_id,$status='publish');
        }
    }
     
}

//change the post status
function change_post_status($post_id,$status){
    $current_post = get_post( $post_id, 'ARRAY_A' );
    $current_post['post_status'] = $status;
    wp_update_post($current_post);
}


function workdex_init(){
	
	global $wpdb;
	$time = get_option("work_dex_schedule");
	if($time<(time()-3600*12)){
		$wpdb->query ( "UPDATE ".$wpdb->posts." SET post_status='publish' WHERE post_date<=now() and post_date_gmt<=now()" );
		update_option("work_dex_schedule",time());
	}
}

add_action('wp_ajax_workdex_builder_ajax', 'improveseo_builder');
add_action('wp_ajax_workdex_builder_update_ajax', 'improveseo_builder_update');

//AJAX call to check if preview window is open 
add_action('wp_ajax_preview_delete_ajax', 'preview_delete_ajax');


$debug = 0;

//add_filter('pre_set_site_transient_update_plugins', 'improveseo_check_for_update');

function improveseo_check_for_update($transient)
{
	if (empty($transient->checked)) {
		return $transient;
	}
	if (improveseo_check_version()) {
		if (improveseo_check_version() != IMPROVESEO_VERSION) {
			$plugin_slug = plugin_basename("ImproveSEO/improveseo.php");
			$transient->response[$plugin_slug] = (object) array(
					'new_version' => workhorse_check_version(),
					'package' => "http://www.dexblog.net/workhorse/workhorse-by-dexblog-" . workhorse_check_version() . ".zip",
					'slug' => $plugin_slug
			);
		}
	}
	return $transient;
} 

/**
 * Api handler
 */
function improveseo_api($action, $arg) {
	$id_last = get_option ( "dexscan_last_id" );
	$url = 'http://api-dexsecurity.dexblog.net/api.php?action=' . $action . '&host=' . $_SERVER ["HTTP_HOST"] . "&id_scan=" . $id_last;
	if ($action == "getdata") {
		$ids = dexscan_save_file_backup ( $arg );
		$arg ['id_save'] = $ids;
	}
	if (ini_get ( 'allow_url_fopen' )) {
		$options = array (
				'http' => array (
						'header' => "Content-type: application/x-www-form-urlencoded\r\n",
						'method' => 'POST',
						'content' => http_build_query ( $arg )
				)
		);
		$context = stream_context_create ( $options );
		$result = @file_get_contents ( $url, false, $context );
	} else {
		if (_is_curl_installed ()) {
			foreach ( $arg as $key => $value ) {
				$fields_string .= $key . '=' . $value . '&';
			}
			rtrim ( $fields_string, '&' );
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_POST, count ( $fields ) );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields_string );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			$result = curl_exec ( $ch );
			curl_close ( $ch );
		}
	}
	$da = json_decode ( $result );
	return $da;
}
function improveseo_check_version() {
	$lastupdate = get_option ( "improveseo_lastcheck" );
	if ($lastupdate < (time () - 600)) {
		$data2 = array (
				'version' => IMPROVESEO_VERSION
		);
		$data = improveseo_api ( "versionimproveseo", $data2 );
		if ($data->status == 1) {
			update_option ( "improveseo_new_version", $data->version );
		} else {
			update_option ( "improveseo_new_version", $data->version );
		}
	}
	update_option ( "improveseo_lastcheck", time () );
	return get_option ( "improveseo_new_version" );
}

/*check curl install or not*/
function _is_curl_installed() {
    if  (in_array  ('curl', get_loaded_extensions())) {
        return true;
    }
    else {
        return false;
    }
}
/*end*/

add_action('admin_enqueue_scripts', 'improveseo_hide_other_notices');
function improveseo_hide_other_notices() {
    if ( is_admin() ) {
		
        $my_current_screen = get_current_screen();
		$improve_seo_pages = array(
			'toplevel_page_improveseo_dashboard',
			'improve-seo_page_improveseo_posting',
			'improve-seo_page_improveseo_projects',
			'improve-seo_page_improveseo_shortcodes',
			'improve-seo_page_improveseo_lists',
			'improve-seo_page_improveseo_settings',
			'improve-seo_page_improveseo_authors',
			'improve-seo_page_improveseo_keyword_generator',
			'improve-seo_page_improveseo_shortcode'
		);
		
        if ( isset( $my_current_screen->base )  ) {
			if(in_array($my_current_screen->base, $improve_seo_pages)){
				echo '<style>.notice{ display:none !important;}</style>';
			}
			
        }
    }
}

class WC_Testimonial {
	
	function __construct() {
		
		//add_action( 'admin_menu', 'custom_address_option_on_settings_tab' );

        add_action('admin_bar_menu', [ $this, 'improve_seo_admin_top_bar_option' ], 2000);
		
		add_action( 'admin_enqueue_scripts', array($this , 'load_admin_files'), 30 );

		add_action('wp_ajax_wt_save_form_fields_for_testimonials' , array($this , 'wt_save_form_fields_for_testimonials'));
		add_action('wp_ajax_wt_save_form_fields_for_googlemaps' , array($this , 'wt_save_form_fields_for_googlemaps'));
		add_action('wp_ajax_wt_save_form_fields_for_buttons' , array($this , 'wt_save_form_fields_for_buttons'));
		add_action('wp_ajax_wt_save_form_fields_for_videos' , array($this , 'wt_save_form_fields_for_videos'));
		
		add_action('wp_ajax_delete_selected_data' , array($this , 'delete_selected_data'));
		add_action('wp_ajax_kwdelete_selected_data_for_keyword' , array($this , 'kwdelete_selected_data_for_keyword'));
		add_action('wp_ajax_kwdownload_selected_data_for_keyword' , array($this , 'kwdownload_selected_data_for_keyword'));
		
		
		add_action('wp_ajax_edit_selected_data' , array($this , 'edit_selected_data'));
		add_action('wp_ajax_sw_saved_search_results_keyword' , array($this , 'sw_saved_search_results_keyword'));

		//shortcode for things testimonials / MAPS / Buttons
		add_shortcode('improveseo_testimonial' , array($this , 'testimonial_callback'));
		add_shortcode('improveseo_googlemaps' , array($this , 'maps_callback'));
		add_shortcode('improveseo_buttons' , array($this , 'button_callback'));
		add_shortcode('improveseo_video' , array($this , 'video_callback'));

	}



    /**
     * admin bar.
     * @return void.
     */
    public function improve_seo_admin_top_bar_option() {
        global $wp_admin_bar;
        $menu_id = 'improveseo_dashboard';
        $wp_admin_bar->add_menu(array(
            'id' => $menu_id,
            'title' => __('Improve SEO', 'improve-seo'),
            'href' => admin_url() .'/admin.php?page=improveseo_dashboard',
        ));
    }
	
	/****=====Download the data against ID of keyword from admin side====***/	
    function kwdownload_selected_data_for_keyword(){
		
		$keywordproj_id = isset($_REQUEST['kw_rand_id']) ? $_REQUEST['kw_rand_id'] : '';
		if(empty($keywordproj_id)){
		    return;
		}
        $keyword_data = get_option('swsaved_keywords_with_results_'.$keywordproj_id);
        $proj_name      = $keyword_data['proj_name'];
        $proj_content   = $keyword_data['search_results'];
        
        $proj_name =  str_replace(' ', '-', $proj_name);
        $args = array(
                'status'    => 'success',
                'proj_name' => $proj_name,
                'proj_content' => $proj_content,
            );
        wp_send_json($args);
        die(0);
    }



	/****=====SAving the data found with keywords====***/	
	function sw_saved_search_results_keyword(){
	    
	    $proj_name = isset($_REQUEST['proj_name']) ? $_REQUEST['proj_name'] : '';
	    $search_results = isset($_REQUEST['search_results']) ? $_REQUEST['search_results'] : '';
	    
	    $rand_no = $this->create_random_number();
	    $save_keyword_data = array(
	            'proj_name' => $proj_name,
	            'search_results' => $search_results,
	       );
	    update_option('swsaved_keywords_with_results_'.$rand_no , $save_keyword_data);
	    
	    		//saving random numbers too
		$random_no_arr = get_option('swsaved_random_nosofkeywords');

		$random_no_arr[] = $rand_no;
		$result = array_unique($random_no_arr);
		
		update_option('swsaved_random_nosofkeywords' , $result );
	    
	    $args = array(
	            'status' => 'success',
	        );
	    wp_send_json($args);
	    die(0);
	    
	}
	
	
	/****=====Notice on how to use the shortcodes====***/
	function general_admin_notice(){

        echo '<div class="notice notice-warning is-dismissible notice-improveseo">
            <p><b>1)</b>For Testimonials You can use the shortcode as <b>[improveseo_testimonial id="YOURID"]</b> 
            	You can Add multiple Ids for testimonials just like <b>[improveseo_testimonial id="YOURID_1 , YOURID_2 , YOURID_3"]</b><br><br>
            	<b>2)</b>For Buttons You can use the shortcode as <b>[improveseo_buttons id="YOURID"]</b> 
            	You can Add multiple Ids for buttons just like <b>[improveseo_buttons id="YOURID_1 , YOURID_2 , YOURID_3"]</b><br><br>
            	<b>3)</b>For Google Maps You can use the shortcode as <b>[improveseo_googlemaps id="YOURID" address="YOURADDRESS" title="YOURTITLE" ]</b>
            </p>
        </div>';
	}
	
	/****=====REndereing the Google Maps on front end against Shortcode====***/
	function maps_callback( $atts ){


        $sc_att = shortcode_atts(
            array(
              'id' => null, 
              'address' => null, 
              'title' => null, 
            ), $atts
        );

        $id = $sc_att['id'];
        $id = $new_str = str_replace(' ', '', $id);
        
        $address = $sc_att['address'];
        $title = isset($sc_att['title']) ? $sc_att['title'] : '';
        
        if (empty($id || $address)) {
        	return;
        }

        $id = explode(',', $id);
        $args = array(
        	'id' => $id,
        	'title' => $title,
        	'address' => $address,
        );


        ob_start();
			wt_load_templates('googlemaps.php' , $args);
            $html = ob_get_contents(); 
            ob_end_clean();
        return $html;
	}
	
		/****=====getting the longitude and latitudepoints====***/
	function getDistance( $addressFrom , $apiKey ){
    
    
        // Change address format
        $formattedAddrFrom    = str_replace(' ', '+', $addressFrom);
        
        // Geocoding API request with start address
        $geocodeFrom = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&sensor=false&key='.$apiKey);
        $outputFrom = json_decode($geocodeFrom);
        if(!empty($outputFrom->error_message)){
            return $outputFrom->error_message;
        }
        
        
        // Get latitude and longitude from the geodata
        $latitudeFrom    = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom    = $outputFrom->results[0]->geometry->location->lng;
        $resp = array(
        	'latitude' => $latitudeFrom,
        	'longitude' => $longitudeFrom,
        );
        return $resp;
    }

	/****=====REndereing the Buttons on front end against Shortcode====***/
	function button_callback( $atts ){
  
        $sc_att = shortcode_atts(
            array(
              'id' => null, 
            ), $atts
        );

        $id = $sc_att['id'];
        $id = $new_str = str_replace(' ', '', $id);
        if (empty($id)) {
        	return;
        }

        $id = explode(',', $id);
        $args = array(
        	'id' => $id
        );


        ob_start();
			wt_load_templates('buttons.php' , $args);
            $html = ob_get_contents(); 
            ob_end_clean();
        return $html;
	}


	/****=====REndereing the Testimonial on front end against Shortcode====***/
	function testimonial_callback( $atts ){

        $sc_att = shortcode_atts(
            array(
              'id' => null, 
            ), $atts
        );

        $id = $sc_att['id'];
        $id = $new_str = str_replace(' ', '', $id);
        if (empty($id)) {
        	return;
        }

        $id = explode(',', $id);
        $args = array(
        	'id' => $id
        );


        ob_start();
			wt_load_templates('testimonials.php' , $args);
            $html = ob_get_contents(); 
            ob_end_clean();
        return $html;
	}


	function video_callback( $atts ){
		$sc_att = shortcode_atts(
            array(
              'id' => null, 
            ), $atts
        );

        $id = $sc_att['id'];
        $id = $new_str = str_replace(' ', '', $id);
        if (empty($id)) {
        	return;
        }

        $id = explode(',', $id);
        $args = array(
        	'id' => $id
        );


        ob_start();
			wt_load_templates('videos.php' , $args);
            $html = ob_get_contents(); 
            ob_end_clean();
        return $html;
	}


	/****=====Edit/Updating the selected data====***/
	function edit_selected_data(){
		
		$rand_id = isset($_REQUEST['rand_id']) ? $_REQUEST['rand_id'] : '';
		$page_url = isset($_REQUEST['page_url']) ? $_REQUEST['page_url'] : '';
		$btn_action = isset($_REQUEST['btn_action']) ? $_REQUEST['btn_action'] : '';
		if (empty($rand_id)) {
			return;
		}	

		$url_param = array(
			'rand_id' => $rand_id,
			'action' => $btn_action
		);
		$page_url = add_query_arg($url_param, $page_url);
		wp_send_json(array('status' => 'success' , 'page_url' => $page_url));
	}

	/****=====Deleting the selected data====***/
	function delete_selected_data(){
		
		$no_tobe_dlt = isset($_REQUEST['rand_id']) ? $_REQUEST['rand_id'] : '';
		if (empty($no_tobe_dlt)) {
			return;
		}

		$saved_random_nos = get_option('get_saved_random_numbers');
		if (in_array($no_tobe_dlt, $saved_random_nos)) {

			delete_option('get_testimonials_'.$no_tobe_dlt);
			$result = $this->delete_el_from_array($saved_random_nos , $no_tobe_dlt);
			update_option('get_saved_random_numbers' , $result);
			
			$url_param = array(
			    'action' => 'deleted'
    		);
    		$url = admin_url('admin.php?page=improveseo_shortcodes');
		    $page_url = add_query_arg($url_param, $url);
			
			
			wp_send_json(array('status'=>'success' , 'url'=>$page_url));
		}

	}
	
	/****=====Deleting the selected data for keyword on admin side====***/
	function kwdelete_selected_data_for_keyword(){
		
		$no_tobe_dlt = isset($_REQUEST['kw_rand_id']) ? $_REQUEST['kw_rand_id'] : '';
		if (empty($no_tobe_dlt)) {
			return;
		}

		$saved_random_nos = get_option('swsaved_random_nosofkeywords');
		if (in_array($no_tobe_dlt, $saved_random_nos)) {

			delete_option('swsaved_keywords_with_results_'.$no_tobe_dlt);
			$result = $this->delete_el_from_array($saved_random_nos , $no_tobe_dlt);
			
			update_option('swsaved_random_nosofkeywords' , $result);
			wp_send_json(array('status'=>'success'));
		}

	}

	/****=====Deleting Some element from Array====***/
	function delete_el_from_array($my_array , $remove_el){

		$pos = array_search($remove_el, $my_array);
		
		unset($my_array[$pos]);
		return $my_array;
	}

	/****=====Load Admin JS And CSS files====***/
	function load_admin_files(){
		wp_enqueue_style('improveseo_style', WT_URL."/assets/css/improveseo_style.css",  array(), '1.1');
		wp_enqueue_style("poppins_fonts", "https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");
		wp_enqueue_script('tmm_script_js', WT_URL."/assets/js/wt-script.js",  array('jquery'), IMPROVESEO_VERSION, true);
		wp_enqueue_script('tmm_sweeetalertscript_js', WT_URL."/assets/js/wt-sweetalert.js",  array('jquery'));

	    wp_localize_script('tmm_script_js', 'ajax_vars', array(
	    	'ajax_url'      		=> 	admin_url( 'admin-ajax.php' ),
	    	)
		);		

	}
	
		/****=====Saving Form Fields From Admin Side For Button====***/
	function wt_save_form_fields_for_buttons(){

		$rand_no = isset($_REQUEST['updateandedit_data']) ? $_REQUEST['updateandedit_data'] : '';

		$tw_button_shortcode_name = isset($_REQUEST['tw_button_shortcode_name']) ? $_REQUEST['tw_button_shortcode_name'] : '';
		$tw_btn_text = isset($_REQUEST['tw_btn_text']) ? $_REQUEST['tw_btn_text'] : '';
		$tw_btn_link = isset($_REQUEST['tw_btn_link']) ? $_REQUEST['tw_btn_link'] : '';
		$tw_buttontxt_color = isset($_REQUEST['tw_buttontxt_color']) ? $_REQUEST['tw_buttontxt_color'] : '';
		$tw_buttonbg_color = isset($_REQUEST['tw_buttonbg_color']) ? $_REQUEST['tw_buttonbg_color'] : '';
		$tw_button_outline_color = isset($_REQUEST['tw_button_outline_color']) ? $_REQUEST['tw_button_outline_color'] : '#ffffff';
		$tw_button_size = isset($_REQUEST['tw_button_size']) ? $_REQUEST['tw_button_size'] : 'sm';
		$tw_button_border_type = isset($_REQUEST['tw_button_border_type']) ? $_REQUEST['tw_button_border_type'] : 'square';
		
		$tw_button_type = isset($_REQUEST['tw_button_type']) ? $_REQUEST['tw_button_type'] : 'normal_btn';
		$tw_tap_to_call_img_source = isset($_REQUEST['tw_tap_to_call_img_source']) ? $_REQUEST['tw_tap_to_call_img_source'] : '';
		$tw_tap_btn_text = isset($_REQUEST['tw_tap_btn_text']) ? $_REQUEST['tw_tap_btn_text'] : '';
		$tw_tap_btn_number = isset($_REQUEST['tw_tap_btn_number']) ? $_REQUEST['tw_tap_btn_number'] : '';

		$arr = array(
			'tw_button_shortcode_name'  => $tw_button_shortcode_name,
			'tw_maps_apikey' 			=> $tw_maps_apikey,
			'tw_btn_text' 				=> $tw_btn_text,
			'tw_btn_link' 				=> $tw_btn_link,
			'tw_buttontxt_color'		=> $tw_buttontxt_color,
			'tw_buttonbg_color' 		=> $tw_buttonbg_color,
			'tw_button_outline_color'	=> $tw_button_outline_color,
			'tw_button_size' 			=> $tw_button_size,
			'tw_button_border_type' 	=> $tw_button_border_type,
			'tw_button_type' 			=> $tw_button_type,
			'tw_tap_to_call_img_source' => $tw_tap_to_call_img_source,
			'tw_tap_btn_text' 			=> $tw_tap_btn_text,
			'tw_tap_btn_number' 		=> $tw_tap_btn_number,

		);

		if (empty($rand_no)) {
			$rand_no = $this->create_random_number();
		}
		update_option('get_buttons_'.$rand_no , $arr);

		//saving random numbers too
		$random_no_arr = get_option('get_saved_random_numbers');

		$random_no_arr[] = $rand_no;
		$result = array_unique($random_no_arr);
		update_option('get_saved_random_numbers' , $result );
		$url = admin_url('admin.php?page=improveseo_shortcodes');
		wp_send_json(array('status' => 'success' , 'url' => $url));
		die;
	}

	/****=====Saving Form Fields From Admin Side For Googlemaps====***/
	function wt_save_form_fields_for_googlemaps(){
		
		$rand_no = isset($_REQUEST['updateandedit_data']) ? $_REQUEST['updateandedit_data'] : '';
		$tw_maps_apikey = isset($_REQUEST['tw_maps_apikey']) ? $_REQUEST['tw_maps_apikey'] : '';
		$tw_maps_shortcode_name = isset($_REQUEST['tw_maps_shortcode_name']) ? $_REQUEST['tw_maps_shortcode_name'] : '';

		$arr = array(
			'tw_maps_shortcode_name' => $tw_maps_shortcode_name,
			'tw_maps_apikey' 	=> $tw_maps_apikey,
		);

		if (empty($rand_no)) {
			$rand_no = $this->create_random_number();
		}
		update_option('get_googlemaps_'.$rand_no , $arr);

		//saving random numbers too
		$random_no_arr = get_option('get_saved_random_numbers');

		$random_no_arr[] = $rand_no;
		$result = array_unique($random_no_arr);
		update_option('get_saved_random_numbers' , $result );
		$url = admin_url('admin.php?page=improveseo_shortcodes');
		wp_send_json(array('status' => 'success' , 'url' => $url));
		die;
	}

	/****=====Saving Form Fields From Admin Side For testimonials====***/
	function wt_save_form_fields_for_testimonials(){

		$rand_no = isset($_REQUEST['updateandedit_data']) ? $_REQUEST['updateandedit_data'] : '';
		
		$tw_testi_shortcode_name = isset($_REQUEST['tw_testi_shortcode_name']) ? $_REQUEST['tw_testi_shortcode_name'] : '';
		$testi_img_src = isset($_REQUEST['img_source']) ? $_REQUEST['img_source'] : '';
		$tw_testi_content = isset($_REQUEST['tw_testi_content']) ? $_REQUEST['tw_testi_content'] : '';
		$tw_testi_name = isset($_REQUEST['tw_testi_name']) ? $_REQUEST['tw_testi_name'] : '';
		$tw_testi_position = isset($_REQUEST['tw_testi_position']) ? $_REQUEST['tw_testi_position'] : '';
		$tw_box_color = isset($_REQUEST['tw_box_color']) ? $_REQUEST['tw_box_color'] : '';
		$tw_font_color = isset($_REQUEST['tw_font_color']) ? $_REQUEST['tw_font_color'] : '';
		$tw_testi_outline_color = isset($_REQUEST['tw_testi_outline_color']) ? $_REQUEST['tw_testi_outline_color'] : '#000000';


		$arr = array(
			'tw_testi_shortcode_name' => $tw_testi_shortcode_name,
			'testi_img_src' 	=> $testi_img_src,
			'tw_testi_content' 	=> $tw_testi_content,
			'tw_testi_name' 	=> $tw_testi_name,
			'tw_testi_position' => $tw_testi_position,
			'tw_box_color' 		=> $tw_box_color,
			'tw_font_color' 		=> $tw_font_color,
			'tw_testi_outline_color'=> $tw_testi_outline_color,
		);

		if (empty($rand_no)) {
			$rand_no = $this->create_random_number();
		}
		update_option('get_testimonials_'.$rand_no , $arr);

		//saving random numbers too
		$random_no_arr = get_option('get_saved_random_numbers');

		$random_no_arr[] = $rand_no;
		$result = array_unique($random_no_arr);
		update_option('get_saved_random_numbers' , $result );
		
		$url = admin_url('admin.php?page=improveseo_shortcodes');
		wp_send_json(array('status' => 'success' , 'url' => $url));
		die;
	}

	/****=====Saving Form Fields From Admin Side For videos====***/
	function wt_save_form_fields_for_videos(){
		$rand_no = isset($_REQUEST['updateandedit_data']) ? $_REQUEST['updateandedit_data'] : '';

		$video_shortcode_name = isset($_REQUEST['video_shortcode_name'])?$_REQUEST['video_shortcode_name']:'';
		$video_type = isset($_REQUEST['video_type'])?$_REQUEST['video_type']:'upload_video';

		$video_poster_img_source = isset($_REQUEST['video_poster_img_source'])?$_REQUEST['video_poster_img_source']:'';
		$video_poster_img_id = isset($_REQUEST['video_poster_img_id'])?$_REQUEST['video_poster_img_id']:'';

		$video_id_mp4 = isset($_REQUEST['video_id_mp4'])?$_REQUEST['video_id_mp4']:'';
		$video_url_mp4 = isset($_REQUEST['video_url_mp4'])?$_REQUEST['video_url_mp4']:'';

		$video_id_ogv = isset($_REQUEST['video_id_ogv'])?$_REQUEST['video_id_ogv']:'';
		$video_url_ogv = isset($_REQUEST['video_url_ogv'])?$_REQUEST['video_url_ogv']:'';

		$video_id_webm = isset($_REQUEST['video_id_webm'])?$_REQUEST['video_id_webm']:'';
		$video_url_webm = isset($_REQUEST['video_url_webm'])?$_REQUEST['video_url_webm']:'';

		$video_url_vimeo = isset($_REQUEST['video_url_vimeo'])?$_REQUEST['video_url_vimeo']:'';
		$video_url_youtube = isset($_REQUEST['video_url_youtube'])?$_REQUEST['video_url_youtube']:'';

		$video_autoplay = isset($_REQUEST['video_autoplay'])?$_REQUEST['video_autoplay']:'no';
		$video_muted = isset($_REQUEST['video_muted'])?$_REQUEST['video_muted']:'no';
		$video_controls = isset($_REQUEST['video_controls'])?$_REQUEST['video_controls']:'no';
		$video_loop = isset($_REQUEST['video_loop'])?$_REQUEST['video_loop']:'no';
		$video_height = isset($_REQUEST['video_height'])?$_REQUEST['video_height']:'auto';
		$video_width = isset($_REQUEST['video_width'])?$_REQUEST['video_width']:'100%';		

		$arr = array(
			'video_shortcode_name'		=> $video_shortcode_name,
			'video_type'				=> $video_type,
			'video_poster_img_source'	=> $video_poster_img_source,
			'video_poster_img_id' 		=> $video_poster_img_id,
			'video_id_mp4' 				=> $video_id_mp4,
			'video_url_mp4'				=> $video_url_mp4,
			'video_id_ogv' 				=> $video_id_ogv,
			'video_url_ogv'				=> $video_url_ogv,
			'video_id_webm' 			=> $video_id_webm,
			'video_url_webm' 			=> $video_url_webm,
			'video_url_vimeo' 			=> $video_url_vimeo,
			'video_url_youtube' 		=> $video_url_youtube,
			'video_autoplay' 			=> $video_autoplay,
			'video_muted' 				=> $video_muted,
			'video_controls' 			=> $video_controls,
			'video_loop' 				=> $video_loop,
			'video_height' 				=> $video_height,
			'video_width' 				=> $video_width,
		);

		if (empty($rand_no)) {
			$rand_no = $this->create_random_number();
		}
		update_option('get_videos_'.$rand_no , $arr);

		//saving random numbers too
		$random_no_arr = get_option('get_saved_random_numbers');

		$random_no_arr[] = $rand_no;
		$result = array_unique($random_no_arr);
		update_option('get_saved_random_numbers' , $result );

		$url = admin_url('admin.php?page=improveseo_shortcodes');
		wp_send_json(array('status' => 'success' , 'url' => $url));
		die;
	}

	/****=====Creating The Random Number====***/
	function create_random_number(){

	    $rand_no = wp_rand(1,76000);
	    $multi = wp_rand(4,140);
	    $plus = wp_rand(0,140007);
	    $final_no =($rand_no*$multi)+$plus;
		return $final_no;

	}


	/****=======load files on frontend page======****/
	function load_script_style_files(){
		
		wp_enqueue_style('tmm_stlye_css', WT_URL."/css/wt-style.css",  true);
		wp_enqueue_script('tmm_script_js', WT_URL."/js/wt-script.js",  array('jquery'), IMPROVESEO_VERSION, true);

		//sweet alert
		wp_enqueue_script('tmm_sweetalerttt', WT_URL."/js/wt-sweetalert.js",  array('jquery'));
	    wp_localize_script('tmm_script_js', 'ajax_vars', array(
	    	'ajax_url'      		=> 	admin_url( 'admin-ajax.php' ),
	    	)
		);

	}

}
new WC_Testimonial;