<?php

namespace EasyReading\Posts;

class Content
{
	public function __construct()
	{
		$theme = wp_get_theme();
		if ('Municipio' == $theme->name || 'Municipio' == $theme->parent_theme) {
			add_filter('accessibility_items', array($this, 'addAccessibility'), 11);
		}
		add_filter('the_lead', array($this, 'easyReadingLead'));
		add_filter('the_content', array($this, 'easyReadingContent'));
	}

	/**
	 * Add easy to read link to accessibility nav
	 * @param  array $items Default items
	 * @return array       	Modified items
	 */
	public function addAccessibility($items): array
	{
		global $wp;
		$current_url = home_url(add_query_arg(array(),$wp->request));

		if (! isset($_GET['readable']) && get_field('easy_reading_select') == true) {
			$items[] = '<a href="' . add_query_arg('readable', '1', $current_url) . '" class=""><i class="pricon pricon-easy-read"></i> ' . __('Easy to read', 'easy-reading') . '</a>';
    	} elseif(isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true) {
    		$items[] = '<a href="' . remove_query_arg('readable', $current_url) . '" class=""><i class="pricon pricon-easy-read"></i> ' . __('Default version', 'easy-reading') . '</a>';
    	}

    	return $items;
	}

	/**
	 * Switch content lead to alternate version
	 * @param  string $lead Default lead
	 * @return string       Modified lead
	 */
	public function easyReadingLead($lead)
	{
		if (isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true) {
			$lead = '';
			$content = get_field('easy_reading_content');
			if (strpos($content,  '<!--more-->') !== false) {
				$content_parts = explode('<!--more-->', $content);
				$lead = wp_strip_all_tags($content_parts[0]);
			}
		}

		return $lead;
	}

	/**
	 * Switch content to alternate version
	 * @param  string $content Default content
	 * @return string       Modified content
	 */
	public function easyReadingContent($content)
	{
		if (isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true) {
			$content = get_field('easy_reading_content');
			if (strpos($content,  '<!--more-->') !== false) {
				$content_parts = explode('<!--more-->', $content);
				$content = $content_parts[1];
			}

		}

		return $content;
	}
}
