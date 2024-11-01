<?php
/**
 * Plugin Name: Simple Connect & Adverts 
 * Description: This plugin used the Amazon Product API adds some Products to our single posts.
 * Plugin URI: http://www.spieletraeume.de/
 * Author: Spieleträume
 * Author URI: http://www.spieletraeume.de/
 * Version: 1.0
 **/
function sca_options () {
	    $option_name = 'sca';
	    if (!current_user_can('manage_options')) {
	        wp_die( __('You are not authorized to access this page.') );
	    }
 	    if(isset($_POST['sca_api_key']) && isset($_POST['sca_api_secret_key']) && isset($_POST['sca_partner_id']) && isset($_POST['sca_css']) && isset($_POST['sca_html'])	) {
			if(wp_verify_nonce($_REQUEST['nonce_options'], 'sca_options'))       
			{
	        $option = array();
			$allowed_html = shapeSpace_allowed_html();
			if (preg_match("/[\w]+/", $_POST['sca_api_key']) && strlen(trim($_POST['sca_api_key']))>=16 && strlen(trim($_POST['sca_api_key']))<=128)
			{
	        	$option['sca_api_key'] = sanitize_text_field($_POST['sca_api_key']);
			} else {
				$option_msg .= "Die Access Key ID wurde nicht korrekt eingegeben bzw. wurde nicht akzeptiert.<br>";
			}
			if (isset($_POST['sca_api_secret_key']) && strlen(trim($_POST['sca_api_secret_key']))>=1 && strlen(trim($_POST['sca_api_secret_key']))<=128)
			{
	        	$option['sca_api_secret_key'] = sanitize_text_field($_POST['sca_api_secret_key']);
			} else {
				$option_msg .= "No Secret Access Key was entered.<br>";
			}
			if (preg_match("/[\w]+/", trim($_POST['sca_partner_id']))  && strlen(trim($_POST['sca_partner_id']))>=1 && strlen(trim($_POST['sca_partner_id']))<=128)
			{
	        	$option['sca_partner_id'] = sanitize_text_field($_POST['sca_partner_id']);
			} else {
				$option_msg .= "The Access Partner ID was not entered correctly or was not accepted.<br>";
			}
	        if (isset($_POST['sca_css']) == trim($_POST['sca_css']))
			{
				$option['sca_css'] = sanitize_textarea_field($_POST['sca_css']);
			} else {
				$option_msg .= "Your CSS template contains unauthorized HTML tags.<br>";
			}						
			if (wp_kses($_POST['sca_html'], $allowed_html) == stripslashes($_POST['sca_html']))
			{
				$option['sca_html'] = wp_kses($_POST['sca_html'], $allowed_html);
			} else {
				$option_msg .= "Your HTML template contains unauthorized tags.<br>";
			}
	        if (empty($option_msg))
			{
				update_option($option_name, json_encode($option));
				$outputa .= '<div class="updated"><p><strong>'.__('Settings saved.', 'menu' ).'</strong></p></div>';
			} else {
				$outputa .= '<div class="error"><p><strong>'.__($option_msg, 'menu' ).'</strong></p></div>';
			}
 		}else{
         wp_die( __('You are not authorized to access this page.') );
  		}	        
	    }

		$defaults = array('sca_api_key' => 'Your Amazon Access Key ID',
						  'sca_api_secret_key' => 'Your Amazon Secret Access Key', 
						  'sca_partner_id' => 'Your Amazon Partner ID', 
						  'sca_html' => '<div class="sca-row">
<a href="%DetailPageURL%" target="_blank" rel="nofollow">
<div class="sca-col-2">
<img src="%MediumImageUrl%" alt="%Title%">
</div>
<div class="sca-col-8">
<h3>%Title%</h3>
</a>
<p>%EditorialReviews%</p>
<br><span class="sca-right sca-red"><b>ab %LowestNewPrice%</b></span>
<a href="%DetailPageURL%" target="_blank" rel="nofollow">
<button type="button" class="sca-button"><i class="fa fa-amazon"></i> Auf Amazon kaufen</button>
</a>
</div>  
</div><hr>
', 
						  'sca_css' => '
.sca-row {
  position: relative;
  width: 100%;
}
.sca-row [class^="sca-col"] {
  float: left;
  margin: 0.5rem 2%;
  min-height: 0.125rem;
}
.sca-col-1,
.sca-col-2,
.sca-col-3,
.sca-col-4,
.sca-col-5,
.sca-col-6,
.sca-col-7,
.sca-col-8,
.sca-col-9,
.sca-col-10,
.sca-col-11,
.sca-col-12 {
  width: 96%;
}
.sca-row::after {
	content: "";
	display: table;
	clear: both;
}
@media only screen and (min-width: 45em) {  /* 720px */
  .sca-col-1 {
    width: 4.33%;
  }
  .sca-col-2 {
    width: 12.66%;
  }
  .sca-col-3 {
    width: 21%;
  }
  .sca-col-4 {
    width: 29.33%;
  }
  .sca-col-5 {
    width: 37.66%;
  }
  .sca-col-6 {
    width: 46%;
  }
  .sca-col-7 {
    width: 54.33%;
  }
  .sca-col-8 {
    width: 62.66%;
  }
  .sca-col-9 {
    width: 71%;
  }
  .sca-col-10 {
    width: 79.33%;
  }
  .sca-col-11 {
    width: 87.66%;
  }
  .sca-col-12 {
    width: 96%;
  }
}

.sca-right {
  text-align: right;
  float: right;
}
.sca-red {
  color: #d9534f;
}
.sca-button {
    background-color: #ff9900;
    border: none;
    color: #000000;
    padding: 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    border-radius: 4px;
}');	    
		$option = array();
		$option_string = get_option($option_name, false);
		$option = json_decode($option_string, true); 	
		$default = wp_parse_args( $option_string, $defaults );
		$outputa .= '
	    <div class="wrap">
	        <h2>'.__( 'Simple Connect & Adverts Setup', 'menu' ).'</h2>
			<b>used Product Advertsing API from Amazon</b><br><br>
	        <form name="form1" method="post" action="">'.
			wp_nonce_field( 'sca_options', 'nonce_options' ).'
	        <table>
	        <tr><td valign="top"><b>'.__("Api Key", 'menu' ).':</b></td>
	        <td>
	        <input type="text" name="sca_api_key" value="';
			if (empty($option['sca_api_key']))
			{
				$outputa .= stripslashes($default['sca_api_key']);
			} else {
				$outputa .= stripslashes($option['sca_api_key']);				
			}
			$outputa .= '	
			" size="50">
			<p>Your Amazon Access Key ID</p>
	        </td></tr>
	        <tr><td valign="top"><b>'.__("Api Secret Key", 'menu' ).':</b></td>
	        <td>
	        <input type="text" name="sca_api_secret_key" value="';
			if (empty($option['sca_api_secret_key']))
			{
				$outputa .= stripslashes($default['sca_api_secret_key']);
			} else {
				$outputa .= stripslashes($option['sca_api_secret_key']);				
			}
			$outputa .= '
			" size="50">
			<p>Your Amazon Secret Access Key</p>
	        </td></tr>
			<tr><td valign="top"><b>'.__("Partner ID", 'menu' ).':</b></td>
	        <td>
	        <input type="text" name="sca_partner_id" value="';
			if (empty($option['sca_partner_id']))
			{
				$outputa .= stripslashes($default['sca_partner_id']);
			} else {
				$outputa .= stripslashes($option['sca_partner_id']);				
			}
			$outputa .= '
			" size="100">
			<p>Your Amazon Partner ID</p>
	        </td></tr>
			<tr><td valign="top"><b>'.__("Template html", 'menu' ).':</b></td>
	        <td style="padding-bottom:20px;">
	        <textarea name="sca_html" cols="100">';
			if (empty($option['sca_html']))
			{
				$outputa .= stripslashes($default['sca_html']);
			} else {
				$outputa .= stripslashes($option['sca_html']);				
			}
			$html_tags = "<p> <a> <i> <div> <span> <blockquote> <ol> <ul> <li> <img> <br> <h1> - <h6> <button> <hr> <b> <strong>
			<br />";
			$outputa .= '
			</textarea>
			<p><b>You can us the following tags:</b><br>'. htmlspecialchars($html_tags).' <br><b>and placeholders:</b> %Title%, %MediumImageUrl%, %EditorialReviews%, %LowestNewPrice%, DetailPageURL </p>
	        </td></tr>
			<tr><td valign="top"><b>'.__("Template CSS", 'menu' ).':</b></td>
	        <td style="padding-bottom:20px;">
	        <textarea name="sca_css" cols="100">';
			if (empty($option['sca_css']))
			{
				$outputa .= stripslashes($default['sca_css']);
			} else {
				$outputa .= stripslashes($option['sca_css']);				
			}
			$outputa .= '
			</textarea>
			<p>Use your own CSS code here.</p>
	        </td></tr>
	        </table>
	        <hr />
	        <p class="submit">
	            <input type="submit" name="Submit" class="button-primary" value="'.esc_attr('Speichern').'" />
	        </p>
	        </form>
			<h3>Shortcodes</h3>
			<p>Now you can use the following shortcode in your posts:</p>
			<p><b>ItemSearch:</b>
			[sca type="ItemSearch" title="Your own Title" search="Your search tag" category="All"]</p>
			<p><b>TopSellers</b>
			[sca type="TopSellers" title="Your own Title" node="165793011"]</p>
			<p><b>NewReleases</b>
			[sca type="NewReleases" title="Your own Title" node="3375251"]</p>
			<p><b>Some information about the node for TopSellers & NewReleases can be found at</b><br> https://docs.aws.amazon.com/de_de/AWSECommerceService/latest/DG/BrowseNodes.html or browsenodes.com</p>
			
	    </div>';
	    echo $outputa;
	}
function show_adverts($atts) 
{  
  	$option_string = get_option('sca');
  	$option = array();
  	$option = json_decode($option_string, true);
  	define('AWS_API_KEY', $option["sca_api_key"]);
  	define('AWS_API_SECRET_KEY', $option["sca_api_secret_key"]);
  	define('AWS_ASSOCIATE_TAG', $option["sca_partner_id"]);

  	require 'lib/AmazonECS.class.php';
	$template=html_entity_decode(stripslashes($option['sca_html']));
	$return_html="<div style=\"clear: both;\"></div>";
	if (isset($atts['title']))
  	{
	  $return_html=$return_html."<u><b>Werbung:</b></u><h2>".$atts['title']."</h2>";
    } else {
	  $return_html=$return_html."<u><b>Werbung:</b></u><h2>Empfehlungen</h2>";	
	}
  	try
  	{
		$amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'DE', AWS_ASSOCIATE_TAG);
 		$amazonEcs->associateTag(AWS_ASSOCIATE_TAG);
	  	if (!isset($atts['type']))
		{
			return;		// Script beenden
		}
		if (isset($atts['type']) && $atts['type']=="TopSellers" && isset($atts['node']))
		{
			$response = $amazonEcs->responseGroup('BrowseNodeInfo,TopSellers')->browseNodeLookup($atts['node']);
			if (is_array($response->BrowseNodes->BrowseNode->TopSellers->TopSeller))
			{
				foreach ($response->BrowseNodes->BrowseNode->TopSellers->TopSeller as $item)
				{
					$ASIN[]=$item->ASIN;		
				}
				$ASINS = implode(",", $ASIN);	
			}
		}
		if (isset($atts['type']) && $atts['type']=="ItemSearch" && isset($atts['search']) && isset($atts['category']))
		{
			$response = $amazonEcs->category($atts['category'])->responseGroup('Large')->search($atts['search']);
			if (is_array($response->BrowseNodes->BrowseNode->TopSellers->TopSeller))
			{
				foreach ($response->BrowseNodes->BrowseNode->TopSellers->TopSeller as $item)
				{
					$ASIN[]=$item->ASIN;		
				}
				$ASINS = implode(",", $ASIN);	
			}
		}	
		if (isset($atts['type']) && $atts['type']=="SimilarProducts" && isset($atts['asin']))
		{
			//$response = $amazonEcs->responseGroup('Large')->optionalParameters(array('Condition' => 'New'))->lookup($atts['asin']);
			$response = $amazonEcs->responseGroup('Large')->similarityLookup($atts['asin']);
			if (is_array($response->Items->Item->SimilarProducts->SimilarProduct))
			{
				foreach ($response->Items->Item->SimilarProducts->SimilarProduct as $item)
				{
					$ASIN[]=$item->ASIN;
				}
				$ASINS = implode(",", $ASIN);
			}
		}
		if (isset($atts['type']) && $atts['type']=="NewReleases" && isset($atts['node']))
		{
			$response = $amazonEcs->responseGroup('BrowseNodeInfo,NewReleases')->browseNodeLookup($atts['node']);
			if (is_array($response->BrowseNodes->BrowseNode->NewReleases->NewRelease))
			{
				foreach ($response->BrowseNodes->BrowseNode->NewReleases->NewRelease as $item)
				{
					$ASIN[]=$item->ASIN;		
				}
				$ASINS = implode(",", $ASIN);	
			}
		}		
		if (isset($ASINS))
		{
			$response = $amazonEcs->responseGroup('Medium')->optionalParameters(array('Condition' => 'New'))->lookup($ASINS);
		}
    }   	
  	catch(Exception $e)
  	{
    	echo $e->getMessage();
		exit();
  	}	
	if (is_array($response->Items->Item))
	{
		foreach ($response->Items->Item as $item)
		{
			$DetailPageURL=$item->DetailPageURL;		//Height & Width
			$SmallImageUrl=$item->SmallImage->URL;
			$MediumImageUrl=$item->MediumImage->URL;
			$LargeImageUrl=$item->LargeImage->URL;  
			$Title=$ReleaseDate=$item->ItemAttributes->Title;
			$EAN=$item->ItemAttributes->EAN;
			$ProductGroup=$item->ItemAttributes->ProductGroup;
			$ReleaseDate=$item->ItemAttributes->ReleaseDate;
			$LowestNewPrice=$item->OfferSummary->LowestNewPrice->FormattedPrice;
	
			if (is_array($item->EditorialReviews->EditorialReview))
			{
				foreach($item->EditorialReviews->EditorialReview as $review)
				{
					//echo $review->Content;
					$EditorialReviews=$review->Content;
					break;
				}		
			} else {
				$EditorialReviews=$response->Items->Item->EditorialReviews->EditorialReview->Content;		
			}	
			$EditorialReviews = preg_replace("/[^.]*$/", '', substr($EditorialReviews, 0, 400));
			$EditorialReviews = str_replace("<br />"," - ",$EditorialReviews);  
			$EditorialReviews = str_replace("<br>"," - ",$EditorialReviews);  
			$EditorialReviews=strip_tags($EditorialReviews);

			$tmp_html = $template;
			$tmp_html=str_replace("%Title%",$Title,$tmp_html);
			$tmp_html=str_replace("%Title%","",$tmp_html);
			$tmp_html=str_replace("%SmallImageUrl%",$SmallImageUrl,$tmp_html);
			$tmp_html=str_replace("%MediumImageUrl%",$MediumImageUrl,$tmp_html);
			$tmp_html=str_replace("%LargeImageUrl%",$LargeImageUrl,$tmp_html);
			$tmp_html=str_replace("%DetailPageURL",$DetailPageURL,$tmp_html);
			$tmp_html=str_replace("%EditorialReviews%",$EditorialReviews,$tmp_html);
			$tmp_html=str_replace("%LowestNewPrice%",$LowestNewPrice,$tmp_html);
			$return_html=$return_html.$tmp_html;
		}
	}
	$return_html=$return_html."<br>";	  
	return $return_html;	
}
// Wordpress Menu aktivieren

