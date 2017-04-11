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

	public function addAccessibility($items)
	{
		if (! isset($_GET['readable']) && get_field('readable_content_select') == true) {
			$items[] = '<a href="' . add_query_arg('readable', '1', get_permalink()) . '" class=""><i class="pricon pricon-easy-read"></i> ' . __('Easy to read', 'easy-reading') . '</a>';
    	} elseif(isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('readable_content_select') == true) {
    		$items[] = '<a href="' . get_permalink() . '" class=""><i class="pricon pricon-easy-read"></i> ' . __('Default version', 'easy-reading') . '</a>';
    	}

    	return $items;
	}

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
