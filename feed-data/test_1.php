<?php
	include_once('../connect_36109638.php');
	include_once('../JOTR_PHP_API.php');
?>


<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Test 1 Getting Started</title>
  <meta name="description" content="The HTML5 Herald">
  <meta name="author" content="SitePoint">

  <link rel="stylesheet" href="css/styles.css?v=1.0">

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->
</head>

<body>
	
    <div id="app"></div>

	<script src="../js/node_modules/react/react.js"></script>
	<script src="../js/node_modules/jsx-transform/browserify.js"></script>
    
    <script type="text/jsx">
		/** @jsx React.DOM */
		
		var MessageBox = React.createClass({
			render: function(){
				return (
					<div>
						<h1>Hello, World</h1>
					</div>
				);	
			}
		});
		
		React.renderComponant(
			<MessageBox />,
			document.getElementById('app'),
			function (){
			
			}
		)
		
	</script>
</body>
</html>


<?
	include_once('../connect_close_36109638.php');
?>