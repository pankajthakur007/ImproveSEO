<!-- <style type="text/css">
 	table {
	  font-family: arial, sans-serif;
	  border-collapse: collapse;
	  width: 100%;
	}
	
	.wt-icons{
	    cursor: pointer;
	}
	#connection-key{
	    display:none;
	}

	td, th {
	  border: 1px solid #dddddd;
	  text-align: center !important;
	  padding: 8px;
	}

	tr:nth-child(even) {
	  background-color: #dddddd;
	}
</style> -->
<?php
    
    $saved_rand_nos_keywords = get_option('swsaved_random_nosofkeywords');
        if(empty($saved_rand_nos_keywords)) {
            return;
        }

?>
    <section class="project-table-wrapper pt-4">
        <div class="form table-responsive-sm">
     <table class="table widefat fixed wp-list-table widefat fixed table-view-list posts text-center">
        <thead>
    <tr>
        <th scope="col" style="width:20%" class="text-center manage-column manage-column column-title column-primary">No#</th>
        <th scope="col" style="width:60%"class="text-center manage-column">Project Name</th>
        <th scope="col" style="width:20%" class="text-center manage-column">Actions</th>
    </tr>
    </thead>
    <tbody>
        <?php 


            $html = '';
            $no = 1;
            foreach ($saved_rand_nos_keywords as $keyowrd_id) {

                $get_keyworddata = get_option('swsaved_keywords_with_results_'.$keyowrd_id);
                
                if (empty($get_keyworddata)) {
                    continue;
                }
                $html .= '<tr>';
                $html .= '<td class="column-title column-primary has-row-actions">'.$no.' <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';

                $kw_proj_name = isset($get_keyworddata['proj_name']) ? $get_keyworddata['proj_name'] : '';
                

                $html .= '<td data-colname="Project Name">'.$kw_proj_name.'</td>
                        <td class="actions-btn" data-colname="Actions"><span data-keyword_rand_id='.$keyowrd_id.' class="kw-download-kwproject wt-icons ct-btn btn btn-outline-primary mr-2">Save</span><span data-keyword_rand_id='.$keyowrd_id.' style="color:red" class="kw-dlt-kwproject wt-icons del-btn btn btn-outline-danger">Remove</span></td>
                        </tr>';

            
                $no++;
            }
            echo $html;
         ?>
         </tbody>
</table>
</div>
</section>