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
		global $post;

		if (isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true && is_object($post) && isset($post->post_content)) {
			$post_content = $post->post_content;
			if (strpos($post_content,  '<!--more-->') !== false) {
				$content_parts = explode('<!--more-->', $post_content);
				$post_lead  = $content_parts[0];
			}
			$post_lead 		= preg_replace('/[^a-z]/i', '', sanitize_text_field($post_lead));
			$sanitized_lead = preg_replace('/[^a-z]/i', '', sanitize_text_field($lead));

			if ($post_lead == $sanitized_lead) {
				$lead = '';
				$content = get_field('easy_reading_content');
				if (strpos($content,  '<!--more-->') !== false) {
					$content_parts = explode('<!--more-->', $content);
					$lead = wp_strip_all_tags($content_parts[0]);
				}
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
		global $post;

		if (isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true && is_object($post) && isset($post->post_content)) {
			$post_content = $post->post_content;
			if (strpos($post_content,  '<!--more-->') !== false) {
				$content_parts = explode('<!--more-->', $post_content);
				$post_content  = $content_parts[1];
			}
			$post_content 	   = preg_replace('/[^a-z]/i', '', sanitize_text_field($post_content));
			$sanitized_content = preg_replace('/[^a-z]/i', '', sanitize_text_field($content));

			if ($post_content == $sanitized_content) {
				$content = get_field('easy_reading_content');
				if (strpos($content,  '<!--more-->') !== false) {
					$content_parts = explode('<!--more-->', $content);
					$content = $content_parts[1];
				}
			}
		}

		return $content;
	}
}
