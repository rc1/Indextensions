<?php if (!defined('SITE')) exit('Boom! No direct script access allowed');

/** 
* RossCairns.com indexhibit extensions
* @copyright RossCairns.com 
* @author Ross Cairns 
* @version v0.3
* @package com.rosscairns.indextensions
*
**/

/** @name Index Menu Styles 
* Styles for formating the website index <plug:rcPageIndex style />
* @see rcPageIndex
*/
//@{
define ('rcSTYLE_NO_SECTIONS_TITLES_DASH_SPLIT', 				'rcSTYLE_NO_SECTIONS_TITLES_DASH_SPLIT');
define ('rcSTYLE_NO_SECTIONS_TITLES_DASH_EMSPLIT_ON_MINUS', 	'rcSTYLE_NO_SECTIONS_TITLES_DASH_EMSPLIT_ON_MINUS');
define ('rcSTYLE_NO_SECTIONS_TITLES_DASH_EMSPLIT_ON_MINUS_SHOW_UNPUBLISHED', 	'rcSTYLE_NO_SECTIONS_TITLES_DASH_EMSPLIT_ON_MINUS_SHOW_UNPUBLISHED');
define ('rcSTYLE_INVISIBLE_TITLES', 							'rcSTYLE_INVISIBLE_TITLES');
define ('rcSTYLE_INDEXHIBIT_SECTIONAL',							'rcSTYLE_INDEXHIBIT_SECTIONAL');
define ('rcSTYLE_NO_MENU',										'rcSTYLE_NO_MENU');
//@}

/** @name Menu Section Selection  
 *  Defines whether supplied section title should be included or excluded from a menu
 *	Example usages: Splitting menu's into two columns.
*/
//@{
define ('rcINCLUDE', 	'rcINCLUDE_ONLY');
define ('rcEXCLUDE', 	'rcEXCLUDE');
//@}


/*****************************************
 * @name Theme Plugins
 * Plugins to be used in Theme Templates
 * @{
*****************************************/ 

/**
*	Runs indexhibits plugins parser on the supplied text
*	Allows plugins to be run in custom exhbits. Useful for setting global variables from with the textarea of an exhiibit.
*	@note Plugin's may run twice if they will later be processed. E.g. if they are within the textarea of an exhiibit.
*/
function rcRunPluginsParser($text) {
	$PARSER =& load_class('parse', TRUE, 'lib');
	$PARSER->parser($text);
}

/** 
* Returns the index menu in a chosen style.
* Has the optional to include only or exlude specificed section titles but adding them to the $sectionTitlesArray. by default titles in here will be included. this allows for different menus to be created by still allowing one menu to be dynamic.
* Usage: <plug:rcPageIndex rcSTYLE_INDEXHIBIT_SECTIONAL />
* @param 	string 	$style 					The style to format the menu 
* @param	string	$sectionTitles			Optional: Menu titles (seperated by | ) to be used to either include or exclude selections. If "" will ignore everything and include everything as normal. @see rcPageIndex
* @param	string	$selectionMode			Optional: Either rcINCLUDE_ONLY or rcEXCLUDE	
* @note		usage <plug:rcPageIndex rcSTYLE_INDEXHIBIT_SECTIONAL,Information|pizza,rcEXCLUDE />
*/
function rcPageIndex($style = rcSTYLE_NO_SECTIONS_TITLES_DASH_SPLIT, $sectionTitles = "", $selectionMode = rcEXCLUDE) {
	
	$sectionTitlesArray = explode("|", $sectionTitles);

	// format based on style
	switch($style) {
		case rcSTYLE_NO_SECTIONS_TITLES_DASH_EMSPLIT_ON_MINUS_SHOW_UNPUBLISHED:
			// format each page title to have emphasis
			$index = _getNavigationFromDB($sectionTitlesArray, $selectionMode, true);
			foreach ($index as &$section) {
				foreach($section as &$page) {
					$page['title'] = rcEmphasisSplit($page['title']);
				}
			}
			$html = _menuStyle_SectionTitleReplacedBy($index, '&mdash;', false);
		break;
		case rcSTYLE_NO_SECTIONS_TITLES_DASH_EMSPLIT_ON_MINUS:
			// format each page title to have emphasis
			$index = _getNavigationFromDB($sectionTitlesArray, $selectionMode);
			foreach ($index as &$section) {
				foreach($section as &$page) {
					$page['title'] = rcEmphasisSplit($page['title']);
				}
			}
			$html = _menuStyle_SectionTitleReplacedBy($index, '&mdash;', false);
		break;
		case rcSTYLE_NO_SECTIONS_TITLES_DASH_SPLIT:
			$html = _menuStyle_SectionTitleReplacedBy(_getNavigationFromDB($sectionTitlesArray, $selectionMode), '&mdash;', false);
		break;
		case rcSTYLE_INVISIBLE_TITLES:
			$html = _menuStyle_SectionTitleReplacedBy(_getNavigationFromDB($sectionTitlesArray, $selectionMode), '');
		break;
		case rcSTYLE_INDEXHIBIT_SECTIONAL:
			$html = _menuStyle_Indexhibit_Sectional(_getNavigationFromDB($sectionTitlesArray, $selectionMode));
		break;
		case rcSTYLE_NO_MENU:
			$html = "";
		break;
	}
	
	return $html;
}

