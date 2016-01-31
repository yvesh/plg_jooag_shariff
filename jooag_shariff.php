<?php
/**
 * @package     JooAg_Shariff
 * @author      Joomla Agentur <info@joomla-agentur.de>
 * @copyright   Copyright (c) 2009 - 2015 Joomla-Agentur All rights reserved.
 * @license     GNU General Public License version 2 || later;
 * @description A small Plugin to share Social Links without compromising their privacy!
 **/

defined('_JEXEC') || die;

/**
 * Class {
 *
 * @since  1.0.0
 */
class PlgSystemJooag_Shariff extends JPlugin
{
	/**
	 * The plugin context
	 *
	 * @var    string
	 */
	protected $context = null;

	/**
	 * Display the buttons before the article
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   mixed    &$article  An object with a "text" property
	 * @param   mixed    &$params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page      Optional page number. Unused. Defaults to zero.
	 *
	 * @return  string
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
		$app = JFactory::getApplication();

		$this->context = $context;

		if ($context == 'com_content.article' && $this->params->get('position') == 1 && $app->isSite())
		{
			$article->introtext = str_replace('{noshariff}', '', $article->introtext, $stringCount);

			if ($stringCount == 0)
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
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
	{
		$app = JFactory::getApplication();

		$this->context = $context;

		if ($context == 'com_content.article' && $this->params->get('position') == 2 && $app->isSite())
		{
			$article->introtext = str_replace('{noshariff}', '', $article->introtext, $stringCount);

			if ($stringCount == 0)
			{
				return $this->getOutputPosition($article, $config = array());
			}
		}

		if ($context == 'com_matukio.event' && $app->isSite())
		{
			$article->description = str_replace('{noshariff}', '', $article->description, $stringCount);

			if ($stringCount == 0)
			{
				return $this->getOutputPosition($article, $config = array());
			}
		}
	}

	/**
	 * Place shariff in your aticles and modules via {shariff} shorttag
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   mixed    &$article  An object with a "text" property
	 * @param   mixed    &$params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page      Optional page number. Unused. Defaults to zero.
	 *
	 * @return  void
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$app = JFactory::getApplication();

		$this->context = $context;

		if ($context == 'mod_custom.content' && preg_match_all('/{shariff\ ([^}]+)\}|\{shariff\}/', $article->text, $matches) && $app->isSite())
		{
			$params = explode(' ', trim($matches[0][0], '}'));
			$config = array();

			foreach ($params as $key => $item)
			{
				if ($key != 0)
				{
					list($k, $v) = explode("=", $item);
					$config[$k] = $v;
				}
			}

			$article->text = str_replace($matches[0][0], $this->getOutputPosition($article, $config), $article->text);
		}

		if ($context == 'mod_articles_news.content' && ($this->params->get('position') == 1 || $this->params->get('position') == 2))
		{
			$article->text .= '{noshariff}';
		}
	}

	/**
	 * appends the required scripts to the documents and returns the markup
	 *
	 * @param   mixed  $article  - An object with a "text" property
	 * @param   array  $config   - Config
	 *
	 * @return  void|string
	 */
	public function getOutputPosition($article, $config)
	{
		$catIds  = (array) $this->params->get('showbycategory');
		$menuIds = (array) $this->params->get('showbymenu');
		$app     = JFactory::getApplication();
		$menu    = $app->getMenu()->getActive();

		if (is_object($menu))
		{
			$actualMenuId = $menu->id;
		}
		else
		{
			$actualMenuId = $app->input->getInt('Itemid', 0);
		}

		$view = 0;

		if ($this->params->get('wheretoshow') == 3)
		{
			$view = 1;
		}

		if ((isset($article->catid) && in_array($article->catid, $catIds)) || in_array($actualMenuId, $menuIds))
		{
			if ($this->params->get('wheretoshow') == 2)
			{
				$view = 1;
			}

			if ($this->params->get('wheretoshow') == 3)
			{
				$view = 0;
			}
		}

		if ($view == 1 || $this->params->get('wheretoshow') == 1)
		{
			return $this->getOutput($config);
		}
	}

