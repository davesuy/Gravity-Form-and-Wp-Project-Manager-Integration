<?php

require_once WP_PLUGIN_DIR. '/gravityflow/includes/pages/class-status.php';

class Gf_Wpm_Integration_Public_shortcodes extends Gf_Wpm_Integration_Public {

	public $project_endpoints;
	
	public function __construct($project_endpoints) {

		$this->project_endpoints = $project_endpoints;

	}

	
	/*** Shortcodes ***/
	
	public function add_shortcode_func() {

		add_shortcode('gravityflowtab', array($this,'gravityflowtab_func'));
		add_shortcode( 'query_task_user', array($this,'query_task_user_shortcode_func'));
		add_shortcode( 'workflow_menu', array($this,'workflow_menu_func'));
		add_shortcode('workflow_custom_data', array($this, 'workflow_custom_data_func'));

	}

	/*** Callback Method ***/

	public function gravityflowtab_func() {
		 
		// $content = '<div id="aiml_tabs">
	
		// 	<ul>
		// 		<li><a href="#inbox">Inbox</a></li>
		// 		<li><a href="#status">Status</a></li>
		// 		<li><a href="#submit">Submit</a></li>
		// 		<li><a href="#reports">Reports</a></li>
		// 	</ul>';

		$content = '<div id="aiml_tabs">
	
			<ul>
				
				<li><a href="#status">Status</a></li>
			
			</ul>';
	
			$content .= '<div id="gravityflowtabs">';
	
				// $content .= '<div id="inbox">';
				// 	$content .= do_shortcode('[gravityflow page="inbox"]');
				// $content .= '</div>';
	
				$content .= '<div id="status">';  
					$content .= do_shortcode('[gravityflow page="status" id_column="false" due_date="false" timeline="false" submitter_column="false" workflow_info="false" actions_column="false"]');
				$content .= '</div>';
	
				// $content .= '<div id="submit">'; 
				// 	$content .= do_shortcode('[gravityflow page="submit"]');
				// $content .= '</div>'; 
	
				// $content .= '<div id="reports">'; 
				// 	$content .= do_shortcode('[gravityflow page="reports"]');
				// $content .= '</div>';  
	
			$content .= '</div>'; 
	
		$content .= '</div>'; 
	
	
		return $content;
	
	}

	public function query_task_user_shortcode_func( $atts ) {

		$atts = shortcode_atts( array(
			'project_id' => ''
	
		), $atts, 'query_task_user' );
		
		$gf_wpm_integration = $this->project_endpoints;
	
		$query_tasks = $gf_wpm_integration->query_task_for_user($atts['project_id']);

	   	return $query_tasks;
	
	}

	public function workflow_menu_func() {

		ob_start();

			wp_nav_menu( array(
				'theme_location' => 'workflow_menu',
				'items_wrap'     => '<div class="boab-workflow-menu elementor-field-type-select elementor-field-group elementor-column elementor-col-100"><form class="elementor-form workflow-form-menu"><div id="boab-redirect-form" class="elementor-field elementor-select-wrapper"><select class="elementor-field-textual elementor-size-lg"><option>Select Workflow </option>%3$s</select></div></form></div>',
				'walker'  =>  new WPDocs_Walker_Nav_Menu()
			) );

		return ob_get_clean();


	}

	public function workflow_custom_data_func() {

		//return ABSPATH . 'wp-content/plugins/gravityflow/includes/pages/class-status.php' ;
  
		$inst_gfst = new Gravity_Flow_Status_Table;
		$form_ids = $inst_gfst->get_workflow_form_ids();
	
	
		// $search_criteria = $inst_gfst->get_search_criteria();
		// $entries = GFAPI::get_entries( $form_ids,  $search_criteria  );
	
		$items = $inst_gfst->prepare_items();
	
		ob_start();

		//echo count($inst_gfst->items);

		$i = 1;
	
		foreach( (array) $inst_gfst->items as $item) {
	
			$step_id = rgar( $item, 'workflow_step' );
			echo $i++.'.)';
			?>
	
				<p><label>Form: </label>
	
					<?php
	
						$inst_gfst->column_form_id($item);
					  
	
					?>
	
				</p>
				
				<p><label>Current Step: </label>
	
					<?php
	
						  $inst_gfst->column_workflow_step($item);
	
					?>
	
				</p>

				<p><label>Status: </label>
	
					<?php

						$inst_gfst->column_workflow_final_status($item);

					?>

				</p>
	
			<?php
		  
			//echo '<p><label>Current Step:</label> '.$inst_gfst->column_workflow_step($item).'</p>';
		   // echo  '<p><label>Status:</label> '.$inst_gfst->column_workflow_final_status($item).'</p>';
			//echo  '<p><label>Form:</label> '.$inst_gfst->column_form_id($item).'</p>';
			
		}
	
	   //echo  '<pre>'.print_r(    $n->items , true).'</pre>';
	 
	   return ob_get_clean();

	}


}

class WPDocs_Walker_Nav_Menu extends Walker_Nav_Menu {
	 
	 
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
 
		// Depth-dependent classes.
		$depth_classes = array(
			( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
			( $depth >=2 ? 'sub-sub-menu-item' : '' ),
			( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
			'menu-item-depth-' . $depth
		);
		$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
 
		// Passed classes.
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
 
		// Build HTML.
		$output .= $indent . '<option value="'.$item->url.'" id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';
 
		// Link attributes.
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';
 
		// Build HTML output and pass through the proper filter.
		$item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
			$args->before,
			$attributes,
			$args->link_before,
			apply_filters( 'the_title', $item->title, $item->ID ),
			$args->link_after,
			$args->after
		);
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$output .= "</option>{$n}";
	}


}