/** 
* Adds noscriptcontent 
* Usage: <plug:rcCSSIncludes />
* @note css files can be added to plug using rcCSSIncludes_Add() helper function
*/
function rcNoScript() {
	// get our instance
	$OBJ =& get_instance();
	
	if (!isset($OBJ->rcNoScript)) return;
	
	$incs = "<noscript>";
	foreach ($OBJ->rcNoScript as $content) {
		$incs .= $content.'
';
	} 
	$incs .= "</noscript>";
	return $incs;
}

/** 
* Adds links to css files
* Usage: <plug:rcCSSIncludes />
* @note css files can be added to plug using rcCSSIncludes_Add() helper function
*/
function rcCSSIncludes() {
	// get our instance
	$OBJ =& get_instance();
	
	if (!isset($OBJ->rcCSSIncludes)) return;
	
	$incs = "";
	foreach ($OBJ->rcCSSIncludes as $links) {
		$incs .= $links.'
';
	} 
	
	return $incs;
}


/** 
* Adds links to js files
* Usage: <plug:rcJSIncludes />
* @note js files can be added to plug using rcJSIncludes_Add() helper function
*/
function rcJSIncludes() {
	// get our instance
	$OBJ =& get_instance();
	
	if (!isset($OBJ->rcJSIncludes)) return;
	
	$incs = "";
	foreach ($OBJ->rcJSIncludes as $links) {
		$incs .= $links.'
';
	} 
	
	return $incs;
}

/** 
* Adds JS to the Document Ready JS in the HTML head
* Usage: <plug:rcJSDocReady />
* @note js can be added to plug using rcJSDocReady_Add() helper function
*/
function rcJSDocReady() {
	// get our instance
	$OBJ =& get_instance();
	
	if (!isset($OBJ->rcJSDocReady)) return;
	
	$incs = "";
	foreach ($OBJ->rcJSDocReady as $js) {
		$incs .= $js.'
';
	} 
	
	return $incs;
}

/** 
* Strings HTML tags from a string
* when used in a template with system template var it can be used below (don't use the closing >)
* <plug:rcStripHTML <%title%>
* @param 	string 	str 	String to strip
*/
function rcStripHTML($str) {
	return strip_tags($str);
}

//@}

/******************************************
 * @name Exhibit Textarea Plugins
 * Plugins that are for using in the textarea
 * of an exhibit in the indexhibit admin area
 * @{
*******************************************/

/** 
* Disables Indexhibit so nothing is shown
* @param 	string 	shouldDo 	TRUE or YES will disable the page
* @notes	can be used in an exhibits textarea to disable a website. For example place in the contexts page textarea to disable the website which content changes are happening 
*/
function rcDisablePage($shouldDo) {
	if(strncasecmp ($shouldDo, "t", 1) == 0 || strncasecmp ($shouldDo, "y", 1) == 0) exit;
}

