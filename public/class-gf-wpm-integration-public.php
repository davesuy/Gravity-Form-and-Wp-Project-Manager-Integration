<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/davesuy
 * @since      1.0.0
 *
 * @package    Gf_Wpm_Integration
 * @subpackage Gf_Wpm_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gf_Wpm_Integration
 * @subpackage Gf_Wpm_Integration/public
 * @author     Dave Ramirez <davesuywebmaster@gmail.com>
 */

require_once WP_PLUGIN_DIR. '/wedevs-project-manager/core/Notifications/Email.php';
require_once WP_PLUGIN_DIR. '/wedevs-project-manager/core/Notifications/Emails/New_Comment_Notification.php';


/**
* Email Notification When a new project created
*/
use WeDevs\PM\Core\Notifications\Email;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Task_List\Models\Task_List;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Discussion_Board\Models\Discussion_Board;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\Comment\Models\Comment;
use WeDevs\PM\File\Models\File;

use WeDevs\PM\Core\Notifications\Emails;

class Gf_Wpm_Integration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/* Gf and Wpm */

	public $project_endpoints;

	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $project_endpoints) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->project_endpoints = $project_endpoints;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gf_Wpm_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gf_Wpm_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gf-wpm-integration-public.css', array(), $this->version, 'all' );
		wp_enqueue_style('jquery-ui-css-aiml', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',array(), '1.0.0', 'all');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gf_Wpm_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gf_Wpm_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('jquery-ui-aiml', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), false, true );

		wp_enqueue_script( 'js-const' , plugin_dir_url( __FILE__ ) . 'js/gf-wpm-integration-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( 'js-const', 'js_const_object',
			array( 
				'homeurl' => get_bloginfo('url')
			)
		);

	}


	/* Gf and Wpm Integration function */

	public function wp_head_func() {
		
 
		if( function_exists('get_field')) {
		
			$args = array('orderby' => 'display_name');
	
			$wp_user_query = new WP_User_Query($args);
			
			$authors = $wp_user_query->get_results();
		
			if (!empty($authors)) {

				$username_output = [];

				foreach ($authors as $author) {
				 
					$author_info = get_userdata($author->ID);
					$username = $author_info->display_name;
					
					$username_output[$author->ID] = $username;

					$task_color = get_field('task_color', 'user_'.$author->ID );
	
					//echo '<pre>'.print_r($author_info->user_nicename, true).'</pre>';
				}

				?>

				<style>

					<?php

					if (function_exists('get_field')) {
						
						foreach($username_output as  $key => $item) {

							//echo '<pre>'.print_r($key.' - '.$item, true).'</pre>';	

							$task_color = get_field('task_color', 'user_'.$key );

							if( $task_color ) {
											
								?>
							
								.cpm-assigned-user a[title="<?php echo  $item; ?>"]:before {
									background: <?php echo  $task_color; ?>;
								}

								.kbc-content-inside:has(> .cpm-assigned-user) + .kbc-title-wrap a {
									color: #fff !important; m
								}

								<?php

							}


						}

					

						
		
					}
					
					?>

				</style>
	
				<?php

				
			
			 
			} 
	
	
		}
	
	}

	public function save_post_type( $task, $request ) {

		// if ( !isset( $request['privacy'] ) ){
		//     return ;
		// }

	
		/* Production */
		$project_id = $this->project_endpoints->get_project_id_boab_aiml_community();
	
		/* Local */
	   // $project_id = 3;
	  
		if($request['project_id'] == $project_id) {
			
	
			$endpoint = $this->project_endpoints->get_endpoint_save_post_type();
	
	
			$task_url = get_bloginfo('url').'/#/projects/'.$request['project_id'].'/task-lists/tasks/'.$task->id;
			
			$body = [
				'task_title'  => $task->title,
				'task_url' => $task_url,
				'task_id' => $task->id,
				'project_id' => $request['project_id']
			]; 
			
			$body = wp_json_encode( $body );
			
			$options = [ 
				'body'        => $body,
				'headers'     => [
					'Content-Type' => 'application/json',
				],
				'timeout'     => 60,
				'redirection' => 5, 
				'blocking'    => true,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'data_format' => 'body',
			];
	
			wp_remote_post( $endpoint, $options );
	
		}
	   
	}

	/*** For Testing ***/

	public function test_data() {
		
		//$b = $this->project_endpoints;
			
		global $wpdb;
	
		$wpdb->show_errors();
	
		$table_boardables = $this->project_endpoints->db_table_boardables(); 

		$board_type = "task_list";
		
	
		$result = $wpdb->get_results("SELECT * FROM $table_boardables WHERE  board_type = '{$board_type}'  ORDER BY id DESC");
	
		//return $result[0]->order + 1; 

		//$a = $gf_wpm_integration->get_project_endpoints();
		//echo '<pre>'.print_r($result, true).'x</pre>';
		//echo '<pre>'.print_r($this->project_endpoints->db_table_tasks(), true).'</pre>';


	}
	
	public function boab_define_approver_to_field( $feedback, $entry, $assignee, $new_status, $form, $step ) {

	
		$step_id_approval = $this->project_endpoints->boab_step_id_approval;
	
		//Modify this to match the field ID of the user field that you add to your form. It will be what your post-approval step gets assigned to.
		$field_id_user = $this->project_endpoints->field_user;
	
	 
	
		if ( $step->get_id() !== $step_id_approval ) {
			return $feedback;
		}
	
		if ( $new_status == 'approved' ) {
	
			$user = get_user_by( 'ID', $assignee->get_id() );
			
			if ( $user ) {
	
				GFAPI::update_entry_field( $entry['id'], $field_id_user, $user->ID );
				
				$note = sprintf( esc_html__( 'Updating Post-Approval Step Assignee to: %s (%s)' ), $user->display_name, $user->ID );
	
				$step->add_note( $note, true );    
	
			}
	
			global $wpdb;
	
			$task_title = $entry[1];
			$task_desc = $entry[2];
			$task_url = $entry[5];
			$task_id = $entry[3];
			$project_id = $entry[4];
	
			$table_name = $this->project_endpoints->db_table_assignees();    
		
			$date = date('Y-m-d H:i:s'); 
	   
			$current_user = wp_get_current_user();
	
		   // $step->add_note( $current_user->ID.' task id '.$task_id, true );  
	
			$exists = $wpdb->insert( $table_name, array(
				'task_id' => $task_id,
				'assigned_to' => $current_user->ID,
				'status' => 0,
				'created_by' => 1,
				'updated_by' => 1,
				'assigned_at' => $date,
				'started_at' => NULL,
				'completed_at' => NULL,
				'project_id' => $project_id,
				'created_at' => $date,
				'updated_at' => $date,
			
			));
			//exit();
	
			
			/****** Incoming Webhook for the Task Approver *******/

			$endpoint = $this->project_endpoints->get_endpoint_approver_field();
	
	
			$body = [
				'user_id'   => $current_user->user_login,
				'username'  => $current_user->ID,
				'email' =>  $current_user->user_email
			
			];
			
			$body = wp_json_encode( $body );
			
			$options = [
				'body'        => $body,
				'headers'     => [
					'Content-Type' => 'application/json',
				],
				'timeout'     => 60,
				'redirection' => 5,
				'blocking'    => true,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'data_format' => 'body',
			];
	
			wp_remote_post( $endpoint, $options );
	
		}
	
		return $feedback;
	}

	public function gform_populate_user_role($value){

		$user = wp_get_current_user();
		$role = $user->roles;
		return reset($role);

	}

	public function add_readonly_script( $form ) {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				/* apply only to a input with a class of gf_readonly */
				jQuery(".gf_readonly input").attr("readonly","readonly");
				jQuery(".gf_readonly input[type=checkbox]").attr("onclick","return false;");
				jQuery(".gf_readonly input[type=radio]").attr("onclick","return false;");
				/*jQuery("option:not(:selected)").prop("disabled","return true;");*/
			});
		</script>
		<?php
		return $form;
	}

	public function gw_prepopluate_merge_tags( $form ) {

		global $gw_filter_names;
	
		$gw_filter_names = array();
	
		foreach( $form['fields'] as &$field ) {
	
			if( ! rgar( $field, 'allowsPrepopulate' ) ) {
				continue;
			}
	
			// complex fields store inputName in the "name" property of the inputs array
			if( is_array( rgar( $field, 'inputs' ) ) && $field['type'] != 'checkbox' ) {
				foreach( $field->inputs as $input ) {
					if( $input['name'] ) {
						$gw_filter_names[ $input['name'] ] = GFCommon::replace_variables_prepopulate( $input['name'] );
					}
				}
			} else {
				$gw_filter_names[ $field->inputName ] = GFCommon::replace_variables_prepopulate( $field->inputName );
			}
	
		}
	
		foreach( $gw_filter_names as $filter_name => $filter_value ) {
	
			if( $filter_value && $filter_name != $filter_value ) {
				add_filter( "gform_field_value_{$filter_name}", function( $value, $field, $name ) {
					global $gw_filter_names;
					$value = $gw_filter_names[ $name ];
					/** @var GF_Field $field  */
					if( $field->get_input_type() == 'list' ) {
						remove_all_filters( "gform_field_value_{$name}" );
						$value = GFFormsModel::get_parameter_value( $name, array( $name => $value ), $field );
					}
					return $value;
				}, 10, 3 );
			}
	
		}
	
		return $form;
	}

	public function add_task( $entry, $form ) {

		$task_title = rgar( $entry, '1' );
		$task_description  = rgar( $entry, '2' );
		$task_project_id = 6;
		$assigned_to  = rgar( $entry, '4' );
		$start_at = rgar( $entry, '6' );
		$due_date = rgar( $entry, '5' );
	   
	
		$this->project_endpoints->add_task_from_gf($task_title, $task_description, $task_project_id, $assigned_to, $start_at, $due_date);
	
	}


	
	public function title_task_population_function( $value ) {

		$task_id = $_GET['task_id'];
	
		global $wpdb;
	
		$wpdb->show_errors();
	
		$table_tasks = $this->project_endpoints->db_table_tasks();  
		
		$result = $wpdb->get_results("SELECT * FROM $table_tasks WHERE  id = '{$task_id}' ");
	
		//return '<pre>'.print_r(	$table_tasks, true).'</pre>';
	
		return $result[0]->title;
		
	}

	public function description_task_population_function( $value ) {

		$task_id = $_GET['task_id'];
	
		global $wpdb;
	
		$wpdb->show_errors();
	
		$table_tasks = $this->project_endpoints->db_table_tasks();  
		
	
		$result = $wpdb->get_results("SELECT * FROM $table_tasks WHERE  id = '{$task_id}' ");
	
	
		//return '<pre>'.print_r($result, true).'</pre>';
	
		return $result[0]->description;
		
	}

	public function user_task_population_function( $value ) {

		$task_id = $_GET['task_id'];
	
		global $wpdb;
	
		$wpdb->show_errors();
	
		$table_assignees = $this->project_endpoints->db_table_assignees();    
		
	
		$results = $wpdb->get_results("SELECT assigned_to FROM $table_assignees WHERE task_id = '{$task_id}'
		");
	
	
	   // return '<pre>'.print_r($results, true).'</pre>';
	
		return $results[0]->assigned_to;
		
	}
	
	public function start_at_task_population_function( $value ) {

		$task_id = $_GET['task_id'];

		global $wpdb;

		$wpdb->show_errors();

		$table_tasks = $this->project_endpoints->db_table_tasks();
		

		$result = $wpdb->get_results("SELECT start_at FROM $table_tasks WHERE  id = '{$task_id}' ");


		//return '<pre>'.print_r($result, true).'</pre>';

		$start_at_format = date('m/d/Y',  strtotime($result[0]->start_at));


		$start_at_format_out = '';
		
		if (strtotime($result[0]->start_at) != "") {

			$start_at_format_out =   $start_at_format;

		} 

		return  $start_at_format_out;
		
	}

	
	public function due_date_population_function( $value ) {

		$task_id = $_GET['task_id'];

		global $wpdb;

		$wpdb->show_errors();

		$table_tasks = $this->project_endpoints->db_table_tasks(); 
		
		$result = $wpdb->get_results("SELECT * FROM $table_tasks WHERE  id = '{$task_id}' ");


		//return '<pre>'.print_r($result, true).'</pre>';

		$due_date_format = date('m/d/Y',  strtotime($result[0]->due_date));


		$due_date_format_out = '';
		
		if (strtotime($result[0]->due_date) != "") {

			$due_date_format_out = $due_date_format;

		} 

		return $due_date_format_out;
		
	}

	
	public function update_form_task( $entry, $form ) {

		$task_id = $_GET['task_id'];

		$task_title = rgar( $entry, '1' );
		$task_description  = rgar( $entry, '2' );
		$task_project_id = 6;
		$assigned_to  = rgar( $entry, '4' );
		$start_at = rgar( $entry, '6' );
		$due_date = rgar( $entry, '5' );

		global $wpdb;  

		$wpdb->show_errors();
	
		$table_tasks = $this->project_endpoints->db_table_tasks(); 
		$table_assignees = $this->project_endpoints->db_table_assignees(); 

		$task_id = $_GET['task_id'];

		$task_title = rgar( $entry, '1' );
		$task_description  = rgar( $entry, '2' );
		$task_project_id = 6;
		$assigned_to  = rgar( $entry, '4' );
		$start_at = rgar( $entry, '6' );
		$due_date = rgar( $entry, '5' );

		
		$query = $wpdb->prepare("
		UPDATE $table_tasks INNER JOIN $table_assignees
		SET $table_tasks.title = '{$task_title}', $table_tasks.description = '{$task_description}', $table_tasks.start_at = '{$start_at}', $table_tasks.due_date = '{$due_date}', $table_assignees.assigned_to = '{$assigned_to}' WHERE $table_assignees.task_id = '{$task_id}' AND $table_tasks.id = {$task_id}
		");

		$results = $wpdb->get_results( $query );

	}

	public function incoming_webhook_add_task( $entry, $form ) {

		$instance_proj_end = new Gf_Wpm_Projects_Endpoints;


		$workflow_api_key = $instance_proj_end->workflow_api_key;
		$workflow_api_secret = $instance_proj_end->workflow_api_secret;

		$entry_id = $entry['id'];
	
		$task_title = rgar( $entry, '1' );
		$task_description  = rgar( $entry, '2' );
		$task_project_id = $instance_proj_end->get_project_id_boab_aiml_community();
		$assigned_to  = rgar( $entry, '4' );
		$start_at = rgar( $entry, '6' );
		$due_date = rgar( $entry, '5' );
	
		$endpoint = get_bloginfo('url').'/wp-json/gf/v2/entries/'.$entry_id.'/workflow-hooks';
	
		$body = [
			'workflow-api-key' => $workflow_api_key,
			'workflow-api-secret' =>  $workflow_api_secret,
			'task_title'  => $task_title,
			'task_description' => $task_description,
			'task_id' => $task->id,
			'task_project_id' =>  $task_project_id,
			'assign_user' =>  $assigned_to
		];
		
		$body = wp_json_encode( $body );
		
		$options = [
			'body'        => $body,
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
		];
	
		$response = wp_remote_post( $endpoint, $options );

	
		// $my_post = array(
		//     'ID'           => 2191,
		//     'post_content' => 'sdasd'
		// );
	
		// // Update the post into the database
		// wp_update_post( $my_post );
	
	
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
	
			return false;
	
		} else {
			
			$instance_proj_end->add_task_from_gf($task_title, $task_description, $task_project_id, $assigned_to, $start_at, $due_date);
	
		}
	
	}

	public function workflow_register_nav_menu() {

		register_nav_menus( array(
            'workflow_menu' => __( 'Workflow Menu', 'text_domain' )
        ) );

	}

	public function gravityflow_columns_status_func($columns, $args, $athis) {
		
		
		unset( $columns['date_created'] );
		return $columns;
	}

	public function set_pm_comments( $entry, $form ) {


		//getting post
		$comment_id = get_post( $entry['post_id'] );
	
		$project_id = rgar( $entry, '1' );
		$commentable_id = rgar( $entry, '4' );
		$message = rgar( $entry, '3' );
		$creator_email = rgar( $entry, '5' );
		$attachment_link = rgar( $entry, '6' );
	
		$user = get_user_by( 'email', $creator_email );
		$creator_id = $user->ID;
		
		global $wpdb;
		
		$wpdb->show_errors();
		
		$table_comments = $wpdb->prefix.'pm_comments';     
	
		$date = date('Y-m-d H:i:s');   
		
		$query = $wpdb->insert( $table_comments, array(
			'content' =>  $message,
			'commentable_id' => $commentable_id,
			'project_id' => $project_id,
			'commentable_type' => 'task',
			'created_by' =>  $creator_id,
			'updated_by' =>  $creator_id,
			'created_at' => $date,
			'updated_at' => $date,
		  
		));

		$getLastInsertedId = $wpdb->insert_id;

	 	$this->attachment_link_func($attachment_link, $getLastInsertedId, $project_id, $creator_id);
	
		//do_action( 'cpm_comment_new', $comment_id,  $project_id, $entry );
			
	   // do_action( 'pm_after_new_comment', $entry, $request->get_params());
	
	
	}

	public function attachment_link_func($file_url, $getLastInsertedId, $project_id, $created_by) {

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();  
    
		// Now you can use it!
		//$file_url = 'https://zapier-dev-files.s3.amazonaws.com/cli-platform/19137/f5ORWDKg7PyeZ6TIgAf7TD0fpEa99FecIyWV10NGPI0ZGUzzTjm6jhkCrWkgdo_P89bEN8QDeHLZ_FCbOMrerRz8IuyG712UWMgrLZUgbeb4g6EMI6xjQ0nDkeCiguLdDB1Lh0DkVu5S_ZRf_Mx2Eu0K1SH5cEa2YclCHA17bDU';
	  
		
		$tmp_file = download_url( $file_url );
	
		$base_name_tmp_file = basename(  $tmp_file );
		
		// Sets file final destination.
		$filepath = $wp_upload_dir['path'].'/'.$base_name_tmp_file;
		
		// Copies the file to the final destination and deletes temporary file.
		copy( $tmp_file, $filepath );
		@unlink( $tmp_file );
	
		
		// $filename should be the path to a file in the upload directory.
		$filename = $filepath;
		
		// The ID of the post this attachment is for.
		//$parent_post_id = 1572;
		
		// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $filename ), null );
		
	  
		
		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'].'/'.$base_name_tmp_file, 
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

	
		// Insert the attachment.
		//$attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
		$attach_id = wp_insert_attachment( $attachment, $filename);

		global $wpdb;

		$table_pm_files = $wpdb->prefix.'pm_files';

		$date = date('Y-m-d H:i:s'); 

		
		$exists = $wpdb->insert( $table_pm_files, array(
			'fileable_id' => $getLastInsertedId,
			'fileable_type' => 'comment',
			'type' => 'file',
			'attachment_id' => $attach_id,
			'parent' => 0,
			'project_id' => $project_id,
			'created_by' => $created_by,
			'updated_by' => $created_by,
			'created_at' => $date,
			'updated_at' => $date,
		));

	}

	public function init_func() {


       add_action('pm_after_new_comment_notification', array($this, 'email_notification_trigger'), 10, 2 );

		//$a = new WeDevs\PM\Core\Notifications\Emails\New_Comment_Notification;
		//echo '<pre>'.print_r($a, true).'</pre>';

		//$Emails = new Email;

		//remove_action('pm_after_new_comment_notification', array(  $Emails::getInstance() , "trigger" ) );
		
	}


	public function email_notification_trigger( $commentData, $request ) {

		$Emails = new Email;

        if ( empty( $request['notify_users'] ) ){
            return ;
        }
        
        $project      = Project::with('assignees', 'managers')->find( $request['project_id'] );
        $users        = array();
        $notify_users = explode( ',',  $request['notify_users'] );

        foreach ( $notify_users as $u ) {
            if( $Emails->is_enable_user_notification( $u ) ){
                if( $Emails->is_enable_user_notification_for_notification_type( $u, '_cpm_email_notification_new_comment' ) ){
                    $users[] = $project->assignees->where( 'ID', $u )->first()->user_email;
                }
            }
        }

        if( $Emails->notify_manager() ){
            foreach ( $project->managers->toArray() as $u ) {
                if ( !in_array( $u['user_email'], $users ) ) {
                    $users[] = $u['user_email'];
                }
            }
        }

        if ( !$users ){
            return ; 
        }

        if ( $request['commentable_type'] == 'discussion_board' ) {
            $type = __( 'Message', 'wedevs-project-manager' );
            $comment_link = $Emails->pm_link() . '#/projects/'.$project->id.'/discussions/'.$request['commentable_id'];
            $title = Discussion_Board::find( $request['commentable_id'] )->title;

        } else if ( $request['commentable_type'] == 'task_list' ) {
            $type = __( 'Task List', 'wedevs-project-manager' );
            $comment_link = $Emails->pm_link() . '#/projects/'.$project->id.'/task-lists/'.$request['commentable_id'];
            $title = Task_List::find( $request['commentable_id'] )->title;

        } else if ( $request['commentable_type'] == 'task' ) {
            $type        = __( 'Task', 'wedevs-project-manager' );
            $comment_link = $Emails->pm_link() . '#/projects/'.$project->id. '/task-lists/tasks/'.$request['commentable_id'];
            $title = Task::find( $request['commentable_id'] )->title;

        } else if ( $request['commentable_type'] == 'file' ) {
            $type        = __( 'File', 'wedevs-project-manager' );
            $file = File::find($request['commentable_id']);
            $comment_link = $Emails->pm_link() . '#/projects/'. $project->id .'/files/'. $file->parent .'/'. $file->type .'/'. $request['commentable_id'];
            $filemeta = Meta::where( 'project_id', $request['project_id'] )
                            ->where( 'entity_type', 'file' )
                            ->where( 'entity_id',  $request['commentable_id'])
                            ->where( 'meta_key', 'title' )
                            ->first();
            $title = $filemeta->meta_value;
        }

        $template_name = apply_filters( 'pm_new_comment_email_template_path', $Emails->get_template_path( '/html/new-comment.php' ) );
		//$template_name =	plugin_dir_url( __FILE__ ) . 'html/new-comment.php';

		$subject       = sprintf( __( '[%s][%s] New Comment on.: %s', 'wedevs-project-manager' ), $Emails->get_blogname(), $project->title , $title );       
        
        $message = $Emails->get_content_html( $template_name, [
            'id'                => $commentData['data']['id'],
            'content'           => $request['content'],
            'updater'           => $commentData['data']['updater']['data']['display_name'],
            'commnetable_title' => $title,
            'commnetable_type'  => $type,
            'comment_link'      => $comment_link,
            'created_at'        => $commentData['data']['created_at']['date'],
            'creator'           => $commentData['data']['creator'],
            'project_id'      => $project->id,
            'commentable_id'      => $request['commentable_id'],
			'request'      => $request,
        ] );

        //$Emails->send( $users, $subject, $message );
        
        /**
         * Custom Send Email Boab
         */

        $to = $users;
        $headers = [];
        $attachments = null;
        
        $blogname     = $Emails::getInstance()->get_blogname();
        $server_name = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ): '';
        //$no_reply     =  $commentData['data']['creator']['data']['email'];
        $no_reply     =  'boabpm@aiml.community';
        $content_type = 'Content-Type: text/html';
        $charset      = 'Charset: UTF-8';
        $from_email   = $Emails::getInstance()->from_email();
        $from         = "From: $blogname <$from_email>";
        $reply_to     = "Reply-To: $no_reply";

        if ( $Emails::getInstance()->is_bcc_enable() ) {
            
            if ( is_array( $to ) ) {
                $bcc     = 'Bcc: ' . implode(',', $to);
            } else {
                $bcc     = 'Bcc: ' . $to;
            }
            
            $headers = array(
                $bcc,
                $reply_to,
                $content_type,
                $charset,
                $from_email
            );

            return wp_mail( $from_email, $subject, wp_kses_post( htmlspecialchars_decode( $message ) ), $headers, $attachments );
            
        } else {
            
            $headers = array(
                $reply_to,
                $content_type,
                $charset,
                $from,
            );
            
            return wp_mail( $to, $subject, wp_kses_post( htmlspecialchars_decode( $message ) ), $headers, $attachments );
        }
    }

	public function gravityflow_status_args_func($default_args) {
		
		$default_args['per_page'] = 2;

		return $default_args;
	}
	
}
