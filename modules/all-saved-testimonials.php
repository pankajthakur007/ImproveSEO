<section>
    <div class="project-heading pt-4">
        <h1>Testimonials</h1>
    </div>
    <section class="project-table-wrapper">
        <div class="form table-responsive-sm">
            <table class="table widefat fixed wp-list-table widefat fixed table-view-list posts text-center">
                <thead>
                    <tr>
                        <th scope="col" class="text-center manage-column manage-column column-title column-primary" style="width:12%">Name/#ID</th>
                        <th scope="col" class="text-center manage-column">Testimonial IMG</th>
                        <th scope="col" class="text-center manage-column" style="width:25%;">Content</th>
                        <th scope="col" class="text-center manage-column">Name</th>
                        <th scope="col" class="text-center manage-column" style="width:20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ids = get_option('get_saved_random_numbers');
                    
                    $html = '';
                    if(!empty($ids)){
                        foreach ($ids as $id) {
                            $get_data = get_option('get_testimonials_'.$id);
                            if (empty($get_data)) {
                                continue;
                            }

                            $testi_img_src = isset($get_data['testi_img_src']) ? $get_data['testi_img_src'] : '';
                            $tw_testi_content = isset($get_data['tw_testi_content']) ? $get_data['tw_testi_content'] : '';
                            $tw_testi_name = isset($get_data['tw_testi_name']) ? $get_data['tw_testi_name'] : '';
                            

                            $shortcode_name = isset($get_data['tw_testi_shortcode_name']) ? $get_data['tw_testi_shortcode_name'] : '';
                            $display_name = ($shortcode_name!="") ? $shortcode_name : $id;

                            $content = stripslashes($tw_testi_content);
                            $content = (strlen($content) > 100)?substr($content, 0, 100).'...': $content;
                            

                            $html .= '<tr>';
                                $html .= '<td class="column-title column-primary has-row-actions">'.$display_name.'</td>';
                                
                                $html .= '<td data-colname="Testimonial IMG">';
                                if($testi_img_src!=""){
                                    $html .= '<img style="width:45px;height:45px;" src='.$testi_img_src.' />';
                                }else{
                                    $html .= 'No Image';
                                }
                                $html .= '</td>';
                                $html .= '<td data-colname="Content">'.$content.'</td>
                                <td data-colname="Name">'.$tw_testi_name.'</td>
                                <td class="actions-btn" data-colname="Actions"><span data-action="testimonial" data-rand_id='.$id.' class="wt-edit-testi btn btn-outline-primary mr-2">Edit</span><span data-action="testimonial" data-rand_id='.$id.' class="wt-dlt-testi wt-icons btn btn-outline-danger">Remove</span></td>
                            </tr>';
                        }
                    }
                    echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    
    <div class="project-heading pt-4">
        <h1>Google Maps</h1>
    </div>
    <section class="project-table-wrapper">
        <div class="form table-responsive-sm">
            <table class="table widefat fixed wp-list-table widefat fixed table-view-list posts text-center">
                <thead>
                    <tr>
                        <th scope="col" class="text-center manage-column manage-column column-title column-primary" style="width:10%">Name/#ID</th>
                        <th scope="col" class="text-center manage-column">Google Maps APIKEY</th>
                        <th scope="col" class="text-center manage-column" style="width:20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ids = get_option('get_saved_random_numbers');
                    $html = '';
                    if(!empty($ids)){
                        foreach ($ids as $id) {
                            $get_data = get_option('get_googlemaps_'.$id);
                            if (empty($get_data)) {
                                continue;
                            }
                            $shortcode_name = isset($get_data['tw_maps_shortcode_name']) ? $get_data['tw_maps_shortcode_name'] : '';
                            $display_name = ($shortcode_name!="") ? $shortcode_name : $id;

                            $html .= '<tr>';
                                $html .= '<td class="column-title column-primary has-row-actions">'.$display_name.'</td>';
                                $tw_maps_apikey = isset($get_data['tw_maps_apikey']) ? $get_data['tw_maps_apikey'] : '';
                                $html .= '<td data-colname="Google Maps APIKEY">'.$tw_maps_apikey.'</td>
                                <td class="actions-btn" data-colname="Actions"><span data-action="googlemaps" data-rand_id='.$id.' class="wt-edit-testi btn btn-outline-primary px-4 mr-2 py-2">Edit</span><span data-action="googlemaps" data-rand_id='.$id.' class="wt-dlt-testi wt-icons btn btn-outline-danger py-2">Remove</span></td>
                            </tr>';
                        }
                    }
                    echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <div class="project-heading pt-4">
        <h1>Buttons</h1>
    </div>
    <section class="project-table-wrapper">
        <div class="form table-responsive-sm">
            <table class="table widefat fixed wp-list-table widefat fixed table-view-list posts text-center">
                <thead>
                    <tr>
                        <th scope="col" class="text-center manage-column manage-column column-title column-primary" style="width:10%">Name/#ID</th>
                        <th scope="col" class="text-center manage-column">Button Type</th>
                        <th scope="col" class="text-center manage-column">Button Text</th>
                        <th scope="col" class="text-center manage-column">Button Link/Number</th>
                        <th scope="col" class="text-center manage-column" style="width:20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ids = get_option('get_saved_random_numbers');
                   
                    $html = '';
                    if(!empty($ids)){
                        foreach ($ids as $id) {
                            $get_data = get_option('get_buttons_'.$id);
                            if (empty($get_data)) {
                                continue;
                            }
                            $shortcode_name = isset($get_data['tw_button_shortcode_name']) ? $get_data['tw_button_shortcode_name'] : '';
                            $display_name = ($shortcode_name!="") ? $shortcode_name : $id;

                            $html .= '<tr>';
                                $html .= '<td class="column-title column-primary has-row-actions">'.$display_name.'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';
                                $tw_btn_text = isset($get_data['tw_btn_text']) ? $get_data['tw_btn_text'] : '';
                                $tw_btn_link = isset($get_data['tw_btn_link']) ? $get_data['tw_btn_link'] : '';
                                
                                $tw_buttontxt_color = isset($get_data['tw_buttontxt_color']) ? $get_data['tw_buttontxt_color'] : '';
                                $tw_buttonbg_color = isset($get_data['tw_buttonbg_color']) ? $get_data['tw_buttonbg_color'] : '';
                                $tw_button_type = isset($get_data['tw_button_type']) ? $get_data['tw_button_type'] : 'normal_btn';
                                $tw_tap_btn_text = isset($get_data['tw_tap_btn_text']) ? $get_data['tw_tap_btn_text'] : '';
                                $tw_tap_btn_number = isset($get_data['tw_tap_btn_number']) ? $get_data['tw_tap_btn_number'] : '';

                                if($tw_button_type=="normal_btn"){
                                    $html .= '<td data-colname="Button Type">Normal Button</td>';
                                    $html .= '<td data-colname="Button Text">'.$tw_btn_text.'</td>';
                                    $html .= '<td data-colname="Button Link">'.$tw_btn_link.'</td>';
                                }else{
                                    $html .= '<td data-colname="Button Type">Tap To Call</td>';
                                    $html .= '<td data-colname="Button Text">'.$tw_tap_btn_text.'</td>';
                                    $html .= '<td data-colname="Button Link">'.$tw_tap_btn_number.'</td>';
                                }
                                $html .= '
                                <td class="actions-btn" data-colname="Actions"><span  data-action="buttons" data-rand_id='.$id.' class="wt-edit-testi wt-icons btn btn-outline-primary px-4 mr-2 py-2">Edit</span><span data-rand_id='.$id.' data-action="buttons" class="wt-dlt-testi wt-icons btn btn-outline-danger py-2">Remove</span></td>
                            </tr>';
                        }
                    }
                    echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="project-heading pt-4">
        <h1>Videos</h1>
    </div>
    <section class="project-table-wrapper">
        <div class="form table-responsive-sm">
            <table class="table widefat fixed wp-list-table widefat fixed table-view-list posts text-center">
                <thead>
                    <tr>
                        <th scope="col" class="text-center manage-column manage-column column-title column-primary" style="width:10%">Name/#ID</th>
                        <th scope="col" class="text-center manage-column" >Video Type</th>
                        <th scope="col" class="text-center manage-column" style="width:17%;">Video Poster</th>
                        <th scope="col" class="text-center manage-column" style="width:20%;">Video URLs</th>
                        <th scope="col" class="text-center manage-column" style="width:18%;">Autoplay - Mute - Controls - Loop</th>
                        <th scope="col" class="text-center manage-column" style="width:20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ids = get_option('get_saved_random_numbers');
                    $html = '';
                    if(!empty($ids)){
                        foreach ($ids as $id) {
                            $get_data = get_option('get_videos_'.$id);
                            if (empty($get_data)) {
                                continue;
                            }
                            $shortcode_name = isset($get_data['video_shortcode_name']) ? $get_data['video_shortcode_name'] : '';
                            $display_name = ($shortcode_name!="") ? $shortcode_name : $id;

                            $video_type = isset($get_data['video_type'])?$get_data['video_type']:'upload_video';

                            $video_poster_img_source = isset($get_data['video_poster_img_source'])?$get_data['video_poster_img_source']:'';
                            $video_url_mp4 = isset($get_data['video_url_mp4'])?$get_data['video_url_mp4']:'';
                            $video_url_ogv = isset($get_data['video_url_ogv'])?$get_data['video_url_ogv']:'';
                            $video_url_webm = isset($get_data['video_url_webm'])?$get_data['video_url_webm']:'';
                            $video_url_vimeo = isset($get_data['video_url_vimeo'])?$get_data['video_url_vimeo']:'';
                            $video_url_youtube = isset($get_data['video_url_youtube'])?$get_data['video_url_youtube']:'';

                            $video_autoplay = isset($get_data['video_autoplay'])?$get_data['video_autoplay']:'no';
                            $video_muted = isset($get_data['video_muted'])?$get_data['video_muted']:'no';
                            $video_controls = isset($get_data['video_controls'])?$get_data['video_controls']:'no';
                            $video_loop = isset($get_data['video_loop'])?$get_data['video_loop']:'no';
                            
                            if($video_type=="upload_video")
                                $video_type_display = "Upload Video";
                            elseif($video_type=="youtube")
                                $video_type_display = "YouTube";
                            elseif($video_type=="vimeo")
                                $video_type_display = "Vimeo";

                            $html .= '<tr>';
                            $html .= '<td class="column-title column-primary has-row-actions">'.$display_name.'</td>';
                            $html .= '<td data-colname="video_type">'.$video_type_display.'</td>';
                            if($video_poster_img_source!="")
                                $html .= '<td data-colname="video_poster_img"><img src="'.$video_poster_img_source.'" style="max-width:200px; height:100px;" /></td>';
                            else
                                $html .= '<td data-colname="video_poster_img">No Poster Image</td>';
                            
                            
                            $html .= '<td data-colname="video_urls">';
                            if($video_type=="upload_video"){
                                if($video_url_mp4!=""){
                                    $html .= '<a href="'.$video_url_mp4.'" target="_blank">'.$video_url_mp4."</a><hr />";
                                }
                                
                                if($video_url_ogv!=""){
                                    $html .= '<a href="'.$video_url_ogv.'" target="_blank">'.$video_url_ogv."</a><hr />";
                                }

                                if($video_url_webm!=""){
                                    $html .= '<a href="'.$video_url_webm.'" target="_blank">'.$video_url_webm."</a><hr />";
                                }
                            }elseif($video_type=="youtube"){
                                $html .= '<a href="'.$video_url_youtube.'" target="_blank">'.$video_url_youtube."</a><hr />";
                            }elseif($video_type=="vimeo"){
                                $html .= '<a href="'.$video_url_vimeo.'" target="_blank">'.$video_url_vimeo."</a><hr />";
                            }
                            $html .= '</td>';

                            $html .= '<td data-colname="video_extras">'.ucfirst($video_autoplay).' - '.ucfirst($video_muted).' - '.ucfirst($video_controls).' - '.ucfirst($video_loop).'</td>';
                            
                            $html .= '
                                <td class="actions-btn" data-colname="Actions"><span data-action="videos" data-rand_id='.$id.' class="wt-edit-testi btn btn-outline-primary px-4 mr-2 py-2">Edit</span><span data-action="videos" data-rand_id='.$id.' class="wt-dlt-testi wt-icons btn btn-outline-danger py-2">Remove</span></td>
                            </tr>';
                        }
                        echo $html;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</section>