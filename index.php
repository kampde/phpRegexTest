<?php
define('SAMPLES_DIR', 'samples');
function is_valid_regex($e) {
	return @preg_match($e, "") !== false;
}

function highlight_match($matches) {
	return '[match]' . htmlspecialchars($matches[0], ENT_QUOTES, 'utf-8') . '[/match]';
}

function show_output($out) {
	return preg_replace('@\[match\](.*)\[/match\]@U', '<em class="match">\1</em>', htmlspecialchars($out, ENT_QUOTES, 'utf-8'));
}

function showFileSelectOptions($files, $selected) {
    foreach ($files as $file) {
        if ($file == $selected) {
            $selectedText = ' selected="selected"';
        } else {
            $selectedText = '';
        }
        echo "<option value='$file'$selectedText>$file</option>\n";
    }
}

$files = array_filter(scandir(SAMPLES_DIR), function($elem) {
    return $elem[0] != '.';
});

if (!empty($_GET['file']) && in_array($_GET['file'], $files) === true) {
    $file = $_GET['file'];
} else {
    $file = $files[0];
}
if (isset($_GET['js'])) {
    // ajax call, just return the file
    header('Content-Type: text/plain; charset=utf-8');
    readfile(SAMPLES_DIR . "/$file");
    die();
}
if (isset($_GET['expr'])) {
	$expr = $_GET['expr'];

	if (!is_valid_regex($expr)) {
		$error = "ERROR: regex " . htmlspecialchars($expr, ENT_QUOTES, 'utf-8') . " is invalid";
	} else {
		$output = "";
		foreach(($matchedLines = preg_grep($expr, file(SAMPLES_DIR . "/$file"))) as $matchLine) {
			$output .= preg_replace_callback($expr, 'highlight_match', $matchLine);
		}
	}
}

$showMatches = '';
if (isset($_GET['showMatches'])) {
    $showMatches = ' checked="checked"';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<title>Testing regular expressions</title>
		<link rel="stylesheet" href="<?php echo SAMPLES_DIR;?>/styles.css"/>
        <script type="text/javascript" src="jquery-1.8.1.min.js"></script>
        <script type="text/javascript" src="behaviour.js"></script>
	</head>
	<body>
		<h1>Test your regular expression</h1>
		<h2>Sample input</h2>
		<pre id="sampleInput"><?php echo htmlspecialchars(file_get_contents(SAMPLES_DIR . "/$file"), ENT_QUOTES, 'utf-8');?></pre>
		<form method="get" action="">
            <fieldset>
                <div>
                    <label>Sample text:
                        <select id="file" name="file">
                            <?php showFileSelectOptions($files, $file);?>
                        </select>
                    </label>
                </div>
                <div>
                    <label>Regex to test: <input type="text" name="expr" value="<?php if (isset($expr)) echo htmlspecialchars($expr, ENT_QUOTES, 'utf-8');?>"/></label>
                    <?php if (isset($error)) echo "<em class='error'>$error</em>\n";?>
                </div>
                <div><label>Show $matches <input type="checkbox" name="showMatches" value="1"<?php echo $showMatches;?>/></label></div>
                <input type="submit" value="Test Regex" />
            </fieldset>
		</form>
<?php
if (!empty($output)) {
	echo "<h2>Matching lines</h2>\n";

	echo "<pre>", show_output($output), "</pre>";

    if ($showMatches) {
        echo "<h2>\$matches array</h2>\n";
        foreach ($matchedLines as $matchLine) {
            preg_match($expr, $matchLine, $matches);
            echo "<h3>For line '<em class='code'>" . rtrim($matchLine, "\n") . "</em>'</h3>\n";
            echo "<pre>"; var_dump($matches); echo "</pre>\n";
        }
    }
} else if (!empty($expr)) {
	echo "<p>No matches...</p>\n";
}
?>
	</body>
</html>
