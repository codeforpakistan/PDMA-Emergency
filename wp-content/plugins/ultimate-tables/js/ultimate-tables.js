// JavaScript Document
				
				
				jQuery('#table_'+object_name.id).dataTable( {
		"bPaginate": object_name.sizethumbnail,
		"bLengthChange": false,
		"bFilter": object_name.op1,
		"bSort": object_name.op2,
		"bInfo": object_name.op3,
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		
		"oLanguage": {
			"sLengthMenu": "Display _MENU_ records per page",
			"sZeroRecords": "Nothing found - sorry",
			"sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
			"sInfoEmpty": "Showing 0 to 0 of 0 records",
			"sInfoFiltered": "(filtered from _MAX_ total records)"
		}
		
		} 
		
		
				);
	alert(object_name.sizethumbnail);