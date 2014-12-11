<?php
/**
 * @package    JooAg_Shariff
 *
 * @author     Joomla Agentur <info@joomla-agentur.de>
 * @copyright  Copyright (c) 2009 - 2015 Joomla-Agentur All rights reserved.
 * @license    GNU General Public License version 2 or later;
 * @description A small Plugin to share Social Links!
 */
defined('_JEXEC') or die;

/**
 * Class PlgContentJooag_Shariff
 *
 * @since  1.0.0
 */
class PlgContentJooag_Shariff extends JPlugin
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
	 */
	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
		$output = $this->getOutput('0');

		return $output;
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
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
	{
		$output = $this->getOutput('1');

		return $output;
	}

	/**
	 * appends the required scripts to the documents and returns the markup
	 *
	 * @param   integer  $position  current position
	 *
	 * @return string
	 */
	public function getOutput($position)
	{
		$setCatId = $this->params->get('showbycategory');
		$currentCatId = JFactory::getApplication()->input->getInt('catid');
		$output = '';

		if ($this->params->get('position') == $position AND ((is_array($setCatId) && in_array($currentCatId, $setCatId)) OR empty($setCatId)))
		{
			$doc = JFactory::getDocument();
			$lang = JFactory::getLanguage();
			JHtml::_('jquery.framework');

			$doc->addScript(JURI::root() . 'plugins/content/jooag_shariff/shariff.min.js');
			$doc->addStyleSheet(JURI::root() . 'plugins/content/jooag_shariff/shariff.min.css');

			$services = implode("&quot;,&quot;", $this->params->get('services'));
			$services = '&quot;' . $services . '&quot;';

			$output = '<div data-theme="' . $this->params->get('theme')
				. '" data-lang="' . $lang->getLocale()[7]
				. '" data-orientation="' . $this->params->get('orientation')
				. '" data-url="' . JURI::current()
				. '" data-info-url="' . $this->params->get('info')
				. '" data-services="[' . $services . ']" data-backend-url="/plugins/content/jooag_shariff/backend" data-referrer-track="null" class="shariff"></div>';
		}

		return $output;
	}
}
