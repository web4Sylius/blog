jQuery(document).ready(function($){
	"use strict";


	$('#qc_clr_checked_all').on('click', function(){
		if($(this).is(':checked')){
			$('.qc_clr_email_checkbox').prop('checked', true);
		}else{
			$('.qc_clr_email_checkbox').prop('checked', false);
		}
	})
	
	$('#qc_clr_submit_email_form').on('click', function(e){
		e.preventDefault();
		alert('pro Feature');
	})
	
	$('.qc-clr-email-del').on('click', function(e){
		e.preventDefault();

		var data_id = $(this).attr('data-id');

		alert('pro Feature');


		
	})


})


