define(["jQuery",'jqueryui/tabs'],function(a){
	var callback = function(){
		var data = 
			{
				ajaxOptions: {
					error: function( xhr, status, index, anchor ) {
						$( anchor.hash ).html(
							"Couldn't load this tab. We'll try to fix this as soon as possible. " +
							"If this wouldn't be a demo." 
						);
					},
					type: 'post',
					data: {_admin:'outer'},
					complete: callback
				}
			};
		$('.tabs.outer').tabs(data);
		
		data.ajaxOptions.data._admin = 'inner';
		$('.tabs.inner').tabs(data);
	};
	$(callback);
});