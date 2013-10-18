<?php

/**
 * DOKU: http://mpdf1.com/manual/index.php?tid=256
 */
namespace Oktolab\Bundle\RentBundle\Model;

use TFox\MpdfPortBundle\Service\MpdfService;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Oktolab\Bundle\RentBundle\Model\Event\EventManager;
use Oktolab\Bundle\RentBundle\Model\SettingService;
use Oktolab\Bundle\RentBundle\Entity\CompanySetting;


/**
 * Description of rentSheetPdfCreator
 *
 * @author rs
 */
class RentSheetPdfCreator
{
    private $mPDFS;
    private $translator;
    private $html;
    private $eventManager;
    private $companySettings;

    public function __construct(MpdfService $mpdfS, Translator $translator, EventManager $eventManager, SettingService $settingService) {
        $this->mPDFS = $mpdfS;
        $this->translator = $translator;
        $this->eventManager = $eventManager;
        $this->companySettings = new CompanySetting();
        $this->companySettings->setWithArray($settingService->get('company'));
    }

    public function createRentPDF(Event $event)
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
                  text-align:right;
             }
             table {
                border-collapse:collapse;
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
                float:left;
             }
             .returnText {
                width: 50%;
                text-align: right;
             }
             .lentSignature {
                width: 50%;
                float:left;
             }
             .returnSignature {
                widht: 50%;
                text-align: right;
             }
             .partTitle {
                font-weight: bold;
             }
             .textStripe {
                width:100%;
             }
             .signatureStripe {
                width:100%;
             }
             .signatureStripe {
                width:100%;
             }
             "
        );

        $this->addHeader($event);
        $this->addSignatureStrip();
        $this->addTable($event);
        $this->addFooter();
        return $this->mPDFS->generatePdfResponse($this->html);
    }

    //http://mpdf1.com/manual/index.php?tid=77
    private function addTable(Event $event)
    {
        $itemsForTable = $this->eventManager->convertEventObjectsToEntites($event->getObjects());

        $this->openTag("table");
        $this->openTag("thead");
        $this->openTag("tr");
        $this->addBlock($this->translator->trans('inventory.item.item'), "th", "item");
        $this->addBlock($this->translator->trans('inventory.item.barcode'), "th", "barcode");
        $this->closeTag("tr");
        $this->closeTag("thead");
        $this->openTag("tbody");
        foreach ($itemsForTable as $item) {
            $this->openTag("tr");
            $this->addBlock($item->getTitle(), "td", "title");
            $this->addBlock($item->getBarcode(), "td", "barcode");
            $this->closeTag("tr");
        }
        $this->closeTag("tbody");
        $this->closeTag("table");
    }

    private function addHeader(Event $event)
    {
        $this->addBlock(
            $this->translator->trans('event.pdf.costUnitName', array('%costUnitName%' => $event->getCostunit()->getName())).": <br>".
            $this->translator->trans('event.pdf.pickUpName', array('%pickUpName%' => $event->getContact()->getName()))."<br>".
            $this->translator->trans('event.pdf.lentAt', array('%rentFromDate%' => $event->getBegin()))."<br>".
            $this->translator->trans('event.pdf.planReturnAt', array('%rentTillDate%' => $event->getEnd())),
            "p",
            "rightTop"
        );

        $this->addBlock("Verleihschein", "h1");
        $this->addBlock(
            $this->companySettings->getName()."<br>".
            $this->companySettings->getAdress()."<br>".
            $this->companySettings->getPlz()." ".$this->companySettings->getPlace(),
            "address"
        );
    }

    private function addSignatureStrip()
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

    private function addLentText()
    {
        $this->openTag('div class=lentText');

        $this->addBlock("Ausgabe", "p", "partTitle");
        $this->addBlock(
            $this->translator->trans(
                'event.pdf.lentgivenBy',
                array('%CompanyName%' => $this->companySettings->getName())
            )
        );
        $this->addBlock($this->companySettings->getAdditionalText());
        $this->closeTag('div');
    }

    private function addReturnText()
    {
        $this->openTag('div class=returnText');

        $this->addBlock("Rücknahme", "p", "partTitle");
        $this->addBlock($this->translator->trans('event.pdf.reallyReturnedAt'));
        $this->addBlock($this->translator->trans('event.pdf.takenBackBy'));

        $this->closeTag('div');
    }

    private function addLentSignature()
    {
        $this->openTag('div class=lentSignature');
        $this->addBlock($this->translator->trans('event.pdf.signatureRenter'));
        $this->closeTag('div');
    }

    private function addReturnSignature()
    {
        $this->openTag('div class=returnSignature');
        $this->addBlock($this->translator->trans('event.pdf.signatureCompany'));
        $this->closeTag('div');
    }

    private function addFooter()
    {
        $this->openTag('htmlpagefooter name="myFooter"');
        $this->addBlock("{DATE j.m.Y} ".$this->translator->trans('event.pdf.Page')." {PAGENO} / {nbpg}", "div", "footer-right");
        $this->closeTag('htmlpagefooter');
    }

    private function addBarcode($barcode)
    {
        //http://mpdf1.com/manual/index.php?tid=407
    }

    private function addBlock($blockText, $tag="p", $class="", $name="")
    {
        $this->html.='<'.$tag.' class="'.$class.'" name="'.$name.'">'.$blockText.'</'.$tag.'>';
    }

    private function openTag($tag)
    {
        $this->html.="<".$tag.">";
    }

    private function closeTag($tag)
    {
        $this->html.="</".$tag.">";
    }

    private function addString($string)
    {
        $this->html.=$string;
    }

    private function addStyle($style)
    {
        $this->addBlock($style, "style");
    }
}
