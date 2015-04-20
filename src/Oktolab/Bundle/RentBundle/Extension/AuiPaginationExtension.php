<?php

namespace Oktolab\Bundle\RentBundle\Extension;

const FIRST = "aui-nav-first";
const SELECTED ="aui-nav-selected";
const LAST = "aui-nav-last";
const PREV = "aui-nav-previous";
const NEXT = "aui-nav-next";

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as Router;

/**
 * AUI-Pager Extension for Twig.
 *
 * @author rs
 * @see https://developer.atlassian.com/design/latest/pagination.html
 *
 * @example aui_pager('dashboard', 30, 3)
 */
class AuiPaginationExtension extends \Twig_Extension
{

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator = null;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $routing = null;

    /**
     * @var string
     */
    private $htmlString = '';

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Translation\TranslatorInterface            $translator
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface    $router
     */
    public function __construct(TranslatorInterface $translator, Router $router)
    {
        $this->translator   = $translator;
        $this->routing      = $router;

        // @TODO: Caching with INDEX: md5($url_name, $pages, $current, $max, $language) would be possible
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getFunctions()
    {
        return array(
            'auiPager' => new \Twig_Function_Method($this, 'getPagerHtml', array('is_safe' => array('html'))),
            'aui_pager' => new \Twig_Function_Method($this, 'getPagerHtml', array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders a nice Paging Navigation like "prev 1 ... 3 4 5 6 7 ... 1 next"
     * Including: Next and Previous link, last and first link, leading and following pages
     *
     * @see https://developer.atlassian.com/design/latest/pagination.html
     *
     * @param string $url_name   uri identifier
     * @param int    $pages      number of pages
     * @param int    $current    current page
     * @param int    $max        maximal number of companion pages to render
     *
     * @return string HTML
     */
    public function getPagerHtml($url_name, $pages, $current, $max = 5, $sortBy = '', $order = '', $nbResults = 10)
    {
        if (1 === $pages) {
            return '<ol class="aui-nav aui-nav-pagination"></ol>';
        }

        $startPoint = $this->calculateStartpoint($pages, $current, $max);

        $this->htmlString = '<ol class="aui-nav aui-nav-pagination">';

        if ($pages < $max) { //No truncating needed, render all pages
            for ($i =1; $i <= $pages; $i++) {
                if ($i == 1) {
                    if ($current > 1) {
                        $this->addListPoint(
                            $this->translator->trans('generic.previous'),
                            $this->routing->generate($url_name, array('page' => $current-1, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults)),
                            PREV
                        );
                    }
                }

                if ($i == $current) {
                    $this->htmlString .= sprintf('<li class="%s">%s</li>', SELECTED, $i);
                } else {
                    $this->addListPoint($i, $this->routing->generate($url_name, array('page' => $i, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults)));
                }

                if ($i == $pages) {
                    if ($current < $pages) {
                        $this->addListPoint(
                            $this->translator->trans('generic.next'),
                            $this->routing->generate($url_name, array('page' => $current+1, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults)),
                            NEXT
                        );
                    }
                }
            }
            $this->htmlString = $this->htmlString."</ol>";

            return $this->htmlString;
        }

        for ($i = 1; $i <= $pages; $i++) {
            if ($i == 1) { //first
                if ($current > 1) {
                    $this->addListPoint(
                        $this->translator->trans('generic.previous'),
                        $this->routing->generate($url_name, array('page' => $current-1, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults)),
                        PREV
                    );
                }

                if ($i == $current) {
                    $this->htmlString .= sprintf('<li class="%s">%s</li>', SELECTED, $i);
                } else {
                    $this->addListPoint($i, $this->routing->generate($url_name, array('page' => $i, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults)));
                }
                if ($startPoint > 2) {
                    $this->addListPoint('&hellip;', '', 'aui-nav-truncation');
                }
            }

            //pages to render
            if (($i >= $startPoint) && ($i < $startPoint + $max)) {
                if ($i == $current) {
                    $this->htmlString .= sprintf('<li class="%s">%s</li>', SELECTED, $i);
                } else {
                    $this->addListPoint(
                        $i,
                        $this->routing->generate($url_name, array('page' => $i, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults))
                    );
                }
            }
            //---------------

            if ($i == $pages) { //last
                if ($startPoint + $max < $pages) {
                    $this->addListPoint('&hellip;', '', 'aui-nav-truncation');
                }

                if ($i == $current) {
                    $this->htmlString .= sprintf('<li class="%s">%s</li>', SELECTED, $i);
                } else {
                    $this->addListPoint(
                        $i,
                        $this->routing->generate($url_name, array('page' => $i, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults)),
                        LAST
                    );
                }

                if ($current < $pages) {
                    $this->addListPoint(
                        $this->translator->trans('generic.next'),
                        $this->routing->generate($url_name, array('page' => $current+1, 'sortBy' => $sortBy, 'order' => $order, 'nbResults' => $nbResults)),
                        NEXT
                    );
                }
            }
        }
        $this->htmlString = $this->htmlString."</ol>";

        return $this->htmlString;
    }

    /**
     * Calculates the Start-Point (index).
     *
     * @param int $pages
     * @param int $current
     * @param int $max
     *
     * @return int
     */
    protected function calculateStartpoint($pages, $current, $max)
    {
        $companionPages = floor($max / 2);
        $index = ($current - $companionPages) > 2 ? $current - $companionPages : 2;
        $index = ($index + $max > $pages - 1) ? $pages - $max : $index;

        return $index;
    }

    /**
     * Adds a List Point to the internal htmlString.
     *
     * @param type $text -> Text to render for the link
     * @param type $link -> URL to the page
     * @param type $class -> AUI class for styling
     */
    protected function addListPoint($text, $link = '', $class = '')
    {
        $this->htmlString .= sprintf('<li class="%s"><a href="%s">%s</a></li>', $class, $link, $text);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'auiPagerExtension';
    }
}
