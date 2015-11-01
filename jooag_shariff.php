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

		if($context == 'com_content.article' and $this->params->get('position') == 1 and $app->isSite())
		{
			$article->introtext = str_replace('{noshariff}', '', $article->introtext, $stringCount);
	
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
			
		if($context == 'com_content.article' and $this->params->get('position') == 2 and $app->isSite())
		{
			$article->introtext = str_replace('{noshariff}', '', $article->introtext, $stringCount);
			
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

			$article->text = str_replace($matches[0][0], $this->getOutputPosition($article, $config), $article->text);
		}
		
		if($context == 'mod_articles_news.content'){
			$article->text .= '{noshariff}';
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
		$menu = $app->getMenu()->getActive();
		
		if (is_object($menu))
		{
		  $actualMenuId = $menu->id;
		}
		else
		{
		  $actualMenuId = $app->input->getInt('Itemid', 0);
		}

		$view = 0;

		if($this->params->get('wheretoshow') == 3){
			$view = 1;
		}

		if ((isset($article->catid) and in_array($article->catid, $catIds)) or in_array($actualMenuId, $menuIds))
		{
			if($this->params->get('wheretoshow') == 2){
				$view = 1;
			}

			if($this->params->get('wheretoshow') == 3){
				$view = 0;
			}
		}

		if($view == 1 or $this->params->get('wheretoshow') == 1){
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
		$html .= ' data-url="'.JURI::getInstance()->toString().'"';
		
		//getServices::start
		$services = array();
		$ordering = array();
		//Twitter
		if($this->params->get('shariff_twitter'))
		{
			$ordering['twitter'] = array($this->params->get('shariff_twitter_ordering'));
			$html .= ($this->params->get('shariff_twitter_via')) ? ' data-twitter-via="'.$this->params->get('shariff_twitter_via').'"' : '';
		}
		//Facebook
		if($this->params->get('shariff_facebook'))
		{
			$ordering['facebook'] = array($this->params->get('shariff_facebook_ordering'));
		}
		//GooglePlus
		$this->params->get('shariff_googleplus') ? $ordering['googleplus'] = array($this->params->get('shariff_googleplus_ordering')) : '';
		//LinkedIn
		$this->params->get('shariff_linkedin') ? $ordering['linkedin'] = $this->params->get('shariff_linkedin_ordering') : '';
		//Pinterest
		$this->params->get('shariff_pinterest') ? $ordering['pinterest'] = $this->params->get('shariff_pinterest_ordering') : '';
		//Xing
		$this->params->get('shariff_xing') ? $ordering['xing'] = $this->params->get('shariff_xing_ordering') : '';
		//Whatsapp
		$this->params->get('shariff_whatsapp') ? $ordering['whatsapp'] = $this->params->get('shariff_whatsapp_ordering') : '';
		//AddThis
		$this->params->get('shariff_addthis') ? $ordering['addthis'] = $this->params->get('shariff_addthis_ordering') : '';
		//Tumblr
		$this->params->get('shariff_tumblr') ? $ordering['tumblr'] = $this->params->get('shariff_tumblr_ordering') : '';
		//Flattr
		if($this->params->get('shariff_flattr'))
		{	
			$ordering['flattr'] = $this->params->get('shariff_flattr_ordering');
			$html .= ($this->params->get('shariff_flattr_category')) ? ' data-flattr-category="'.$this->params->get('shariff_flattr_category').'"' : '';
			$html .= ($this->params->get('shariff_flattr_user')) ? ' data-flattr-user="'.$this->params->get('shariff_flattr_user').'"' : '';
		} 
		//Diaspora
		$this->params->get('shariff_diaspora') ? $ordering['diaspora'] = $this->params->get('shariff_diaspora_ordering') : '';
		//Mail
		if($this->params->get('shariff_mail'))
		{
			$ordering['mail'] = array($this->params->get('shariff_mail_ordering'));
			$html .= ($this->params->get('data_mail_url')) ? ' data-mail-url="mailto:'.$this->params->get('data_mail_url').'"' : '';
			$html .= ($this->params->get('data-mail-subject')) ? ' data-mail-subject="'.$this->params->get('data-mail-subject').'"' : '';
			$html .= ($this->params->get('data-mail-body')) ? ' data-mail-body="'.$this->params->get('data-mail-body').'"' : '';
		}
		//Info
		if($this->params->get('shariff_info'))
		{
			$ordering['info'] = array($this->params->get('shariff_info_ordering'));
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
		//Services Ordering
		array_multisort($ordering);
		foreach($ordering as $key => $orders)
		{
			$services[] = $key;
		}

		//Services
		$html .= ' data-services="'.htmlspecialchars(json_encode((array)$services)).'"';
		//getServices::end		
		
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
			
			$data->services[] = $this->params->get('shariff_googleplus');
			$data->services[] = $this->params->get('shariff_twitter');
			$data->services[] = $this->params->get('shariff_facebook');
			$data->services[] = $this->params->get('shariff_linkedin');
			$data->services[] = $this->params->get('shariff_flattr');
			$data->services[] = $this->params->get('shariff_pinterest');
			$data->services[] = $this->params->get('shariff_xing');
			$data->services[] = $this->params->get('shariff_addthis');
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