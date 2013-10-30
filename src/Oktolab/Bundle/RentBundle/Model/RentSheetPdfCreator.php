<?php

namespace Oktolab\Bundle\RentBundle\Model;

use TFox\MpdfPortBundle\Service\MpdfService;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Model\Event\EventManager;
use Oktolab\Bundle\RentBundle\Model\SettingService;
use Oktolab\Bundle\RentBundle\Entity\CompanySetting;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Description of rentSheetPdfCreator
 *
 * @author rs
 * @see http://mpdf1.com/manual/index.php?tid=256
 */
class RentSheetPdfCreator
{

    /**
     * Contains the HTML
     *
     * @var string
     */
    protected $html;

    /**
     * @var \TFox\MpdfPortBundle\Service\MpdfService
     */
    protected $mPDFS;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\EventManager
     */
    protected $eventManager;

    /**
     * @var \Oktolab\Bundle\RentBundle\Entity\CompanySetting
     */
    protected $company;

    /**
     * Constructor.
     *
     * @param \TFox\MpdfPortBundle\Service\MpdfService $mpdfS
     * @param \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
     * @param \Oktolab\Bundle\RentBundle\Model\Event\EventManager $eventManager
     * @param \Oktolab\Bundle\RentBundle\Model\SettingService $settingService
     */
    public function __construct(MpdfService $pdf, Translator $trans, EventManager $em, SettingService $settings)
    {
        $this->mPDFS        = $pdf;
        $this->translator   = $trans;
        $this->eventManager = $em;

        $this->company = new CompanySetting();
        $this->company->fromArray($settings->get('company'));
    }

    /**
     * Generates the PDF
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Event $event
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generatePdf(Event $event)
    {
        $this->loadStyles();

        $this->addHeader($event);
        $this->addSignatureStrip();
        $this->addTable($event);
        $this->addFooter();

        return $this->mPDFS->generatePdfResponse($this->html);
    }

    /**
     *
     * @see http://mpdf1.com/manual/index.php?tid=77
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Event $event
     */
    protected function addTable(Event $event)
    {
        $objects = $this->eventManager->convertEventObjectsToEntites($event->getObjects());

        $this->openTag('table');
        $this->openTag('thead');
        $this->openTag('tr');
        $this->addBlock($this->trans('inventory.item.item'), 'th', 'item');
        $this->addBlock($this->trans('inventory.item.barcode'), 'th', 'barcode');
        $this->closeTag('tr');
        $this->closeTag('thead');
        $this->openTag('tbody');

        foreach ($objects as $object) {
            $this->openTag('tr');
            $this->addBlock($object->getTitle(), 'td', 'title');
            $this->addBlock($object->getBarcode(), 'td', 'barcode');
            $this->closeTag('tr');
        }

        $this->closeTag('tbody');
        $this->closeTag('table');
    }

    protected function addHeader(Event $event)
    {
        $content = sprintf('%s: <br />%s<br />%s<br />%s',
            $this->trans('event.pdf.costUnitName', array('%costUnitName%' => $event->getCostunit()->getName())),
            $this->trans('event.pdf.pickUpName', array('%pickUpName%' => $event->getContact()->getName())),
            $this->trans('event.pdf.lentAt', array('%rentFromDate%' => $event->getBegin()->format('Y-m-d'))),
            $this->trans('event.pdf.planReturnAt', array('%rentTillDate%' => $event->getEnd()->format('Y-m-d')))
        );

        $this->addBlock($content, 'p', 'rightTop');
        $this->addBlock('Verleihschein', 'h1');

        $address = sprintf(
            '%s<br/>%s<br/>%s %s',
            $this->company->getName(),
            $this->company->getAddress(),
            $this->company->getPostalCode(),
            $this->company->getCity()
        );

        $this->addBlock($address, 'address');
    }

    protected function addSignatureStrip()
    {
        $this->openTag('div class=signatureStrip');
        $this->openTag('div class=textStripe');
        $this->addLentText();
        $this->addReturnText();
        $this->closeTag('div');
        $this->openTag('div class=signatureStripe');
        $this->addLentSignature();
        $this->addReturnSignature();
        $this->closeTag('div');
        $this->closeTag('div');
    }

    protected function addLentText()
    {
        $this->openTag('div class=lentText');

        $this->addBlock('Ausgabe', 'p', 'partTitle');
        $this->addBlock($this->trans('event.pdf.lentgivenBy', array('%CompanyName%' => $this->company->getName())));

        $this->addBlock($this->company->getAdditionalText());
        $this->closeTag('div');
    }

    protected function addReturnText()
    {
        $this->openTag('div class=returnText');

        $this->addBlock('Rücknahme', 'p', 'partTitle');
        $this->addBlock($this->trans('event.pdf.reallyReturnedAt'));
        $this->addBlock($this->trans('event.pdf.takenBackBy'));

        $this->closeTag('div');
    }

    protected function addLentSignature()
    {
        $this->openTag('div class=lentSignature');
        $this->addBlock($this->trans('event.pdf.signatureRenter'));
        $this->closeTag('div');
    }

    protected function addReturnSignature()
    {
        $this->openTag('div class=returnSignature');
        $this->addBlock($this->trans('event.pdf.signatureCompany'));
        $this->closeTag('div');
    }

    protected function addFooter()
    {
        $this->openTag('htmlpagefooter name="myFooter"');

        $content = sprintf('{DATE j.m.Y} %s {PAGENO} / {nbpg}', $this->trans('event.pdf.Page'));
        $this->addBlock($content, 'div', 'footer-right');

        $this->closeTag('htmlpagefooter');
    }

    protected function addBlock($blockText, $tag='p', $class='', $name='')
    {
        $this->html .= sprintf('<%s class="%s" name="%s">%s</%1$s>', $tag, $class, $name, $blockText);
    }

    protected function openTag($tag)
    {
        $this->html .= sprintf('<%s>', $tag);
    }

    protected function closeTag($tag)
    {
        $this->html .= sprintf('</%s>', $tag);
    }

    protected function addString($string)
    {
        $this->html .= $string;
    }

    protected function addStyle($style)
    {
        $this->addBlock($style, 'style');
    }

    /**
     * Loads the CSS styles.
     *
     * @TODO: Find a better solution for this ...
     */
    protected function loadStyles()
    {
        $this->addStyle(
            "@page {
                  even-footer-name: html_myFooter;
                  odd-footer-name: html_myFooter;
             }
             body {
                font-family: 'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', 'Helvetica';
             }
             .footer-right {
                  text-align: right;
             }
             table {
                border-collapse: collapse;
                border: 0;
                width: 100%;
             }
             td {
                border-bottom: 0.1mm solid #000000;
             }
             .barcode {
                text-align: right;
             }
             .item {
                text-align: left;
             }
             .rightTop {
                text-align: right;
                position: fixed;
                top: 0px;
                right: 0px;
             }
             .lentText {
                width: 50%;
                float: left;
             }
             .returnText {
                width: 50%;
                text-align: right;
             }
             .lentSignature {
                width: 50%;
                float: left;
             }
             .returnSignature {
                widht: 50%;
                text-align: right;
             }
             .partTitle {
                font-weight: bold;
             }
             .textStripe,
             .signatureStripe {
                width: 100%;
             }
             "
        );
    }

    /**
     * Short-Cut method to translate a message.
     *
     * @see Symfony\Component\Translation\TranslatorInterface::trans
     *
     * @return string
     */
    protected function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}
