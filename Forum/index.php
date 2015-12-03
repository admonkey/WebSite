<?php

include_once('../_resources/credentials.php');
require_once('../_resources/header.php');

echo "<h1>$section_title</h1>";

// default login
if (isset($_GET["default"])){
	$_SESSION["user_id"] = 0;
	$_SESSION["username"] = "Default";
}
if (!isset($_SESSION["username"]))
	echo "<p><a href='?default' class='btn btn-primary'>Login as 'Default'</a></p>";
else echo "
<div id='message_div' class='well'>
	
	<form id='message_form' method='post' role='form'>

		<input name='user_id' type='hidden' value='$_SESSION[user_id]'></input>

		<div class='form-group'>
			<label for='message_textarea'>Message:</label>
			<textarea class='form-control' rows='5' id='message_textarea' name='message_textarea'></textarea>
		</div>

		<button type='submit' class='btn btn-primary'>Submit</button>

	</form>

</div>
";
?>

<?php require_once('../_resources/footer.php');?>