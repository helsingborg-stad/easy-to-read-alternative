<?php

namespace EasyReading\Posts;

class Content
{
    private $_isReadable = false;

    public function __construct()
    {
        $this->setIsReadable();
        $theme = wp_get_theme();

        $themeName = 'Municipio';

        if (
            $themeName == $theme->name ||
            $themeName == $theme->parent_theme
        ) {
            add_filter('accessibility_items', array($this, 'addAccessibility'), 10);
        }

        add_filter('the_lead', array($this, 'easyReadingLead'), 10);
        add_filter('the_content', array($this, 'easyReadingContent'), 15);
    }

    /**
     * Add easy to read link to accessibility nav
     * @param  array $items Default items
     * @return array       	Modified items
     */
    public function addAccessibility($items): array
    {
        $hasField = get_field('easy_reading_select');

        if ($hasField && !$this->getIsReadable()) {
            $hrefUrl = add_query_arg('readable', true, get_permalink());
            $linkText = __('Easy to read', 'easy-reading');
        } elseif ($hasField && $this->getIsReadable()) {
            $hrefUrl = remove_query_arg('readable', get_permalink());
            $linkText = __('Default version', 'easy-reading');
        }

        $items[] = sprintf('
            <a href="%s" class="">
                <i class="pricon pricon-easy-read"></i>
                %s
            </a>',
            $hrefUrl,
            $linkText
        );

        return $items;
    }

    /**
     * Remove the lead
     * @param  string $lead Default lead
     * @return string       Modified lead
     */
    public function easyReadingLead($lead): string
    {
        if($this->_isShowable()) {
			// Ugly but works...
            return apply_filters('the_content', null);
        }

        return $lead;
    }

    /**
     * Switch content to alternate version
     * @param  string $content 	Default content
     * @return string       	Modified content
     */
    public function easyReadingContent($content)
    {
        if($this->_isShowable()) {
            $easyReadingContent = get_field('easy_reading_content');
            return $easyReadingContent;
        }

        return $content;
    }

    /**
     * Get isReadable property
     * @return bool
     */
    public function getIsReadable(): bool
    {
        return (bool) $this->_isReadable;
    }

    /**
     * Set isReadable property via query string param
     */
    public function setIsReadable(): void
    {
        $queryParam = isset($_GET['readable']) ? substr($_GET['readable'], 0, 1) : false;
        $this->_isReadable = $queryParam === '1' ? true : false;
    }

    /**
     * Check if easy reading content is to be showable
     * @return bool
     */
    private function _isShowable(): bool
    {
        return (
            $this->getIsReadable() &&
            get_field('easy_reading_select') &&
            in_the_loop() &&
            is_main_query()
        );
    }
}
