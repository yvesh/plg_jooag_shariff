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
	 * Display the buttons before the article
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
			return $this->getOutputPosition($article);
		}
	}

	/**
	 * Display the buttons after the article
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
			return $this->getOutputPosition($article);
		}
	}
	
	/**
	 * Place shariff in your aticles and modules via {shariff} shorttag
	 **/
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{	
		if	($context == 'mod_custom.content' and $this->params->get('position') == '3'
			and (preg_match_all('/\{shariff\ ([^}]+)\}/', $article->text, $matches) or preg_match_all('/{shariff}/', $article->text, $matches)))
		{
			foreach($matches[0] as $matchIndex => $match){
				$params = explode(' ', trim($match,'}'));
				$config = array ();
				
				foreach ($params as $key => $item)
				{
					if($key != '0')
					{
						list($k, $v) = explode("=", $item);
						$config[ $k ] = $v;
					}

				}

				$article->text = str_replace($matches[0][$matchIndex], $this->getOutputPosition($article, $config), $article->text);
			}
		}
	} 
	
	/**
	 * appends the required scripts to the documents and returns the markup
	 *
	 * @param   mixed    &$article  An object with a "text" property
	 *
	 * @return string
	 **/
	public function getOutputPosition($article, $config)
	{
		$catIds = (array)$this->params->get('showbycategory');
		$menuIds = (array)$this->params->get('showbymenu');
		$app = JFactory::getApplication();
		$actualMenuId = $app->getMenu()->getActive()->id;
		$view = '0';
			
		if($this->params->get('wheretoshow') == '3'){
			$view = '1';
		}
		
		if ((isset($article->catid) and in_array($article->catid, $catIds)) or in_array($actualMenuId, $menuIds))
		{
			if($this->params->get('wheretoshow') == '2'){
				$view = '1';
			}
			
			if($this->params->get('wheretoshow') == '3'){
				$view = '0';
			}
		}

		if($view == '1' or $this->params->get('wheretoshow') == '1'){		
			return $this->getOutput($config);
		}
	}

	/**
	 * Shariff output generation
	 **/
	public function getOutput($config)
	{
		$doc = JFactory::getDocument();
		JHtml::_('jquery.framework');
		$doc->addStyleSheet(JURI::root().'media/plg_jooag_shariff/css/'.$this->params->get('shariffcss'));
		$doc->addScript(JURI::root().'media/plg_jooag_shariff/js/'.$this->params->get('shariffjs'));
		$doc->addScriptDeclaration( 'jQuery(document).ready(function() {var buttonsContainer = jQuery(".shariff");new Shariff(buttonsContainer);});' );
		
		//Cache Folder
		jimport('joomla.filesystem.folder');
		if(!JFolder::exists(JPATH_SITE.'/cache/plg_jooag_shariff') and $this->params->get('data_backend_url')){
			JFolder::create(JPATH_SITE.'/cache/plg_jooag_shariff', 0755);
		}
		
		$html  = '<div class="shariff"';
		$html .= ($this->params->get('data_backend_url')) ? ' data-backend-url="/plugins/system/jooag_shariff/backend/"' : '';
		$html .= ' data-lang="'.explode("-", JFactory::getLanguage()->getTag())[0].'"';
		$html .= ($this->params->get('data_mail_url')) ? ' data-mail-url="mailto:'.$this->params->get('data_mail_url').'"' : '';
		$html .= (array_key_exists('orientation', $config)) ? ' data-orientation="'.$config['orientation'].'':' data-orientation="'.$this->params->get('data_orientation').'"';	
		$html .= ' data-services='.json_encode(array_map('strtolower', (array)json_decode($this->params->get('data_services'))->services));
		$html .= (array_key_exists('theme', $config)) ? ' data-theme="'.$config['theme'].'"' : ' data-theme="'.$this->params->get('data_theme').'"';
		$html .= ' data-url="'.JURI::getInstance()->toString().'"';
		if (($id = (int) $this->params->get('data_info_url')))
		{
			jimport( 'joomla.database.table' );
			$item =	JTable::getInstance("content");
			$item->load($this->params->get('data_info_url'));
			require_once JPATH_SITE . '/components/com_content/helpers/route.php';
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language));
			$html .= ' data-info-url="'.$link.'"';
		}
		$html .= '></div>';
		return $html;
	}
	
	/**
	 * Generator for shariff.json if the is saved
	 *
	 * @return void
	 **/
	public function onExtensionBeforeSave($context, $table, $isNew)
	{	
		if($table->name == 'PLG_JOOAG_SHARIFF')
		{
			$params = json_decode($table->params);
			$data->domain = JURI::getInstance()->getHost();		
			$data->services = array_diff(json_decode($params->data_services)->services, array('Whatsapp', 'Mail', 'Info'));
			
			if($params->fb_app_id and $params->fb_secret)
			{
				$data->Facebook->app_id = $params->fb_app_id;
				$data->Facebook->secret = $params->fb_secret;
			}
			
			$data->cache->cacheDir = JPATH_SITE.'/cache/plg_jooag_shariff';
			$data->cache->ttl = $params->cache_time;
			
			if($params->cache == '1')
			{				
				$data->cache->adapter = $params->cache_handler;
				
				if($params->cache_handler == 'file'){
					$data->cache->adapter = 'filesystem';
				}
			}
			
			$data = json_encode($data, JSON_UNESCAPED_SLASHES);
			JFile::write(JPATH_PLUGINS . '/system/jooag_shariff/backend/shariff.json', $data);
		}
	}
}
