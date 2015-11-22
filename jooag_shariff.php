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
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
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
		$app = JFactory::getApplication();

		if($context == 'com_content.article' AND $app->isSite() AND ($this->params->get('com_content_output') == 1 OR $this->params->get('com_content_output') == 3))
		{
			$article->introtext = str_replace('{noshariff}', '', $article->introtext, $stringCount);
	
			$config['shorttag'] = 0;
			
			if($stringCount == 0)
			{
				return $this->getOutputPosition($article, $config = array());
			}
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
		$app = JFactory::getApplication();
			
		if($context == 'com_content.article' AND $app->isSite() AND ($this->params->get('com_content_output') == 2 OR $this->params->get('com_content_output') == 3))
		{
			$article->introtext = str_replace('{noshariff}', '', $article->introtext, $stringCount);
			
			$config['shorttag'] = 0;
			
			if($stringCount == 0)
			{
				return $this->getOutputPosition($article, $config = array());
			}
		}
	}

	/**
	 * Place shariff in your aticles and modules via {shariff} shorttag
	 **/
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$app = JFactory::getApplication();
		
		if(preg_match_all('/{shariff\ ([^}]+)\}|\{shariff\}/', $article->text, $matches) and $app->isSite())
		{
			$params = explode(' ', trim($matches[0][0],'}'));
			$config = array ();

			foreach ($params as $key => $item)
			{
				if($key != 0)
				{
					list($k, $v) = explode("=", $item);
					$config[ $k ] = $v;
				}
			}
			
			$this->params->get('shorttag_use') ? $config['shorttag'] = 1 : $config['shorttag'] = 0;

			$article->text = str_replace($matches[0][0], $this->getOutputPosition($article, $config), $article->text);
		}
		
		if($context == 'mod_articles_news.content'){
			$article->text .= '{noshariff}';
		}
	}

	
	public function onBeforeRender()
	{
		$app = JFactory::getApplication();
		
		if($this->params->get('shariff_where_output') == 2 and $app->isSite())
		{
			$doc = JFactory::getDocument();
			$config['shorttag'] = 0;
			$buffer = $doc->getBuffer('component');
			$buffering = '';
			
			if($this->getMenuAccess() == 1 and ($this->params->get('shariff_position_output') == 1 or $this->params->get('shariff_position_output') == 3))
			{
				$buffering .= $this->getOutput($config);
			}
			
			$buffering .= $buffer;
			
			if($this->getMenuAccess() == 1 and ($this->params->get('shariff_position_output') == 2 or $this->params->get('shariff_position_output') == 3))
			{
				$buffering .= $this->getOutput($config);
			}

			$doc->setBuffer($buffering, 'component');
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

		//Check for Com_Content Category
		$catIds = (array)$this->params->get('content_showbycategory');
		
		$this->params->get('com_content_category_access') == 0 ? $contentCategoryAccess = 0 : '';
		$this->params->get('com_content_category_access') == 1 ? $contentCategoryAccess = 1 : '';

		if($this->params->get('com_content_category_access') == 2)
		{
			$contentCategoryAccess = 0;
			isset($article->catid) and in_array($article->catid, $catIds) ? $contentCategoryAccess = 1 : '';
		}
		
		if($this->params->get('com_content_category_access') == 3)
		{
			$contentCategoryAccess = 1;
			isset($article->catid) and in_array($article->catid, $catIds) ? $contentCategoryAccess = 0 : '';
		}
		//END
			
		!isset($config['shorttag']) ? $config['shorttag'] = 0 : '';
		
		//Show
		if($this->getMenuAccess() == 1 and ($contentCategoryAccess == 1 or $config['shorttag'] == '1'))
		{
			return $this->getOutput($config);
		}
	}
	
	private function getMenuAccess()
	{
		//Check for Menu Item has the highest priority
		$app = JFactory::getApplication();
		$menu = $app->getMenu()->getActive();
		$menuIds = (array)$this->params->get('content_showbymenu');
		is_object($menu) ? $actualMenuId = $menu->id : $actualMenuId = $app->input->getInt('Itemid', 0);
		
		$this->params->get('menu_access') == 0 ? $menuAccess = 0 : '';
		$this->params->get('menu_access') == 1 ? $menuAccess = 1 : '';
		
		if($this->params->get('menu_access') == 2)
		{
			$menuAccess = 0;
			in_array($actualMenuId, $menuIds) ? $menuAccess = 1 : '';
		}
		
		if($this->params->get('menu_access') == 3)
		{
			$menuAccess = 1;
			in_array($actualMenuId, $menuIds) ? $menuAccess = 0 : '';
		}
		
		return $menuAccess;
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
		$doc->addScriptDeclaration('jQuery(document).ready(function() {var buttonsContainer = jQuery(".shariff");new Shariff(buttonsContainer);});');

		//Cache Folder
		jimport('joomla.filesystem.folder');
		if(!JFolder::exists(JPATH_SITE.'/cache/plg_jooag_shariff') and $this->params->get('data_backend_url')){
			JFolder::create(JPATH_SITE.'/cache/plg_jooag_shariff', 0755);
		}
				
		$html  = '<div class="shariff"';
		$html .= ($this->params->get('data_backend_url')) ? ' data-backend-url="/plugins/system/jooag_shariff/backend/"' : '';
		$html .= ' data-lang="'.explode("-", JFactory::getLanguage()->getTag())[0].'"';
		$html .= (array_key_exists('orientation', $config)) ? ' data-orientation="'.$config['orientation'].'"' : ' data-orientation="'.$this->params->get('data_orientation').'"';
		$html .= (array_key_exists('theme', $config)) ? ' data-theme="'.$config['theme'].'"' : ' data-theme="'.$this->params->get('data_theme').'"';		

		//getServices::start
		$services = array('twitter','facebook','googleplus','linkedin','pinterest','xing','whatsapp','mail','info','addthis','tumblr','flattr','diaspora','reddit','stumbleupon','threema');		
				
		foreach ($services as $key => $service)
		{
			$this->params->get('shariff_'.$service) ? $activeServices[$service][] = $this->params->get('shariff_'.$service.'_ordering') : '';
		}
		
		array_multisort($activeServices);
				
		foreach($activeServices as $key => $activeService)
		{
			$orderedServices[] = $key;
		}

		//Services output
		$html .= ' data-services="'.htmlspecialchars(json_encode((array)$orderedServices)).'"';
		//getServices::end
		
		//Twitter
		if($this->params->get('shariff_twitter'))
		{
			$html .= ($this->params->get('shariff_twitter_via')) ? ' data-twitter-via="'.$this->params->get('shariff_twitter_via').'"' : '';
		}
		//Flattr
		if($this->params->get('shariff_flattr'))
		{	
			$html .= ($this->params->get('shariff_flattr_category')) ? ' data-flattr-category="'.$this->params->get('shariff_flattr_category').'"' : '';
			$html .= ($this->params->get('shariff_flattr_user')) ? ' data-flattr-user="'.$this->params->get('shariff_flattr_user').'"' : '';
		} 
		
		//Mail
		if($this->params->get('shariff_mail'))
		{
			$html .= ($this->params->get('data_mail_url')) ? ' data-mail-url="mailto:'.$this->params->get('data_mail_url').'"' : '';
			$html .= ($this->params->get('data-mail-subject')) ? ' data-mail-subject="'.$this->params->get('data-mail-subject').'"' : '';
			$html .= ($this->params->get('data-mail-body')) ? ' data-mail-body="'.$this->params->get('data-mail-body').'"' : '';
		}
		//Info
		if($this->params->get('shariff_info'))
		{
			if ((int)$this->params->get('data_info_url'))
			{
				jimport('joomla.database.table');
				$item =	JTable::getInstance("content");
				$item->load($this->params->get('data_info_url'));
				require_once JPATH_SITE . '/components/com_content/helpers/route.php';
				$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language));
				$html .= ' data-info-url="'.$link.'"';
			}
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
			
			$services = array('facebook','googleplus','twitter','linkedin','reddit','stumbleupon','flattr','pinterest'/*,'addthis'*/);
			
			foreach($services as $service)
			{
				$data->services[] = $this->params->get('shariff_'.$service);
			}
			//Delete unuses services
			$data->services = array_diff($data->services, array('0'));
						
			if($params->fb_app_id and $params->fb_secret)
			{
				$data->Facebook->app_id = $params->fb_app_id;
				$data->Facebook->secret = $params->fb_secret;
			}

			$data->cache->cacheDir = JPATH_SITE.'/cache/plg_jooag_shariff';
			$data->cache->ttl = $params->cache_time;
			$data->client->timeout = $params->client_timeout;
			
			if($params->cache)
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