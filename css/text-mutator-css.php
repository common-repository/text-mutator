<?php
//prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
header( "Content-type: text/css; charset: UTF-8" );

/*set defaults*/
		
$textbgd = 'inherit';
$textcolor = 'inherit';
$background_img_url = '';
$selectcolor = 'inherit';
$selectbgdcolor = 'inherit';
$linkcolor = 'inherit';
$linkhovercolor = 'inherit';

if($this->options['txt_color']) $textcolor = $this->options['txt_color'];
if($this->options['select_color']) $selectcolor = $this->options['select_color'];
if($this->options['select_bgd_color']) $selectbgdcolor = $this->options['select_bgd_color'];
if($this->options['link_color']) $linkcolor = $this->options['link_color'];
if($this->options['link_hover_color']) $linkhovercolor = $this->options['link_hover_color'];
if($this->options['background']) $textbgd = $this->options['background'];
if($this->options['background_img'] == 'marker') $background_img_url = plugins_url( 'images/marker-brush.svg', dirname(__FILE__) );
if($this->options['background_img'] == 'highlighter') $background_img_url = plugins_url( 'images/yellow-highlighter-brush.svg', dirname(__FILE__) );
if($this->options['background_img'] == 'mutate_black') $background_img_url = plugins_url( 'images/mutate-black.gif', dirname(__FILE__) ); 
if($this->options['background_img'] == 'mutate_white') $background_img_url = plugins_url( 'images/mutate-white.gif', dirname(__FILE__) );

?>

.text-mutator { 
	background-repeat:repeat-x;
	background-color:<?php echo $textbgd; ?>;
	background-image:url('<?php echo $background_img_url; ?>');
	background-size:contain;
}
.text-mutator.strike1 {
	text-decoration: #000 line-through;
}
.text-mutator.strike2 {
	text-decoration: #000 double line-through;
}
.text-mutator-text::selection {
	color:<?php echo $selectcolor; ?>;
	background:<?php echo $selectbgdcolor; ?>;
}
.text-mutator-text::-moz-selection {
	color:<?php echo $selectcolor; ?>;
	background:<?php echo $selectbgdcolor; ?>;
}
.text-mutator-text {
	color:<?php echo $textcolor;?>;
}
.text-mutator-text a {
	color:<?php echo $linkcolor;?>;
}
.text-mutator-text a:hover {
	color:<?php echo $linkhovercolor;?>;
}
.text-mutator-text a::selection {
	color:<?php echo $selectcolor; ?>;
	background:<?php echo $selectbgdcolor; ?>;
}
.text-mutator-text a::-moz-selection {
	color:<?php echo $selectcolor; ?>;
	background:<?php echo $selectbgdcolor; ?>;
}