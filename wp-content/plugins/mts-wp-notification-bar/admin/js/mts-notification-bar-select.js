(function( $ ) {
	'use strict';

	$(function() {

		// Bar select
		function mtsnbProcessPostSelectDataForSelect2( ajaxData, page, query ) {

			var items=[];

			for (var thisId in ajaxData) {
				var newItem = {
					'id': ajaxData[thisId]['id'],
					'text': ajaxData[thisId]['title']
				};
				items.push(newItem);
			}
			return { results: items };
		}

		$('input.mtsnb-bar-select').each(function() {

			var $this = $(this);

			$this.select2( {
				placeholder: mtsnb_locale.select_placeholder,
				multiple: true,
				maximumSelectionSize: 1,
				minimumInputLength: 2,
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					data: function (term, page) {
						return {
							q: term,
							action: 'mtsnb_get_bars',
						};
					},
					results: mtsnbProcessPostSelectDataForSelect2
				},
				initSelection: function(element, callback) {

					var ids=$(element).val();
					if ( ids !== "" ) {
						$.ajax({
							url: ajaxurl,
							dataType: "json",
							data: {
								action: 'mtsnb_get_bar_titles',
								post_ids: ids
							},
							
						}).done(function(response) {console.log(response);
							
							var processedData = mtsnbProcessPostSelectDataForSelect2( response );
							callback( processedData.results );
						});
					}
				},
			});
		});
	});

})( jQuery );
