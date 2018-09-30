<?php

namespace Utils\PDFExport;


use app\entities\Customer;
use DB\InvitationsRepository;
use Nette;
use Nette\Utils\Strings;
use TCPDF;
/**
 * Class for generating PDFs
 *
 * @package Utils\PDFExport
 */
class PDFExport
{
    public function generateInvitationPdf($customer)
    {
        /* @var Customer $customer*/
        $pdf = $this->initPdf("", "P", false);
        $pdf->AddPage();
        $pdf->Image(
            __INVITATION_BACKGROUNDS_DIR__. date("Y") . "/" . $customer->language . ".jpg",
            0,
            0,210, 297, 'JPG', '', '', false, 300, '', false, false, 0);
        $pdf->Ln(19);
        $text = "  ".($customer->is_woman?"Vážená paní":"Vážený pane")." ".$customer->addressing.",";
        $pdf->Write(13.5, $text);

        $pdf->Ln(190);
        $pdf->Write(13.5, "                           $customer->reply_deadline");
        $pdf->Output(__INVITATIONS_DIR__."/" . date("Y") . "/" . $customer->id . ".pdf", "F");
    }

    private function initPdf($title = "", $orientation = "P", $addPage = true, $format = "A4")
    {
        $pdf = new TCPDF($orientation, PDF_UNIT, $format, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Multiplast');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);


        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont("DeJaVuSans",'', 11.5, true);

        if ($addPage) {
            $pdf->AddPage();

            if ($title !== "") {
                $pdf->Write(0, $title, '', 0, 'L', true, 0, false, false, 0);
            }

            $pdf->Ln(5);
            //$pdf->SetFont('courier','', 13.5);
            //$pdf->setFontSubsetting(true);
            //$pdf->addFont(__FONTS_DIR__ . "arial.ttf", 'TrueTypeUnicode', 32);
        }

        return $pdf;
    }
}
