<?php
namespace Oktolab\Bundle\RentBundle\Extension;

const first = "aui-nav-first";
const selected ="aui-nav-selected";
const last = "aui-nav-last";
const prev = "aui-nav-previous";
const next = "aui-nav-next";
const truncation = "aui-nav-truncation";

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
/**
 * AttachmentExtension
 */
class AuiPaginationExtension extends \Twig_Extension
{
    private $translator;
    private $routing;
    private $htmlString;

    public function __construct(Translator $translator, Router $router)
    {
        $this->translator = $translator;
        $this->routing = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'auiPager' => new \Twig_Function_Method(
                $this,
                'getPagerHtml',
                array(
                'is_safe' => array('html')
                )
            )
        );
    }

    /**
     * Renders a nice Paging Navigation like "prev 1 ... 3 4 5 6 7 ... 1 next"
     * Including: Next and Previous link, last and first link, leading and following pages
     * and indicator to more pages left or to the right
     *
     * @param type $url_name -> The URL to the Page
     * @param type $pages -> All Pages available
     * @param type $current -> The Current page you are at
     * @param type $max -> How Many companions including the current Page to render
     * @return string -> the complete Ordered List as HTML with AUI formatting classes.
     */
    public function getPagerHtml($url_name, $pages, $current, $max = 5)
    {
        $this->htmlString = '<ol class="aui-nav aui-nav-pagination">';

        $companionPages = floor($max/2);
        $startPoint = $current - $companionPages;

        if ($startPoint < 2) {
            $startPoint = 2;
        }
        if ($startPoint + $max > $pages - 1) {
            $startPoint = $pages - $max;
        }

        if ($pages == 1) { //No pager needed
            return '';
        }

        if ($pages < $max) { //No truncating needed, render all pages
            for ($i =1; $i <= $pages; $i++) {
                if ($i == 1) {
                    if ($current > 1) {
                        $this->addListPoint(
                            $this->translator->trans('generic.previous'),
                            $this->routing->generate($url_name, array('page' => $current-1)),
                            prev);
                    }
                }

                if ($i == $current) {
                    $this->addListPoint($i, $this->routing->generate($url_name, array('page' => $i)), selected);
                } else {
                    $this->addListPoint($i, $this->routing->generate($url_name, array('page' => $i)));
                }

                if ($i == $pages) {
                    if ($current < $pages) {
                        $this->addListPoint(
                            $this->translator->trans('generic.next'),
                            $this->routing->generate($url_name, array('page' => $current+1)),
                            next
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
                        $this->routing->generate($url_name, array('page' => $current-1)),
                        prev);
                }

                if ($i == $current) {
                    $this->addListPoint($i, $this->routing->generate($url_name, array('page' => $i)), selected);
                } else {
                    $this->addListPoint($i, $this->routing->generate($url_name, array('page' => $i)));
                }
                if ($startPoint > 2) {
                    $this->addListPoint("&hellip;", "", truncation);
                }
            }

            //pages to render
            if (($i >= $startPoint) && ($i < $startPoint + $max)) {
                if ($i == $current ) {
                    $this->addListPoint(
                        $i,
                        $this->routing->generate($url_name, array('page' => $i)),
                        selected
                    );
                } else {
                    $this->addListPoint(
                        $i,
                        $this->routing->generate($url_name, array('page' => $i))
                    );
                }
            }
            //---------------

            if ($i == $pages) { //last
                if ($startPoint + $max < $pages) {
                    $this->addListPoint("&hellip;");
                }

                if ($i == $current) {
                    $this->addListPoint(
                        $i,
                        $this->routing->generate($url_name, array('page' => $i)),
                        selected
                    );
                } else {
                    $this->addListPoint(
                        $i,
                        $this->routing->generate($url_name, array('page' => $i)),
                        last
                    );
                }

                if ($current < $pages) {
                    $this->addListPoint(
                        $this->translator->trans('generic.next'),
                        $this->routing->generate($url_name, array('page' => $current+1)),
                        next
                    );
                }
            }
        }
        $this->htmlString = $this->htmlString."</ol>";

        return $this->htmlString;
    }

    /**
     * adds a List Point to the internal htmlString.
     *
     * @param type $text -> Text to render for the link
     * @param type $link -> URL to the page
     * @param type $class -> AUI class for styling
     */
    private function addListPoint($text, $link ="", $class="")
    {
        $this->htmlString = sprintf('%s<li class="%s"><a href="%s">%s</a></li>', $this->htmlString, $class, $link, $text);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'auiPagerExtension';
    }

}