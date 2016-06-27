<!DOCTYPE html>
<html>
<body>

<h1>Separating parts of a CNN article page.</h1>

<?php
class pageparser {
		var $url;
		
		//Constructor sets URL of CNN article page
		function __construct($cnn_url) {		
			$this->url = $cnn_url;		
		}		
 
		//Parses the article, separates title, story text and picture and prints them
		function parse() {
		 	 //Read file, convert to HTML, create DOM and XPath objects
			 $lines_string=file_get_contents($this->url);
			 $dom = new DOMDocument();
			 libxml_use_internal_errors(true);
			 $dom->loadHTML($lines_string);
			 libxml_use_internal_errors(false);
			 $xpath = new DOMXpath($dom);
			 
			 //Read title tage and print it
			 try
			 {
				$titletags = $dom->getElementsByTagName('title');
				foreach($titletags as $titletag)
				{
					echo $titletag->nodeValue;
				}
			 }
			 catch(Exception $e)
			 {
				echo 'Error encountered while processing title.';
			 }
			 
			 //Find meta tag(s) with property attribute og:image
			 //These tags should contain image(s) and/or video(s)
			 //Print the image as an HTML <img> tag
			 //More testing is needed here to print different formats of articles with multiple images and videos
			 $metatagsimg = $xpath->query("*/meta[@property='og:image']");
			 foreach($metatagsimg as $metaimg)
			 {
			 	$imgPath = $metaimg->getAttribute('content');
				echo '<p><p><img src="';
				echo $imgPath;
				echo '"></p></p>';
			 }
			 
			 try
			 {
				//Find p tag with class zn-body__paragraph and print its text content
				//This tag contains the location the article was published and the first para
				$articleptags = $dom->getElementsByTagName('p');
				foreach($articleptags as $articleptag)
				{
					$classAttr = $articleptag->getAttribute('class');
					if($classAttr == 'zn-body__paragraph')
					{
						$articlePara = $articleptag->textContent;
						echo $articlePara;
					}
				}
			 }
			 catch(Exception $e)
			 {
			 	echo 'Error encountered while processing first paragraph of article text.';
			 }
			 
			 //Find div tags with the class zn-body__paragraph and print their text
			 //These tags contain the remaining paras of the article
			 try
			 {
				$articlebodytags = $dom->getElementsByTagName('div');
				foreach($articlebodytags as $articlebodytag)
				{
					$classAttr = $articlebodytag->getAttribute('class');
					if($classAttr == 'zn-body__paragraph')
					{
						$articlePara = $articlebodytag->textContent;
						echo $articlePara;
					}
				}
			 }
			 catch(Exception $e)
			 {
				echo 'Error encountered 2nd and subsequent paragraphs of the article.';
			 }
		}	
	}	 

//Create parser object by passing article URL to constructor
$pp = new pageparser("http://www.cnn.com/2016/06/23/politics/iwo-jima-photo-flag-raiser-marine-john-bradley-harold-schultz/?iid=ob_homepage_deskrecommended_pool");

//Call parse function
$pp->parse();

?> 

</body>
</html>