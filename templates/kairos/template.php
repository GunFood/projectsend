<?php
/*
Template name: Kairos
URI: http://www.projectsend.org/templates/kairos
Author: KAIROS GmbH
Author URI: http://kairos.de
Author e-mail: infrastruktur@kairos.de
Description: Kairos modified template
*/

$ld = 'cftp_template'; // specify the language domain for this template

define('TEMPLATE_RESULTS_PER_PAGE', 25);

if ( !empty( $_GET['category'] ) ) {
	$category_filter = $_GET['category'];
}

include_once(ROOT_DIR.'/templates/common.php'); // include the required functions for every template

$window_title = __('File downloads','cftp_template');

$footable_min = true; // delete this line after finishing pagination on every table
$load_scripts	= array(
	'footable',
); 

$body_class = array('template', 'default-template', 'hide_title');

include_once(ROOT_DIR.'/header.php');
?>

<div class="col-xs-12">
	<div id="wrapper">
		<div id="right_column">
			<div class="form_actions_left">
				<div class="form_actions_limit_results">
					<!-- <?php show_search_form(); ?> -->
					<?php
						if ( !empty( $cat_ids ) ) {
					?>
					<form action="" name="files_filters" method="get" class="form-inline form_filters">
						<?php form_add_existing_parameters( array('category', 'action') ); ?>
						<div class="form-group group_float">
							<select name="category" id="category" class="txtfield form-control">
								<option value="0"><?php _e('All categories','cftp_admin'); ?></option>
								<?php
									$selected_parent = ( isset($category_filter) ) ? array( $category_filter ) : array();
									echo generate_categories_options( $get_categories['arranged'], 0, $selected_parent, 'include', $cat_ids );
								?>
							</select>
						</div>
						<button type="submit" id="btn_proceed_filter_files" class="btn btn-sm btn-default"><?php _e('Filter','cftp_admin'); ?></button>
					</form>
					<?php
						}
					?>
				</div>
			</div>
				<form action="" name="files_list" method="get" class="form-inline">
				<?php form_add_existing_parameters(); ?>
				
				<div class="right_clear"></div><br />
				<div class="form_actions_count">
					<!-- <p class="form_count_total"><?php _e('Found','cftp_admin'); ?>: <span><?php echo $count_for_pagination; ?> <?php _e('files','cftp_admin'); ?></span></p> -->
				</div>
					<div class="right_clear"></div>
				<?php
					if (!$count_for_pagination) {
						if (isset($no_results_error)) {
							switch ($no_results_error) {
								case 'search':
									$no_results_message = __('Your search keywords returned no results.','cftp_admin');
									break;
							}
						}
						else {
							$no_results_message = __('There are no files available.','cftp_template');
						}
						echo system_message('error',$no_results_message);
					}
					if ($count > 0) {
						/**
						 * Generate the table using the class.
						 */
						$table_attributes	= array(
							'id'		=> 'files_list',
							'class'		=> 'footable table',
						);
						$table = new generateTable( $table_attributes );
						$thead_columns		= array(
							array(
								'sortable'		=> true,
								'sort_url'		=> 'filename',
								'content'		=> __('Title','cftp_admin'),
							),
							array(
								'content'		=> __('Type','cftp_admin'),
								'hide'			=> 'phone',
							),
							array(
								'sortable'		=> true,
								'sort_url'		=> 'description',
								'content'		=> __('Description','cftp_admin'),
								'hide'			=> 'phone',
								'attributes'	=> array(
									'class'		=> array( 'description' ),
								),
							),
							array(
								'content'		=> __('Size','cftp_admin'),
								'hide'			=> 'phone',
							),
							array(
								'content'		=> __('Category','cftp_admin'),
							),
							array(
								'content'		=> __('Download','cftp_admin'),
								'hide'			=> 'phone',
							),
						);
	
						$table->thead( $thead_columns );

						foreach ($my_files as $file) {
							$download_link = make_download_link($file);
							$table->add_row();
							/**
							 * Prepare the information to be used later on the cells array
							 */
							/** File title */
							$file_title_content = '<strong>' . htmlentities($file['name']) . '</strong>';
							if ($file['expired'] == false) {
								$filetitle = '<a href="' . $download_link . '" target="_blank">' . $file_title_content . '</a>';
							}
							else {
								$filetitle = $file_title_content;
							}
							
							/** Extension */
							$pathinfo = pathinfo($file['url']);
							$extension = strtolower($pathinfo['extension']);
							$extension_cell = '<span class="label label-success label_big">' . $extension . '</span>';
							
							/** Description */
							$description = htmlentities_allowed($file['description']);
							
							/** File size */
							$file_size_cell = '-'; // default
							$file_absolute_path = UPLOADED_FILES_FOLDER . $file['url'];
							if ( file_exists( $file_absolute_path ) ) {
								$this_file_size = get_real_size(UPLOADED_FILES_FOLDER.$file['url']);
								$file_size_cell = format_file_size($this_file_size);
							}
							
							/** Category */
							$category = $file['categories'];
							
							/** Download */
							if ($file['expired'] == true) {
								$download_link		= 'javascript:void(0);';
								$download_btn_class	= 'btn btn-danger btn-sm disabled';
								$download_text		= __('File expired','cftp_template');
							}
							else {
								$download_btn_class	= 'btn btn-primary btn-sm btn-wide';
								$download_text		= __('Download','cftp_template');
							}
							$download_cell = '<a href="' . $download_link . '" class="' . $download_btn_class . '" target="_blank">' . $download_text . '</a>';
							$tbody_cells = array(
								array(
									'content'		=> $filetitle,
									'attributes'	=> array(
										'class'		=> array( 'file_name' ),
									),
								),
								array(
									'content'		=> $extension_cell,
									'attributes'	=> array(
										'class'		=> array( 'extra' ),
									),
								),
								array(
									'content'		=> $description,
									'attributes'	=> array(
										'class'		=> array( 'description' ),
									),
								),
								array(
									'content'		=> $file_size_cell,
								),
								array(
									'content'		=> $file['categories'],
									'attributes'	=> array(
										'class'		=> array( 'extra' ),
									),
								),
								array(
									'content'		=> $download_cell,
									'attributes'	=> array(
										'class'		=> array( 'text-center' ),
									),
								),
							);
							foreach ( $tbody_cells as $cell ) {
								$table->add_cell( $cell );
							}
							$table->end_row();
						}
						echo $table->render();
						/**
						 * PAGINATION
						 */
						$pagination_args = array(
							'link'		=> 'my_files',
							'current'	=> $pagination_page,
							'pages'		=> ceil( $count_for_pagination / TEMPLATE_RESULTS_PER_PAGE ),
						);
						
						echo $table->pagination( $pagination_args );
						

					}
				?>
			</form>
		
		</div> <!-- right_column -->
	</div> <!-- wrapper -->
	
	<?php default_footer_info(); ?>

</div>
	<?php
		load_js_files();
	?>
</body>
</html>