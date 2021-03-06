<?php

namespace Oktolab\Bundle\RentBundle\Tests\Extension;

use Oktolab\Bundle\RentBundle\Extension\AuiPaginationExtension;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Aui Pagination Extension Tests
 *
 * @author meh
 */
class AuiPaginationExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System Under Test.
     *
     * @var \Oktolab\Bundle\RentBundle\Extension\AuiPaginationExtension
     */
    protected $SUT = null;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator = null;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->translator = $this->getMock('\Symfony\Component\Translation\TranslatorInterface');
        $this->router = $this->getMock('\Symfony\Component\Routing\Generator\UrlGeneratorInterface');

        $this->SUT = new AuiPaginationExtension($this->translator, $this->router);
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Extension\AuiPaginationExtension', $this->SUT);
    }

    /**
     * @test
     */
    public function registersAuiPagerAsFunction()
    {
        $this->assertArrayHasKey('auiPager', $this->SUT->getFunctions());
        $this->assertArrayHasKey('aui_pager', $this->SUT->getFunctions());
    }

    /**
     * @test
     */
    public function htmlContainsList()
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', 3, 1));
        $this->assertNotCount(0, $crawler, 'Expected a valid DomDocument.');

        $ol = $crawler->filter('ol');
        $this->assertCount(1, $ol, 'Contains list <ol />.');
        $this->assertRegExp('/aui-nav/', $ol->attr('class'), '<ol /> contains class "aui-nav".');
        $this->assertRegExp('/aui-nav-pagination/', $ol->attr('class'), '<ol /> contains class "aui-nav-pagination".');
    }

    /**
     * @dataProvider htmlLinksProvider
     * @test
     */
    public function htmlContainsLinks(array $links, $nb, $currentPage, $max, $currentIndex)
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', $nb, $currentPage, $max));
        $this->assertNotCount(0, $crawler, 'Expected a valid DomDocument.');
        $this->assertCount(count($links)-1, $crawler->filter('li a'));

        foreach ($links as $key => $expected) {
            $xpath = ($key +1 === $currentIndex) ? sprintf('//ol/li[%d]', $key + 1) : sprintf('//ol/li[%d]/a', $key + 1);
            $this->assertSame(
                $expected,
                $crawler->filterXPath($xpath)->text(),
                sprintf('Expected "%s" at XPath "%s".', $expected, $xpath)
            );
        }
    }

    /**
     * @dataProvider nbLinksProvider
     * @test
     *
     * @param int       $nb
     * @param int       $curent
     * @param int       $max
     * @param int       $expectedNb
     * @param string    $xpath
     */
    public function htmlContainsNbLinks($nb, $current, $max, $expectedNb, $xpath)
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', $nb, $current, $max));
        $this->assertNotCount(0, $crawler, 'Expected a valid DomDocument.');

        $li = $crawler->filter('li > a');
        $this->assertCount($expectedNb, $li);
    }

    /**
     * @dataProvider nbLinksProvider
     * @test
     *
     * @param int       $nb
     * @param int       $curent
     * @param int       $max
     * @param int       $expectedNb
     * @param string    $xpath
     */
    public function currentLinkIsHighlighted($nb, $current, $max, $expectedNb, $xpath)
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', $nb, $current, $max));
        $this->assertNotCount(0, $crawler, 'Expected a valid DomDocument.');

        $this->assertCount(1, $crawler->filterXPath($xpath));
        $this->assertSame('aui-nav-selected', $crawler->filterXPath($xpath)->attr('class'));
    }

    /**
     * @test
     */
    public function htmlContainsOnlyOneLink()
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', 1, 1, 5));
        $this->assertNotCount(0, $crawler, 'Expected a valid DomDocument.');

        $this->assertCount(0, $crawler->filter('li'));
    }

    /**
     * @dataProvider willPrintDotsProvider
     * @test
     *
     * @param int       $nb
     * @param int       $current
     * @param string    $xpath
     */
    public function willPrintDots($nb, $current, $xpath)
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', $nb, $current, 5));
        $this->assertNotCount(0, $crawler, 'Expected a valid DomDocument.');

        $element = $crawler->filterXPath($xpath);
        $this->assertCount(1, $element);
        $this->assertSame(html_entity_decode("&hellip;", 2 | 48, 'UTF-8'), $element->text());
        $this->assertSame('aui-nav-truncation', $element->attr('class'));
    }

    /**
     * @dataProvider willPrintDotsProvider
     * @test
     *
     * @param int       $nb
     * @param int       $current
     * @param string    $xpath
     */
    public function willPrintDotsIsHightlighted($nb, $current, $xpath)
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', $nb, $current, 5));
        $this->assertNotCount(0, $crawler, 'Expected a valid DomDocument.');

        $this->assertCount(1, $crawler->filterXPath($xpath));
        $this->assertSame('aui-nav-truncation', $crawler->filterXPath($xpath)->attr('class'));
    }

    public function willPrintDotsProvider()
    {
        return array(
            array(20,  1, '//ol/li[7]'),   // 1, 2, 3, 4, 5, 6, [...], 20, next
            array(20, 10, '//ol/li[3]'),   // prev, 1, [...], 8, 9, 10, 11, 12, ..., 20, next
            array(20, 10, '//ol/li[9]'),   // prev, 1, ..., 8, 9, 10, 11, 12, [...], 20, next
        );
    }

    public function nbLinksProvider()
    {
        return array(
            // nb | current | max | expectedNb | XPath [highlighted]
            array( 3,  1, 5,  3, '//ol/li[1]'),     // [1], 2, 3, next
            array( 3,  3, 5,  3, '//ol/li[4]'),     // prev, 1, 2, [3]
            array( 3,  2, 5,  4, '//ol/li[3]'),     // prev, 1, [2], 3, next
            array(20,  1, 5,  8, '//ol/li[1]'),     // [1], 2, 3, 4, 5, 6, ..., 20, next
            array(20, 10, 5, 10, '//ol/li[6]'),     // prev, 1, ..., 8, 9, [10], 11, 12, ..., 20, next
            array(20, 20, 5,  8, '//ol/li[9]'),     // prev, 1, ..., 15, 16, 17, 18, 19, [20]
        );
    }

    public function htmlLinksProvider()
    {
        $prev = 'generic.previous';
        $next = 'generic.next';
        $hellip = html_entity_decode("&hellip;", 2 | 48, 'UTF-8');

        return array(
            array(array('1', '2', '3', $next), 3, 1, 5, 1),
            array(array($prev, '1', '2', '3','generic.next'), 3, 2, 5, 3),
            array(array($prev, '1', $hellip, '3', '4', '5', '6', '7', $hellip, '20', $next), 20, 5, 5, 6),
            array(array($prev, '1', $hellip, '15', '16', '17', '18', '19', '20'), 20, 20, 5, 9),
            array(array($prev, '1', $hellip, '15', '16', '17', '18', '19', '20', $next), 20, 19, 5, 8),
            array(array($prev, '1', $hellip, '15', '16', '17', '18', '19', '20', $next), 20, 18, 5, 7),
            array(array($prev, '1', $hellip, '15', '16', '17', '18', '19', '20', $next), 20, 17, 5, 6),
            array(array($prev, '1', $hellip, '14', '15', '16', '17', '18', $hellip, '20', $next), 20, 16, 5, 6),
            array(array($prev, '1', $hellip, '15', '16', '17', $hellip, '20', $next), 20, 16, 3, 5),
            array(array($prev, '1', $hellip, '17', '18', '19', '20'), 20, 20, 3, 7),
        );
    }

    protected function trainTheRouter()
    {
        $this->router->expects($this->any())
                ->method('generate')
                ->with($this->equalTo('RENT_URI'))
                ->will($this->returnValue('/dashboard'));
    }

    protected function trainTheTranslator()
    {
        $this->translator->expects($this->any())
                ->method('trans')
                ->will($this->returnArgument(0));
    }
}
