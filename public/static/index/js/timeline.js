layui.use('jquery', function () {
	var $ = layui.jquery;
	$(function () {
	    $('.monthToggle').click(function () {
	        $(this).parent('h3').siblings('ul').slideToggle('slow');
	        $(this).children('i').toggleClass('fa-caret-down fa-caret-right');
	    });
	    $('.yearToggle').click(function () {
	        $(this).parent('h2').siblings('.timeline-month').slideToggle('slow');
	        $(this).children('i').toggleClass('fa-caret-down fa-caret-right');
	    });
	});
});
