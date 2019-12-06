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
        global $post;

        if($this->shouldDisplay($post)) {
            return "";
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

        if($this->shouldDisplay($post)) {
            remove_filter('the_content', array($this, 'easyReadingContent'));
            $content = apply_filters('the_content', get_field('easy_reading_content'));
            add_filter('the_content', array($this, 'easyReadingContent'), 10);
        }

        return $content;
    }

    /**
     * Detect if alternate content should be delivered
     * @param  string $post The post object
     * @return bool   If the easy read text should be displayed
     */
    public function shouldDisplay($post) {
        if(!(isset($_GET['readable']) && $_GET['readable'] == '1')) {
            return false;
        }

        if(!(is_object($post) && isset($post->post_content))) {
            return false;
        }

        if(!(in_the_loop() && is_main_query())) {
            return false;
        }

        if(get_field('easy_reading_select') == false) {
            return false;
        }

        return true;
    }
}