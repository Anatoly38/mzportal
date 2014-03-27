<?php
require_once("svg_buid.php");
$gr_template = "gr.svg"
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Пример обработки svg</title>
</head>

<body>
<?php
$gr = new Svg_Buider($gr_template);

?>
</body>
</html>