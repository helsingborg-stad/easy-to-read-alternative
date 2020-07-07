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

        add_filter('the_post', array($this, 'replacePostContent'), 9);
        add_filter('the_lead', array($this, 'easyReadingLead'), 10);
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
        $current_url = home_url(add_query_arg(array(), $wp->request));
        $items = []; 

        if (! isset($_GET['readable']) && get_field('easy_reading_select') == true) {
            $items[] = '<a href="' . add_query_arg('readable', '1', $current_url) . '" class=""><i class="pricon pricon-easy-read"></i> ' . __('Easy to read', 'easy-reading') . '</a>';
        } elseif (isset($_GET['readable']) && $_GET['readable'] == '1' && get_field('easy_reading_select') == true) {
            $items[] = '<a href="' . remove_query_arg('readable', $current_url) . '" class=""><i class="pricon pricon-easy-read"></i> ' . __('Default version', 'easy-reading') . '</a>';
        }

        return $items;
    }

    /**
     * Replaces post content with readable alternative if it exists
     * @param object $post The post object
     * @return void
     */
    public function replacePostContent($post)
    {
        if ($this->shouldDisplay($post)) {
            $post->post_content = get_field('easy_reading_content', $post->ID);
        }

        return $post;
    }

    /**
     * Removes the lead if readable content is showing
     * @param  string $lead Default lead
     * @return string       Modified lead
     */
    public function easyReadingLead($lead)
    {
        global $post;

        if ($this->shouldDisplay($post)) {
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

        if ($this->shouldDisplay($post)) {
            $content = get_field('easy_reading_content', $post->ID);

            // Apply lead styles to more tag content
            if (strpos($content, '<!--more-->') !== false) {
                $content_parts = explode('<!--more-->', $content);
                $content = '<p class="lead">' . sanitize_text_field($content_parts[0]) . '</p>' . $content_parts[1];
            }
        }

        return $content;
    }

    /**
   * Detect if alternate content should be delivered
   * @param  string $post The post object
   * @return bool   If the easy read text should be displayed
   */
    public function shouldDisplay($post)
    {
        if (empty($post->ID)) {
            return false;
        }

        if (!(isset($_GET['readable']) && $_GET['readable'] == '1')) {
            return false;
        }

        if (!(in_the_loop() && is_main_query())) {
            return false;
        }

        if (get_field('easy_reading_select', $post->ID) == false) {
            return false;
        }

        return true;
    }
}
