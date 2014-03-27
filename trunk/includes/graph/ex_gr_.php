<?php

require_once("includes/graph/svg_build.php");
$gr_template = "includes/graph/gr.svg";
$gr = new Svg_Buider($gr_template);
$gr->render();
echo $gr->image;

?>
