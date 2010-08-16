$(document) .ready(function(){
	$('.toggle_toc').click(function(){
		$(this) .parent().children('.toc_sub').toggle();
	});
});