function myonready() {
	$('#file').change(function() {
		var f = $(this).val();
		$.get('?js=1&file=' + encodeURIComponent(f), function(response) {
			$('#sampleInput').text(response);
		});
	});
}

$(document).ready(myonready);
