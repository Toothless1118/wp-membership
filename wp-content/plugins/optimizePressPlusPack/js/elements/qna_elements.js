var op_asset_settings = (function($){
	var columms = ['large-q','qa-text'];
	var titletextcolor = ['style7'];
	var textcolor = ['style7'];
	var bgcolor = ['style7'];
	return {
		help_vids: {
			step_1: {
				url: 'http://op2-inapp.s3.amazonaws.com/elements-q-and-a.mp4',
				width: '600',
				height: '341'
			},
			step_2: {
				url: 'http://op2-inapp.s3.amazonaws.com/elements-q-and-a.mp4',
				width: '600',
				height: '341'
			}
		},
		attributes: {
			step_1: {
				style: {
					type: 'style-selector',
					folder: 'previews',
					addClass: 'op-disable-selected'
				}
			},
			step_2: {
				columns: {
					title: 'columns',
					type: 'select',
					valueRange: {start:2,finish:3},
					showOn: {field:'step_1.style',value:columms}
				},
				titletextcolor:{
					title: 'title_text_color',
					type: 'color',
					default_value: '#3973a0',
					showOn: {field:'step_1.style',value:titletextcolor}
				},
				textcolor:{
					title: 'text_color',
					type: 'color',
					default_value: '#000000',
					showOn: {field:'step_1.style',value:textcolor}
				},
				bgcolor:{
					title: 'bg_color',
					type: 'color',
					default_value: '#e9eff4',
					showOn: {field:'step_1.style',value:bgcolor}
				},
				question: {
					title: 'qna_questions',
					type: 'multirow',
					link_suffix: 'qna_question',
					multirow: {
						attributes: {
							question: {
								title: 'qna_question'
							},
							content: {
								title: 'qna_answer',
								type: 'wysiwyg'
							}
						}
					}
				}
			}
		},
		insert_steps: {2:true},
		customInsert: function(attrs){
			var str = '',
				style = attrs.style || 'qa-text',
				columns = attrs.columns || 2,
				question = attrs.question || attrs.op_question;
				titletextcolor = attrs.titletextcolor;
				textcolor = attrs.textcolor;
				bgcolor = attrs.bgcolor;

			var colorEdit = '';
			switch(style){
				case 'style7':
				    colorEdit = ' titletextcolor="' + titletextcolor + '"' + ' textcolor="' + textcolor + '"' + ' bgcolor="' + bgcolor + '"';
				    break;
			}

			$.each(question,function(i,v){
				var q = encodeURIComponent(v.question || ''),
					a = encodeURIComponent(v.content || '');

				str += '[op_question question="'+q.replace( /"/ig,"'")+'"' + colorEdit + ']'+a+'[/op_question] ';
			});

			str = '[qna_elements style="'+style+'" columns="'+columns+'"]'+str+'[/qna_elements]';
			OP_AB.insert_content(str);

			$(document).on('op.afterLiveEditorParse',function(){
				window.OptimizePress.initQNAElements();
			});

			$.fancybox.close();
		},
		customSettings: function(attrs,steps){
			var style = attrs.attrs.style,
				columns = attrs.attrs.columns,
				questions = attrs.question || attrs.op_question;

			//Set the style
			OP_AB.set_selector_value('op_assets_core_qna_elements_style_container',style);

			//Set the number of columns
			$('#op_assets_core_qna_elements_columns').find('option').each(function(){
				if ($(this).val()==columns) $(this).attr('selected', 'selected');
			});

			//Loop through each of the questions and add the question and answer fields for each
            if (typeof(questions) !== 'undefined') {
                $.each(questions, function (index, val) {
                    //Trigger the click event on the add question button
                    //so it adds a new question to the multirow container.
                    //Also return the multirow container next to this button's parent container
                    var multirow = steps[1].find('.field-question a.new-row').trigger('click').parent().prev().find('.op-multirow').last(),
                        id = multirow.find('.field-content textarea').attr('id'); //Return the ID to the answer field

                    //Set the question field
                    multirow.find('.field-question input').val(op_decodeURIComponent(val.attrs.question));

					var titletextcolor = val.attrs.titletextcolor,
						textcolor = val.attrs.textcolor,
						bgcolor = val.attrs.bgcolor;

					if (typeof(titletextcolor) !== 'undefined') {
						$('#op_assets_core_qna_elements_titletextcolor').val(titletextcolor).next('a').css({ backgroundColor: titletextcolor });
					}
					if (typeof(textcolor) !== 'undefined') {
						$('#op_assets_core_qna_elements_textcolor').val(textcolor).next('a').css({ backgroundColor: textcolor });
					}
					if (typeof(bgcolor) !== 'undefined') {
						$('#op_assets_core_qna_elements_bgcolor').val(bgcolor).next('a').css({ backgroundColor: bgcolor });
					}

                    //Set the content of the answer field
                    if (typeof val.attrs.content != 'undefined') {
                        OP_AB.set_wysiwyg_content(id, op_decodeURIComponent(val.attrs.content));
                    }
                });
            }
		}
	};
}(opjq));