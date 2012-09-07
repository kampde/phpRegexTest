<?php
define('SAMPLES_DIR', 'samples');
/*
if (isset($_GET['l'])) {
	$locale = 'ca_ES.utf8';
} else {
	$locale = 'en_US.utf8';
}
if (($newLocale = setlocale(LC_ALL, $locale)) === false) {
	echo "Failed to set locale to $locale<br />\n";
} else {
	//echo "Testing locale $newLocale<br />\n";
}
*/
function is_valid_regex($e) {
	return @preg_match($e, "") !== false;
}

function show_output($out) {
	return preg_replace('@\[match\](.*)\[/match\]@Us', '<em class="match">\1</em>', htmlspecialchars($out, ENT_QUOTES, 'utf-8'));
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
    $file = current($files);
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
			$output .= preg_replace($expr, '[match]\0[/match]', $matchLine);
		}
	}
}

$showMatches = '';
if (isset($_GET['showMatches'])) {
    $showMatches = ' checked="checked"';
}
$p_m_all = '';
$preg_match = 'preg_match';
if (isset($_GET['p_m_all'])) {
    $p_m_all = ' checked="checked"';
    $preg_match = 'preg_match_all';
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
                <div>
                    <label>
                        <input type="checkbox" name="showMatches" value="1"<?php echo $showMatches;?>/>
                            Show $matches
                    </label>
                    <label>
                        (<input type="checkbox" name="p_m_all" value="1"<?php echo $p_m_all;?>/>
                        Using <code>preg_match_all</code> instead of <code>preg_match</code>)
                    </label>
                </div>
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
            $preg_match($expr, $matchLine, $matches);
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
