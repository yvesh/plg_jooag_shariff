<?php
/**
 * @package 	JooAg Shariff
 * @version 	3.x.0 Alpha
 * @for 	Joomla 3.3+ 
 * @author 	Joomla Agentur - http://www.joomla-agentur.de
 * @copyright 	Copyright (c) 2009 - 2015 Joomla-Agentur All rights reserved.
 * @license 	GNU General Public License version 2 or later;
 * @description A small Plugin to share Social Links!
 */
defined('_JEXEC') or die;

class PlgContentJooag_shariff extends JPlugin
{	
	public function __construct(&$subject, $config){
		$view = JFactory::getApplication()->input->getWord('view');
		if($view != 'article'){
			return;
		}
		parent::__construct($subject, $config);
	}
	
	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0){
		$output = $this->getOutput('0');
		return $output;
	}
	
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0){
		$output = $this->getOutput('1');
		return $output;
	}
	
	public function getOutput($position){
		$setCatId = $this->params->get('showbycategory');
		$currentCatId = JFactory::getApplication()->input->getInt('catid');
		$output = '';
		if($this->params->get('position') == $position and ((is_array($setCatId) && in_array($currentCatId,$setCatId)) or empty($setCatId))){
			$doc = JFactory::getDocument();
			$lang = JFactory::getLanguage();
			$lang = explode("-", $lang->getTag());
			JHtml::_('jquery.framework');
			$doc->addScript(JURI::root().'plugins/content/jooag_shariff/shariff.min.js');
			$doc->addStyleSheet(JURI::root().'plugins/content/jooag_shariff/shariff.min.css');
			$services = implode("&quot;,&quot;", $this->params->get('services'));
			$services = '&quot;'.$services.'&quot;';
			$output = '<div data-theme="'.$this->params->get('theme').'" data-lang="'.$lang[0].'"  data-orientation="'.$this->params->get('orientation').'" data-url="'.JURI::current().'" data-referrer-track="null" data-info-url="'.$this->params->get('info').'" data-services="['.$services.']" data-backend-url="/plugins/content/jooag_shariff/backend" class="shariff"></div>';
			$output .= '<script src="plugins/content/jooag_shariff/shariff.min.js"></script>';
		}
		return $output;
	}
}