	/**
	 * Shariff output generation
	 *
	 * @param   array  $config  - Config
	 *
	 * @return  string
	 */
	public function getOutput($config)
	{
		$doc = JFactory::getDocument();

		JHtml::_('jquery.framework');
		$doc->addStyleSheet(JURI::root() . 'media/plg_jooag_shariff/css/' . $this->params->get('shariffcss'));
		$doc->addScript(JURI::root() . 'media/plg_jooag_shariff/js/' . $this->params->get('shariffjs'));
		$doc->addScriptDeclaration('jQuery(document).ready(function() {var buttonsContainer = jQuery(".shariff");new Shariff(buttonsContainer);});');

		// Cache Folder
		jimport('joomla.filesystem.folder');

		if (!JFolder::exists(JPATH_SITE . '/cache/plg_jooag_shariff') && $this->params->get('data_backend_url'))
		{
			JFolder::create(JPATH_SITE . '/cache/plg_jooag_shariff', 0755);
		}

		$html = '<div class="shariff"';
		$html .= ($this->params->get('data_backend_url')) ? ' data-backend-url="' . JURI::root() . '/plugins/system/jooag_shariff/backend/"' : '';
		$html .= ' data-lang="' . explode("-", JFactory::getLanguage()->getTag())[0] . '"';
		$html .= ($this->params->get('data_mail_url')) ? ' data-mail-url="mailto:' . $this->params->get('data_mail_url') . '"' : '';
		$html .= (array_key_exists('orientation', $config)) ? ' data-orientation="' . $config['orientation'] . '"' : ' data-orientation="' . $this->params->get('data_orientation') . '"';

		$html .= ' data-services="' . htmlspecialchars(json_encode(array_map('strtolower', (array) json_decode($this->params->get('data_services'))->services))) . '"';
		$html .= (array_key_exists('theme', $config)) ? ' data-theme="' . $config['theme'] . '"' : ' data-theme="' . $this->params->get('data_theme') . '"';
		$html .= ' data-url="' . JURI::getInstance()->toString() . '"';

		if (($id = (int) $this->params->get('data_info_url')) && $this->context == 'com_content.article')
		{
			jimport('joomla.database.table');
			$item = JTable::getInstance("content");
			$item->load($this->params->get('data_info_url'));
			require_once JPATH_SITE . '/components/com_content/helpers/route.php';
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language));
			$html .= ' data-info-url="' . $link . '"';
		}

		$html .= '></div>';

		return $html;
	}

	/**
	 * Generator for shariff.json if the is saved
	 *
	 * @param   string  $context  - The context passed to the plugin.
	 * @param   JTable  $table    - The JTable object
	 * @param   bool    $isNew    - Is the entry new?
	 *
	 * @return void
	 */
	public function onExtensionBeforeSave($context, $table, $isNew)
	{
		if ($table->name == 'PLG_JOOAG_SHARIFF')
		{
			$params         = json_decode($table->params);
			$data->domain   = JURI::getInstance()->getHost();
			$data->services = array_diff(json_decode($params->data_services)->services, array('AddThis', 'Whatsapp', 'Mail', 'Info', 'Tumblr', 'Flattr', 'Diaspora'));

			if ($params->fb_app_id && $params->fb_secret)
			{
				$data->Facebook->app_id = $params->fb_app_id;
				$data->Facebook->secret = $params->fb_secret;
			}

			$data->cache->cacheDir = JPATH_SITE . '/cache/plg_jooag_shariff';
			$data->cache->ttl      = $params->cache_time;

			if ($params->cache == 1)
			{
				$data->cache->adapter = $params->cache_handler;

				if ($params->cache_handler == 'file')
				{
					$data->cache->adapter = 'filesystem';
				}
			}

			$data = json_encode($data, JSON_UNESCAPED_SLASHES);
			JFile::write(JPATH_PLUGINS . '/system/jooag_shariff/backend/shariff.json', $data);
		}
	}
}
