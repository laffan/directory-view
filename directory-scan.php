<?php

$response = scan($dir);

// This function scans the files folder recursively, and builds a large array
function scan($dir){
	$files = array();

	// Is there actually such a folder/file?
	if(file_exists($dir)){
		foreach(scandir($dir) as $f) {
			if(!$f || $f == 'index.php' || $f[0] == '.') {
				continue; // Ignore hidden files
			}
			if(is_dir($dir . '/' . $f)) {
				// The path is a folder

				$files[] = array(
					"name" => $f,
					"type" => "folder",
					"path" => $dir . '/' . $f,
					"items" => scan($dir . '/' . $f) // Recursively get the contents of the folder
				);
			}

			else {
				// It is a file

				$filesize = filesize($dir . '/' . $f); // bytes
				$filesize = round($filesize / 1024 / 1024, 1); // megabytes with 1 digit

				$files[] = array(
					"name" => $f,
					"type" => "file",
					"path" => $dir . '/' . $f,
					"size" => $filesize // Gets the size of this file
				);
			}
		}
	}
	return $files;
}

$directoryAsJSON =  json_encode(array(
	"name" => "files",
	"type" => "folder",
	"path" => $dir,
	"items" => $response
));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon">

    <title>🥌</title>

		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

		<style media="screen">
			body, html {
				background: SlateGray ;
			}
			body > div {
				width: 90%;
				max-width: 400px;
				margin: 50px auto;
				padding: 30px;
				background: white;
				box-shadow: 0px 0px 10px rgba(0,0,0,.5);
				border-radius: 5px;
			}
			ul { padding: 0; margin: 0; }
			li { list-style: none; margin: 15px 0; }
			a {
				background: #eeeeee;
				border-radius: 5px;
				padding: 15px;
				display: block;
				text-decoration: none;
				color: black;
				font-family: 'Montserrat', sans-serif;
				font-size: 14px;
				border: 1px solid transparent;
			}
			a:hover{
				border: 1px solid DodgerBlue;
				color: DodgerBlue;
			}
			a span {
				float: right ;
			}
			li a::before {
				font-family: "Font Awesome 5 Free"; font-weight: 900;
				width: 30px;
				font-size: 18px;
				display: inline-block;
			}
			li[data-type="file"] a::before {
				 content: "\f56d";
			}
			li[data-type="folder"] a::before {
				content: "\f07b";
			}
		</style>

</head>
<body>

	<script id="template-filelist" type="text/x-mustache-template">
		<ul>
			{{ #items }}
			<li data-type="{{ type }}"><a href="{{ name }}">{{name}} {{ #size }} <span> {{ size }} MB </span>{{ /size }}</a></li>
			{{ /items }}
		</ul>
  </script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/3.0.1/mustache.min.js"></script>
	<script> var directoryAsJSON = `<?php echo $directoryAsJSON; ?>`; </script>
	<script>
		var content = document.createElement("div");
		let template = document.getElementById("template-filelist").innerHTML;
		content.innerHTML = Mustache.to_html(template, JSON.parse( `<?php echo $directoryAsJSON; ?>`));
		document.body.appendChild(content);
	</script>

</body>
</html>
