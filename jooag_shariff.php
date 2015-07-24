<?php
/**
 * @package    JooAg_Shariff
 *
 * @author     Joomla Agentur <info@joomla-agentur.de>
 * @copyright  Copyright (c) 2009 - 2015 Joomla-Agentur All rights reserved.
 * @license    GNU General Public License version 2 or later;
 * @description A small Plugin to share Social Links without compromising their privacy!
 **/
defined('_JEXEC') or die;

/**
 * Class PlgContentJooag_Shariff
 *
 * @since  1.0.0
 **/
class plgSystemJooag_Shariff extends JPlugin
{	
	/**
	 * renders the buttons before the article
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   mixed    &$article  An object with a "text" property
	 * @param   mixed    &$params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page      Optional page number. Unused. Defaults to zero.
	 *
	 * @return  string
	 **/	
	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
		if($context == 'com_content.article' and $this->params->get('position') == '1')
		{
			$output = $this->getOutputPosition($article);
			
			return $output;
		}
	}

	/**
	 * renders the buttons after the article
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   mixed    &$article  An object with a "text" property
	 * @param   mixed    &$params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page      Optional page number. Unused. Defaults to zero.
	 *
	 * @return  string
	 **/
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
	{
		if($context == 'com_content.article' and $this->params->get('position') == '2')
		{
			$output = $this->getOutputPosition($article);
			
			return $output;
		}
	}
	
	/**
	 * place shariff in your aticles and modules via {shariff} experimental
	 **/
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{	
		if($context == 'mod_custom.content' and JString::strpos( $article->text, '{shariff}' )  !== false and $this->params->get('position') == '3')
		{
			$article->text = preg_replace( "#{shariff}#s", $this->getOutputPosition($article), $article->text );	
		}
	} 
	
	/**
	 * appends the required scripts to the documents and returns the markup
	 *
	 * @param   mixed    &$article  An object with a "text" property
	 *
	 * @return string
	 **/
	public function getOutputPosition($article)
	{
		$catCount = '0';
		$menuCount = '0';
		$catIds = $this->params->get('showbycategory');
		$menuIds = $this->params->get('showbymenu');
		$app = JFactory::getApplication();
		$actualMenuId = $app->getMenu()->getActive()->id;
		
		if ($this->params->get('position') != '3'){
			if ((is_array($catIds) AND in_array($article->catid, $catIds)) AND $this->params->get('wheretoshow') == '2')
			{
				$catCount = '1';
			}
			
			if ((is_array($catIds) AND !in_array($article->catid, $catIds)) AND $this->params->get('wheretoshow') == '3')
			{	
				$catCount = '1';
			}
			
			if ((is_array($menuIds) AND in_array($actualMenuId, $menuIds)) AND $this->params->get('wheretoshow') == '2')
			{
				$menuCount = '1';
			}
			
			if ((is_array($menuIds) AND !in_array($actualMenuId, $menuIds)) AND $this->params->get('wheretoshow') == '3')
			{	
				$menuCount = '1';
			}
		}
		
		if($catCount == '1' or $menuCount =='1' or $this->params->get('wheretoshow') == '1' or $this->params->get('position') == '3')
		{
			$output = $this->getOutput();
			
			return $output;
		}
	}

	/**
	 * Shariff output generation
	 **/
	public function getOutput()
	{
		$doc = JFactory::getDocument();
		JHtml::_('jquery.framework');
		$doc->addStyleSheet(JURI::root() . 'plugins/system/jooag_shariff/assets/css/'.$this->params->get('shariffcss'));
		$doc->addScript(JURI::root() . 'plugins/system/jooag_shariff/assets/js/'.$this->params->get('shariffjs'));
		$doc->addScriptDeclaration( 'jQuery(document).ready(function() {var buttonsContainer = jQuery(".shariff");new Shariff(buttonsContainer);});' );
		
		//Cache Folder
		jimport('joomla.filesystem.folder');
		if(!JFolder::exists(JPATH_SITE.'/cache/plg_jooag_shariff') and $this->params->get('data-backend-url')){
			JFolder::create(JPATH_SITE.'/cache/plg_jooag_shariff', 0755);
		}
		
		$html  = '<div class="shariff"';
		$html .= ($this->params->get('data-backend-url')) ? ' data-backend-url="/plugins/system/jooag_shariff/backend/"' : '';
		$html .= ' data-lang="'.explode("-", JFactory::getLanguage()->getTag())[0].'"';
		$html .= ($this->params->get('data-mail-url')) ? ' data-mail-url="mailto:'.$this->params->get('data-mail-url').'"' : '';
		$html .= ' data-orientation="'.$this->params->get('data-orientation').'"';	
		$html .= ' data-services='.json_encode(array_map('strtolower', json_decode($this->params->get('data-services'))->services));
		$html .= ' data-theme="'.$this->params->get('data-theme').'"';
		$html .= ' data-url="'.JURI::getInstance()->toString().'"';
		if ( ($id = (int) $this->params->get('data-info-url')) )
		{
			jimport( 'joomla.database.table' );
			$item =	JTable::getInstance("content");
			$item->load($this->params->get('data-info-url'));
			require_once JPATH_SITE . '/components/com_content/helpers/route.php';
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language));
			$html .= ' data-info-url="'.$link.'"';
		}
		$html .= '></div>';

		return $html;
	}
	
	/**
	 * writes a file for shariff backend
	 *
	 * @return void
	 **/
	public function generateShariffJson()
	{	
		$data->domain = JURI::getInstance()->getHost();		
		$data->services = array_diff(json_decode($this->params->get('data-services'))->services, array('Whatsapp', 'Mail', 'Info'));
		$data->cache->cacheDir = JPATH_SITE.'/cache/plg_jooag_shariff';
		$data->cache->ttl = $this->params->get('cache-time');
		if($this->params->get('cache') == '1'){
			$data->cache->adapter = $this->params->get('cache_handler');
			
			if ($data->cache->adapter == 'file'){
				$data->cache->adapter = 'filesystem';
			}
		}
		$data = json_encode($data, JSON_UNESCAPED_SLASHES);
		JFile::write(JPATH_PLUGINS . '/system/jooag_shariff/backend/shariff.json', $data);
	}
	
	public function onExtensionBeforeSave()
	{
		$json = $this->generateShariffJson();
		
		return $json;
	}
	
	public function onExtensionBeforeInstall()
	{
		$json = $this->generateShariffJson();
			
		return $json;
	}
	
	public function onExtensionBeforeUpdate()
	{
		$json = $this->generateShariffJson();
			
		return $json;
	}
}