/** 
* An <span> html element with the hight of a number of lines
* @param 	int 	numberOfLines 	Number of the span shall span
* @param 	int 	lineHeight 		Height of each line in pixels
* @param 	int 	paddingBottom 	Padding at the bottom of the span
*/
function rcSpacerBox($numberOfLines, $lineHeight = 15, $paddingBottom = 0) {
	$spanHeight = $lineHeight * $numberOfLines;
	$spanHeight += $paddingBottom;
	
	return "<span style='height: $spanHeight"."px; width:100%; display:block'> </span>";
}

// @}

/******************************************
 * @name Anywhere Plugins
 * Plugins that should work anywhere. 
 * e.g. in exhibition Formats 
 * @{
*******************************************/

/** 
* Splits a string on a character and add emphasise to the second part
* e.g. 	"The Client - The Project"
*		will return
*		"The Client <em>The Project</em>"
* can be used to use emphasis on project titles
* the split character will be used in the url
*
* @param 	string 	text 		text to be split and emphasised
* @param 	string 	split 		the character to split the text on
*/

function rcEmphasisSplit($text, $split = "-") {
	//do the split
	$newText = preg_replace("[$split]", "<em>", $text, 1);
	
	if (strlen($newText) == strlen($text))
		return $text;
	else 
		return $newText."</em>";
}

/**
*	Display the page title, using rcEmphasisSplit
*	In theme templates plugin seems to work when passing varibles with commsa
*	@see rcEmphasisSplit
*/
function rcPageTitleWithEmphasisSplit() {
	global $rs;
	return rcEmphasisSplit($rs['title'], "-");
}

/** 
* Helper function to add CSS files to the rcCSSIncludes plugin
*
* @param 	string 	dir 		Directory to load the css file from e.g. /web/css/
* @param	string	filename	CSS file to load e.g. reset.css
* @param	string	mediaOrNil	optional CSS media. Screen etc.
* @param	string	typeOrNil	optional CSS type. text/css etc.
* 
* @note this can be used to include difference css files from a exhibit type
*/
function rcCSSIncludes_Add($dir, $filename, $mediaOrNil = "", $typeOrNil = "") {
	// get our instance
	$OBJ =& get_instance();
	
	// see if the rcCSSIncludes array has been made
	if (!isset($OBJ->rcCSSIncludes)) {
		$OBJ->rcCSSIncludes = array();
	}
	// create our include html tag
	if ($mediaOrNil != "") $mediaOrNil = "media='$mediaOrNil'";
	$typeOrNil = $typeOrNil == "" ?  "type='text/css'"  : "type='$typeOrNil'";
	
	$cssIncludeString = "<link rel='stylesheet' href='$dir$filename' $mediaOrNil $typeOrNil />";
	
	array_push($OBJ->rcCSSIncludes, $cssIncludeString);
}

/** 
* Helper function to add JS files to the rcJSIncludes plugin
* @param 	string 	dir 		Directory to load the js file from e.g. /web/js/
* @param	string	filename	JS file to load e.g. reset.js
*/
function rcJSIncludes_Add($dir, $filename) {
	// get our instance
	$OBJ =& get_instance();
	
	// see if the rcJSIncludes array has been made
	if (!isset($OBJ->rcJSIncludes)) {
		$OBJ->rcJSIncludes = array();
	}
	// create our include html tag

	$jsIncludeString = "<script type='text/javascript' src='$dir$filename'></script>";
	
	array_push($OBJ->rcJSIncludes, $jsIncludeString);
}

/** 
* Helper function to add Document Ready JS to the rcJSDocReady plugin
* @param	string	js			the js
*/
function rcJSDocReady_Add($js) {
	// get our instance
	$OBJ =& get_instance();
	
	// see if the rcJSDocReady array has been made
	if (!isset($OBJ->rcJSDocReady)) {
		$OBJ->rcJSDocReady = array();
	}
	
	array_push($OBJ->rcJSDocReady, $js);
}

