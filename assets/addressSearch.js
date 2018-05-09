var addressSearch = function() {
	var fillAddress = function( event, ui ) {
		$(event.target).parent().parent().find("input[name*='[street]']").val( ui.item.street);
		$(event.target).parent().parent().find("input[name*='[number]']").val( ui.item.number);
		$(event.target).parent().parent().find("input[name*='[city]']").val( ui.item.city);
		$(event.target).parent().parent().find("input[name*='[zip]']").val( ui.item.zip);
		$(event.target).parent().parent().find("*[name*='[country]']").val( ui.item.country);
		$(event.target).parent().parent().find("input[name*='[lat]']").val( ui.item.geometry.location.lat);
		$(event.target).parent().parent().find("input[name*='[lng]']").val( ui.item.geometry.location.lng);
	};

	$("input[name*='[street]']").keypress(function() {
		var source = $(this).data('source');
		$(this).autocomplete({
			minLength: 3,
			source: function( request, response ) {
				$.ajax({
					type: 'GET',
					url: source,
					data: {
						street: request.term
					},
					success: function( data ) {
						response( data );
					}
				});
			},
			focus: function( event, ui ) {
				$(this).val(ui.item.address);
				return false;
			},
			select: fillAddress,
			change: fillAddress
		})
		.autocomplete("instance")._renderItem = function( ul, item ) {
			return $("<li>")
			.append("<a>" + item.label + "</a>" )
			.appendTo( ul );
		};
	});
};

$(document).ready(function(){
	$.nette.ext('addressSearch', {
		init: addressSearch(),
		complete: addressSearch
	});
});
