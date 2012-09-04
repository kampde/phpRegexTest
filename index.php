<?php
function is_valid_regex($e) {
	return @preg_match($e, "") !== false;
}

function highlight_match($matches) {
	return '[match]' . htmlspecialchars($matches[0], ENT_QUOTES, 'utf-8') . '[/match]';
}

function show_output($out) {
	return preg_replace('@\[match\](.*)\[/match\]@U', '<em class="match">\1</em>', htmlspecialchars($out, ENT_QUOTES, 'utf-8'));
}

$file = 'test_regex.txt';
if (isset($_GET['expr'])) {
	$expr = $_GET['expr'];

	if (!is_valid_regex($expr)) {
		$error = "ERROR: regex " . htmlspecialchars($expr, ENT_QUOTES, 'utf-8') . " is invalid";
	} else {
		$output = "";
		foreach (file($file) as $k => $line) {
			if (preg_match($expr, $line, $matches)) {
				$output .= preg_replace_callback($expr, 'highlight_match', $line);
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<title>Testing regular expressions</title>
		<link rel="stylesheet" href="styles.css"/>
	</head>
	<body>
		<h1>Test your regular expression</h1>
		<h2>Sample input</h2>
		<pre><?php echo htmlspecialchars(file_get_contents($file), ENT_QUOTES, 'utf-8');?></pre>
		<form method="get" action="">
			<label>Regex to test: <input type="text" name="expr" value="<?php if (isset($expr)) echo htmlspecialchars($expr, ENT_QUOTES, 'utf-8');?>"/></label>
<?php if (isset($error)) echo "<em class='error'>$error</em>\n";?>
			<input type="submit" value="Test Regex" />
		</form>
<?php
if (!empty($output)) {
	echo "<h2>Matching lines</h2>\n";

	echo "<pre>", show_output($output), "</pre>";
} else if (!empty($expr)) {
	echo "<p>No matches...</p>\n";
}
?>
	</body>
</html>