/** 
* Helper function to add content that will be all contained within a <noscript> tag
* @param	string	content			the content to be placed 
*/
function rcNoScript_Add($content) {
	// get our instance
	$OBJ =& get_instance();
	
	// see if the rcJSDocReady array has been made
	if (!isset($OBJ->rcNoScript)) {
		$OBJ->rcNoScript = array();
	}
	
	array_push($OBJ->rcNoScript, $content);
}

//@}



/******************************************
 * @name Private Functions
 * @{
*******************************************/

/** 
* _menuStyle_SectionTitleReplacedBy
* @param 			items					array from the db (_getNavigationFromDB)
* @param 	string 	replacement				Section title will display this string instead of the section title
* @param 	bool 	firstSelectionTitle 	if False will skip using the first title
*/
function _menuStyle_SectionTitleReplacedBy($items, $replacement = "", $firstSelectionTitle = true) {
	
	$firstSelectionTitle = ($firstSelectionTitle == true) ? 1 : 0;
	
	$s = "";
	foreach($items as $key => $out)
	{
		$s .= "<ul>\n";
		if ($firstSelectionTitle > 0) {
			if ($out[0]['disp'] == 1) $s .= "<li class='section-title'>$replacement</li>\n";
		}
		$firstSelectionTitle++;
		$s = _makePagesIntoMenuListItems($out, $s);
		$s .= "</ul>\n\n";
	}
	return $s;
}
 
function _menuStyle_Indexhibit_Sectional($items) {
	$s = "";
	foreach($items as $key => $out)
	{
		$s .= "<ul>\n";
		if ($out[0]['disp'] == 1) $s .= "<li class='section-title'>" . $key . "</li>\n";
		$s = _makePagesIntoMenuListItems($out, $s);
		$s .= "</ul>\n\n";
	}
	return $s;
}

function _makePagesIntoMenuListItems($out, $s) {
		foreach($out as $page)
		{
			$active = ($rs['id'] == $page['id']) ? " class='active'" : '';
			if (isset($page['status']) && $page['status'] == 0) 
				$s .= "<li class='unpublished'>" . $page['title'] . "</li>\n";
			else
				$s .= "<li$active><a href='" . BASEURL . ndxz_rewriter($page['url']) . "' >" . $page['title'] . "</a></li>\n";
		}
		
		return $s;
}

/** 
* _getNavigationFromDB
* Pulls the navigation out of the DB
* @param 	bool	showUnpublished			also fetch unpublished results
* @param	array	$sectionTitlesArray		Optional: Menu titles to be used to either include or exclude selections. 
*											If "" will ignore everything and include everything as normal. @see rcPageIndex
* @param	string	$selectionMode			Optional: Either rcINCLUDE or rcEXCLUDE	
*/
function _getNavigationFromDB($sectionTitlesArray = array(), $selectionMode = rcEXCLUDE, $showUnpublished = false) {

	$OBJ =& get_instance();
	global $rs;
	
	// pages with a status of 0 are unpublished
	$status = ($showUnpublished) ? "" : " status = '1' AND" ;

	$pages = $OBJ->db->fetchArray("SELECT id, title, url, 
		section, sec_desc, sec_disp, year, secid, status   
		FROM ".PX."objects, ".PX."sections WHERE
		$status
		hidden != '1' 
		AND section_id = secid  
		ORDER BY sec_ord ASC, ord ASC");
		
	if (!$pages) return array('Error Fetching Navigation from DB');
	
	foreach($pages as $reord)
	{
		// figure out if we should include or exclude this item
		$includeItem = ($selectionMode == rcEXCLUDE) ? true : false; 
		foreach($sectionTitlesArray as &$title) {
			if ($reord['sec_desc'] == $title) {
				$includeItem = !$includeItem;
			}
		}
		
		if ($includeItem == true) {
			$order[$reord['sec_desc']][] = array(
				'id' => $reord['id'],
				'title' => $reord['title'],
				'url' => $reord['url'],
				'year' => $reord['year'],
				'secid' => $reord['secid'],
				'disp' => $reord['sec_disp'],
				'status' => $reord['status']);
		}
	}
	
	return $order;
}

//@}
 
