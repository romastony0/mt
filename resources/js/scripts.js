(function($) {
	$.mtutor_qa = $.mtutor_qa || {};
	$(document).ready(function() {
		//$.mtutor_qa.answer_form();
		$.mtutor_qa.ap_respond_time_dropdown();
		$.mtutor_qa.assign_questions();
		$.mtutor_qa.moderator_dashboard();
		$.mtutor_qa.sort_question_answer_status();
		$.mtutor_qa.question_answer_search_filter();
		$.mtutor_qa.topic_search_filter();
		$.mtutor_qa.update_answer_attachment();
		$.mtutor_qa.save_as_draft();
		$.mtutor_qa.moderator_similar_qa();
		
	});
	$.mtutor_qa = {
		ap_respond_time_dropdown: function(){
			var mainContext = $('.mt-buddypress #anspress');
			//dropdown creation
			$(".mt-btn", mainContext).on('click', function(e){ 
		   		var thesi = $(this).parents('#myDropdown');
		   			thesi.children(".dropdown-menu" ).show();				  
		   			e.stopPropagation();
		 	});
		 	$(".dropdown-menu", mainContext).click(function(e){
				e.stopPropagation();
		 	});
			$(document).click(function(){
		  		$(".dropdown-menu", mainContext).hide();
			});

			//get respond time data
			$(".ap-respond-submit", mainContext).on('click', function(e){
				e.preventDefault();
				var respond_time;
				var radioValue = $("input[name='respond_time']:checked"). val();
				if(radioValue){
					respond_time = radioValue;
				}
				var data_url	= $(this).data('href'),
					question_id = $(this).data('question_id'),
					parentDropdown = $(this).parents('#myDropdown'),
					data_id 	= {'question_id': question_id, 'respond_time': respond_time};
					console.log(data_id);
					$.ajax({
						type: 'POST',
						url: data_url,
						data: data_id,
						dataType: 'html',
						success: function(data) {
						 if(data != ""){
							parentDropdown.css("display","none");	
						} 
						
					}
				});
   
			});
			
		},
		
		//assign question to faculty
		assign_questions: function() {
			var post_column = $('.mt-buddypress #manage-questions-table .post-item');
			
			post_column.each( function() {
				var mainContext			= $( '#mt-assignee-wrap', $(this) ),
					branchContext		= $( '.branch-id', mainContext ),
					assigneeContext		= $( '.assignee-id', mainContext ),
					subContext			= $( '.column-assigned-to', $(this) ),
					assigneeWorkload	= $( '.faculty-load', subContext );
				// Branch On Change Event
				branchContext.on('change', function() {
					if( $(this).val() != '' ) {
						var data_id = {'branch_id': $(this).val()};
						$.ajax({
							type: 'POST',
							url: 'faculty_list',
							data: data_id,
							dataType: 'json',
							success: function(response) {
								//console.log(response);
								assigneeContext.html('');
								assigneeContext.append('<option value="">Select Assignee</option>');
								$.each( response, function(k, val) {
									assigneeContext.append('<option value=' + val.id + '>' + val.username + '</option>');
								});
							} 
						});
					}else{
						assigneeContext.html('');
						assigneeWorkload.html('');
					}
				});
				// Assignee Workload
				assigneeContext.on('change', function() {
					if( !$(this).val() == '' ) {
						$.ajax({
							type: 'POST',
							url: 'anspress_assignee_workload',
							data: {'assignee_id': $(this).val()},
							dataType: 'json',
							success: function(response) {
								assigneeWorkload.html('');
								assigneeWorkload.append('<br>In Progress : ' + response.inprogress + '<br>Yet to Start : '+ response.assigned );
							} 
						});
					}
				});
							
				// Button On Click Event
				 $( '.mt-btn', mainContext ).on('click', function(e) {
					e.preventDefault();
					 if( !branchContext.val() == '' ) {
						$.ajax({
							type: 'POST',
							url: 'assign_question',
							data: {'post_id': mainContext.data('post_id'), 'faculty_id': assigneeContext.val()},
							dataType: 'json',
							success: function(response) {
								window.location.reload();
							},
						});
					} 
				}); 
				
				// Button On Click Event to Reject Question
				 $( '.mt-btn-rj', mainContext ).on('click', function(e) {
					e.preventDefault();
					var parent_context = $(this).parents('.question-entry');
					var reject_reason = parent_context.find("#rejected_reason").val();					
					var reject_reason_pl = parent_context.find("#rejected_reason").attr('placeholder');
					if(reject_reason != '' && reject_reason_pl != reject_reason) {					 			
						$.ajax({
						type: 'POST',
						url: 'reject_question',
						data: {'post_id': mainContext.data('post_id'), 'rejected_reason': reject_reason},
						dataType: 'json',
						success: function(response) {
							window.location.reload();
							},
						});	
					}else{
						alert('Answer Reason cannot be empty!');
					}
				});
			});
		},		
		//moderator
		moderator_dashboard: function(){
			var mainContext	= $( '.mt-container #manage-questions-table .modal .modal-body' );
			 $( '.data-approve-answer', mainContext ).on('click', function(e) {
					e.preventDefault();
					var ans_id = $(this).data("ansid"),
						ques_id = $(this).data("ques_id");
					$.ajax({
						type: 'POST',
						url: 'approve_answer',
						data: {'ans_id': ans_id, 'ques_id': ques_id},
						dataType: 'json',
						success: function(response) {
							if( response == 1 ){
								window.location.reload();
							}
						}
					});
			 });
			 
			 $( '.data-reject-answer', mainContext ).on('click', function(e) {
					e.preventDefault();
					var ans_id = $(this).data("ansid");
					var ques_id = $(this).data("ques_id");
					var parent_context = $(this).parents('.answer-entry');
					var ans_reject_reason = parent_context.find("#ans_rejected_reason").val();
					var ans_reject_reason_pl = parent_context.find("#ans_rejected_reason").attr('placeholder');
					if(ans_reject_reason != '' && ans_reject_reason_pl != ans_reject_reason) {
						$.ajax({
							type: 'POST',
							url: 'reject_answer',
							data: {'ans_id': ans_id, 'ques_id': ques_id, 'rejected_reason': ans_reject_reason},
							dataType: 'json',
							success: function(response) {
								if( response == 1 ){
									window.location.reload();
								}
							}
						});
					}else{
						alert('Answer Reason cannot be empty!');
					}
			 });
		},	
		//sort status on moderator dashboard
		sort_question_answer_status: function(){
			var mainContext	= $('#main-content .mt-buddypress .container');
			$('#sort-question-answer-status', mainContext).on('change', function(e){
				e.preventDefault();
				var sort_key = $(this).val(),
					url 	 = $(this).data('location');
					window.location.href = url+sort_key;
				/*$.ajax({
					type: 'POST',
					url: 'moderator_qa_dashboard',
					data: {'sort_key': sort_key},
					dataType: 'json',
					success: function(response) {
						window.location.reload();
					}
				});*/
			});
		},
		question_answer_search_filter: function(){			
			var mainContext = $('#main-content .mt-container .mt-buddypress #anspress'),
				keywordContext = $('#ap-search-form .ap-form-control', mainContext),  
				resultContext = $('.ap-questions').find('.ap-questions-inner .ap-questions-title a'), 
				resultContext2 = $('.ap-questions').find('.ap-questions-inner .ap-questions-title p');             

			//moderator
			keywordContext.keyup(function() {
				//console.log(keywordContext.val());
				var filter = $.trim( keywordContext.val().toLowerCase() );
				$( resultContext ).each(function(){
					if ($(this).text().search(new RegExp(filter, "i")) > -1) {
						$(this).closest('div.ap-questions-inner').parents('.mt-question-item').show();
					} else {
						$(this).closest('div.ap-questions-inner').parents('.mt-question-item').hide();
					} 
				});  
		   });
		   //student
		   	keywordContext.keyup(function() {
				//console.log(keywordContext.val());
				var filter = $.trim( keywordContext.val().toLowerCase() );
				$( resultContext2 ).each(function(){
					if ($(this).text().search(new RegExp(filter, "i")) > -1) {
						$(this).closest('div.ap-questions-inner').parents('.mt-question-item').show();
					} else {
						$(this).closest('div.ap-questions-inner').parents('.mt-question-item').hide();
					} 
				});  
		   });

		},
		topic_search_filter:function(){
			// var mainContext = $(document).find('#main-content .mt-container .ap-tab-container #ask_form'),
				// subjectContext	= $( '#mt_subject_id', mainContext ),
				// topicContext	= $( '#mt_topic_id', mainContext );
			
			// subjectContext.on('change', function() {
					// if( !$(this).val() == '' ) {
						// var data_id = {'subject_id': $(this).val()};
						// $.ajax({
							// type: 'POST',
							// url: 'topic_list',
							// data: data_id,
							// dataType: 'json',
							// success: function(response) {
								// topicContext.html('');
								// topicContext.append('<option value="">Select Topic</option>');
								// $.each( response, function(k, val) {
									// topicContext.append('<option value=' + val.topic_id + '>' + val.topic_name + '</option>');
								// });
							// } 
						// });
					// }else{
						// topicContext.html('');
						// topicContext.append('<option value="">Select Topic</option>');
					// }
				// });
		},
		update_answer_attachment:function(){
			var mainContext = $('#main-content .mt-container #anspress #buddypress '),
				attachment	= $( '#delete-attachment', mainContext );
				attachment.on('click', function(e) {
				e.preventDefault();
				var theis = $(this);
				var attachment_id     = $(this).attr('delete-attachment');
				var ans_attach_file   = $(this).attr('ans-attachment');
				var controller_link   = $(this).attr('controller-link');
				var data_url = controller_link +'QA/update_answer_attachment';
				var data_id 	      = {'ans_id': attachment_id,'ans_attach': ans_attach_file};
				if( attachment_id != '' ){
					if (confirm("Are you really want to delete attachment.?")){
						$.ajax({
							type: 'POST',
							url: data_url,
							data: data_id,
							dataType: 'json',
							success: function(response) {
								if(response.message == true ){
									console.log(theis);
									
									theis.parents('.attachment-container').children('a').remove();
									$('.attachment-container').append('<span style="color: red;">Attachment Deleted..</span>');
								}
							}
						});
					}else{
						console.log("You clicked cancel");
					}
				}else{
					console.log(data_id);
				}
			});
		},
		save_as_draft:function(){
			$( "#save-as-draft" ).click(function(e) {
					if($( "#tinymce" ).val() === ""){
						alert('Please fill out your initials.');
						}
					 if (confirm("Are you really want to save as draft.?")){				
						console.log("answer save as draft");
					 }else{
						 e.preventDefault();
					 }
					 
				});
		},
		moderator_similar_qa:function(){
			var mainContext = $('#main-content .mt-container .content-wrap .answer-block');
			$( '.data-approve-similar', mainContext ).on('click', function(e) {
					e.preventDefault();
					var ans_id = $(this).data("ansid"),
						ques_id = $(this).data("quesid"),
						url 	= $(this).data('location');
						//alert(url);
					if (confirm("Are you really want to approve this answer ?")){
						$.ajax({
							type: 'POST',
							url: 'approve_similar_qa',
							data: {'ans_id': ans_id, 'ques_id': ques_id},
							dataType: 'json',
							success: function(response) {
								if( response == 1 ){									
									window.location.href = url;									
								}
							}
						});
					}else{
						console.log("You clicked cancel");
					}
			 });
				
		},
		
};
})(jQuery);