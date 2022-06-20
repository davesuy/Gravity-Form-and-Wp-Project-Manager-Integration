
jQuery( document ).ready(function($) {
  
	$( "#aiml_tabs"  ).tabs();
 
	//  $( "#aiml_tabs"  ).tabs({
	//      collapsible: true,
	//      active: true,
	//      create: function( event, ui ) {
 
	//         // setTimeout(delayLoad, 2000);
	//      }
	//    });
	   
	   //.addClass( "ui-tabs-vertical ui-helper-clearfix" );
 
	//  function delayLoad(){
	//     $( "#aiml_tabs" ).tabs( "option", "active", 0 );
	//  }
 
	 //$( "#aiml_tabs-other"  ).tabs();
 
	// $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

	//alert(js_const_object.homeurl);

	/**
	 * Redirect to Kanban Board
	 */

	if (window.location.href.indexOf("/#/projects/") > -1) {
	
		var count = 0;

		$(document).ajaxStop(function(){

					
			var suffix = window.location.href.match(/\d+/); // 123456


			if(suffix) {

				//console.log(suffix[0]);

				var tasklist_url = js_const_object.homeurl + "/projects/#/projects/" + suffix[0] + "/task-lists";
				var kanboard_url = js_const_object.homeurl + "/projects/#/projects/" + suffix[0] + "/kanboard";

				if (window.location.href  ===  tasklist_url ) {

					count++;
				
					//alert(1);

					if(count == 1) {

						window.location.href = kanboard_url;

					}

				}

				
				// if(window.location.href  === kanboard_url ) {

				// 	console.log(kanboard_url);

				// 	var url = kanboard_url + "?users=0&title=&lists=0&dueDate=0&status=complete&filterTask=active";

				// 	//document.location = url;
				// 	//window.location.replace(url);
				// 	//window.location.href = url;
				// 	//window.location.reload();
			
				// 	setTimeout(function(){
					
						
				// 	 },5000);// after 5 seconds

					
				// }

			}

		});

	}

	//$('body').on('click', '.active-task-filter', function () {
		//alert(100);
		//$('.kbc-kanboard .list-search-menu').remove();
   //});

	//setTimeout(function(){
    //	 $('.active-task-filter').trigger('click');
  // }, 4000);

	//alert(0);
	

	/**
	 * Dropdown Redirect for Workflow
	 * 
	 */

	 $("#boab-redirect-form select").change(function() {

		var href_val = $(this).val();

		//console.log('SELECTED' + href_val);

		if (href_val != "") {
			//console.log('redirect' + href_val);
			//window.location.href = href_val;
			window.open(href_val);
		}

	});

 });