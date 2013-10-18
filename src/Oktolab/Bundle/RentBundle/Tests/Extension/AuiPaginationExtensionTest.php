<?php

namespace Oktolab\Bundle\RentBundle\Tests\Extension;

use Oktolab\Bundle\RentBundle\Extension\AuiPaginationExtension;
use Symfony\Component\DomCrawler\Crawler;

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
        $this->assertNotCount(0, $crawler, 'Expected valid DomDocument');

        $ol = $crawler->filter('ol');
        $this->assertCount(1, $ol, 'Contains list <ol />');
        $this->assertRegExp('/aui-nav/', $ol->attr('class'), '<ol /> contains class "aui-nav"');
        $this->assertRegExp('/aui-nav-pagination/', $ol->attr('class'), '<ol /> contains class "aui-nav"-pagination');
    }

    /**
     * @test
     */
    public function htmlContainsLinks()
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', 3, 1, 5));
        $this->assertNotCount(0, $crawler, 'Expected valid DomDocument');

        $li = $crawler->filter('li');
        $this->assertCount(4, $li, 'Contains 3 List-Elements');

        $a = $li->filter('a')->first();
        $this->assertCount(1, $a, 'List element contains Link');
        $this->assertSame('1', $li->text(), 'First link is "1"');

        $li = $li->nextAll();
        $a = $li->filter('a')->first();
        $this->assertCount(1, $a, 'List element contains Link');
        $this->assertSame('2', $li->text(), 'Next link is "2"');

        $li = $li->nextAll();
        $a = $li->filter('a')->first();
        $this->assertCount(1, $a, 'List element contains Link');
        $this->assertSame('3', $li->text(), 'Next link is "3"');

        $li = $li->nextAll();
        $a = $li->filter('a')->first();
        $this->assertCount(1, $a, 'List element contains Link');
        $this->assertSame('generic.next', $li->text(), 'Last link is "generic.next"');
    }

    /**
     * @dataProvider htmlContainsNbLinksProvider
     * @test
     */
    public function htmlContainsNbLinks($nb, $current, $max, $expected)
    {
        $this->trainTheRouter();
        $this->trainTheTranslator();

        $crawler = new Crawler($this->SUT->getPagerHtml('RENT_URI', $nb, $current, $max));
        $this->assertNotCount(0, $crawler, 'Expected valid DomDocument');

        $li = $crawler->filter('li > a');
        $this->assertCount($expected, $li);
    }

    public function htmlContainsNbLinksProvider()
    {
        return array(
            array(3, 1, 5, 4),      // 1, 2, 3, next
            array(3, 3, 5, 4),      // prev, 1, 2, 3
            array(3, 2, 5, 5),      // prev, 1, 2, 3, next
            array(20, 1, 5, 9),     // 1, 2, 3, 4, 5, 6, ..., 20, next
            array(20, 10, 5, 11),   // prev, 1, ..., 8, 9, 10, 11, 12, ..., 20, next
            array(20, 20, 5, 9),    // prev, 1, ..., 15, 16, 17, 18, 19, 20
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
