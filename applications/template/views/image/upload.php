<html>
  <head>
    <link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print">	
    <!--[if lt IE 8]><link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    <link rel="stylesheet" href="css/image/upload.css" type="text/css">
  </head>
 
  <body>
    <form name="formImageUpload" method="post" enctype="multipart/form-data">
      <div class="vertSpacer"></div>
      <div id="imageUploadBox">
	<div>
	  <input name="userfile[]" type="file">
	</div>
        <div><input type="submit" name="submit" value="Upload"></div>
      </div>
    </form>
  </body>
  
</html>
