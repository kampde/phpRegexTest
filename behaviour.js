function myonready() {
	$('#file').change(function() {
		var f = $(this).val();
		$.get(f, function(response) {
			$('#sampleInput').text(response);
		});
	});
}

$(document).ready(myonready);
