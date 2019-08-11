<!DOCTYPE html>
<html>
<head>
	<title>Test</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script>
	window.onload = function () {
		var form =  document.getElementsByTagName('form')[0],
			selects = form.getElementsByTagName('select'),
			i = 0,
			selects_count = selects.length;
		for (; i < selects_count; i++) {
			selects[i].addEventListener('change', function (e) {
				window.location.hash = e.target.id;
				form.submit();
			});
		}
	}
</script>
</head>

<body>
<form name="Form1" method="post">
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<select name="Select1" id="test1">
		<option>-Select-</option> 
		<option >Yes</option>
		<option >No</option>>
	</select>
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<select name="Select2" id="test2">
		<option>-Select-</option> 
		<option >Yes</option>
		<option >No</option>>
	</select>
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
	<select name="Select2" id="test3">
		<option>-Select-</option> 
		<option >Yes</option>
		<option >No</option>>
	</select>	
</form>

</body>
</html>