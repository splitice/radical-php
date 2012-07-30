define(["jquery",'jqueryui/tabs'],function(a){
	var callback = function(){
		var data = 
			{
				idPrefix: 'ui-tabs-outer',
				ajaxOptions: {
					error: function( xhr, status, index, anchor ) {
						$( anchor.hash ).html(
							"Couldn't load this tab. We'll try to fix this as soon as possible."
						);
					},
					type: 'post',
					data: {_admin:'outer'},
					complete: callback
				}
			};
		$('.tabs.outer').tabs(data);
		
		data = $.extend(true,{},data);
		data.ajaxOptions.data._admin = 'inner';
		data.idPrefix = 'ui-tabs-inner';
		$('.tabs.inner').tabs(data);
	};
	$(callback);
});