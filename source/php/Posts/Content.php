<?php

namespace EasyReading\Posts;

class Content
{
	public function __construct()
	{
		//add_filter('the_content', array($this, 'addLink'));

		add_filter('the_lead', array($this, 'easyReadingLead'));
		add_filter('the_content', array($this, 'easyReadingContent'));
	}

	public function addLink($content)
	{
		if (get_field('readable_content_select') == true) {
			$content = '<a href="' . add_query_arg('readable', 'true', get_permalink()) . '">' . __('Easy to read', 'easy-reading') . '</a>';
	    	$content .= $content;
    	}

    	return $content;
	}

	public function easyReadingLead($lead)
	{
		if (isset($_GET['readable']) && $_GET['readable'] == 'true' && get_field('easy_reading_select') == true) {
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
		if (isset($_GET['readable']) && $_GET['readable'] == 'true' && get_field('easy_reading_select') == true) {
			$content = get_field('easy_reading_content');
			if (strpos($content,  '<!--more-->') !== false) {
				$content_parts = explode('<!--more-->', $content);
				$content = $content_parts[1];
			}

		}

		return $content;
	}

}