add_action('admin_menu', 'sca_menu');
	function sca_menu() {
	    add_options_page('Simple Connect & Adverts Setup', 'Simple Connect & Adverts', 'manage_options', 'simple-connect-adverts', 'sca_options');
	}

// Ausgabe steuern

$option_string = get_option('sca');
$option = array();
$option = json_decode($option_string, true);
add_action('template_redirect','sca_check_is_page');
function sca_check_is_page(){
	if (is_single())
	{
		add_shortcode('sca', 'show_adverts');
	}
}
function sca_wp_styles() {
    wp_register_style( 'font-awesome', plugins_url( 'css/fontawesome-all.min.css', __FILE__ ) );
    wp_enqueue_style( 'font-awesome' );
}
add_action( 'wp_enqueue_scripts', 'sca_wp_styles' ); 
function sca_add_inline_css() {
  	$option_string = get_option('sca');
  	$option = array();
  	$option = json_decode($option_string, true);	
	wp_enqueue_style( 'scacss', plugins_url('css/sca.css', __FILE__) );
        $sca_custom_css = html_entity_decode(stripslashes($option['sca_css']));
  wp_add_inline_style( 'scacss', $sca_custom_css ); 
}
add_action( 'wp_enqueue_scripts', 'sca_add_inline_css' ); 
function shapeSpace_allowed_html() {

	$allowed_tags = array(
		'a' => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
			'target' => array(),
		),
		'b' => array(),
		'blockquote' => array(
			'cite'  => array(),
		),		
		'button'  => array(
			'type' => array(),
			'class' => array(),
		),
		'div' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'h1' => array(
			'class' => array(),
		),
		'h2' => array(
			'class' => array(),
		),
		'h3' => array(
			'class' => array(),
		),
		'h4' => array(
			'class' => array(),
		),
		'h5' => array(
			'class' => array(),
		),
		'h6' => array(
			'class' => array(),
		),
		'i' => array(
			'class'  => array(),
		),
		'img' => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'li' => array(
			'class' => array(),
		),
		'ol' => array(
			'class' => array(),
		),
		'p' => array(
			'class' => array(),
		),
		'span' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'strong' => array(),
		'hr' => array(),
		'br' => array(),
		'ul' => array(
			'class' => array(),
		),
	);
	
	return $allowed_tags;
}
?>