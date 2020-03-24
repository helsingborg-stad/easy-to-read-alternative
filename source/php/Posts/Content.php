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
		add_filter('the_lead', array($this, 'easyReadingLead'), 11);
		add_filter('the_content', array($this, 'easyReadingContent'), 10);
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
	 * Remove the lead
	 * @param  string $lead Default lead
	 * @return string       Modified lead
	 */
	public function easyReadingLead($lead)
	{
		if (isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true && in_the_loop() && is_main_query()) {
			return '';
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

		if (isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true && is_object($post) && isset($post->post_content) && in_the_loop() && is_main_query()) {
			$post_content = $post->post_content;
			if (strpos($post_content,  '<!--more-->') !== false) {
        $content_parts = explode('<!--more-->', $post_content, 0);
        $post_content  = $content_parts[0];
			}
			$post_content 	   = preg_replace('/[^a-z]/i', '', sanitize_text_field($post_content));
			$sanitized_content = preg_replace('/[^a-z]/i', '', sanitize_text_field($content));

			if ($post_content == $sanitized_content) {
				$content = get_field('easy_reading_content');
				if (strpos($content,  '<!--more-->') !== false) {
					$content_parts = explode('<!--more-->', $content);
					$content = '<p class="lead">' . sanitize_text_field($content_parts[0]) . '</p>' . $content_parts[1];
				}
			}
		}

		return $content;
	}
}
