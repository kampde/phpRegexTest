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
<form method="get" action="">
	<label>Regex to test: <input type="text" name="expr" value="<?php if (isset($expr)) echo htmlspecialchars($expr, ENT_QUOTES, 'utf-8');?>"/></label>
<?php if (isset($error)) echo "<em class='error'>$error</em>\n";?>
	<input type="submit" value="Test Regex" />
</form>
<pre><?php echo htmlspecialchars(file_get_contents($file), ENT_QUOTES, 'utf-8');?></pre>
<?php
if (!empty($output)) {
	echo "<h2>Matching lines</h2>\n";

	echo "<pre>", show_output($output), "</pre>";
}
