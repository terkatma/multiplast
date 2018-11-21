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
        $year = date("Y");

        /* @var Customer $customer */
        $pdf = $this->initPdf("", "P", false);
        $pdf->AddPage();
        $pdf->Image(
            //TODO neexistujici soubor - pozadi pdf
            __INVITATION_BACKGROUNDS_DIR__ . $year . "/" . $customer->language . ".png",
            0,
            0, 210, 297, 'PNG', '', '', false, 300, '', false, false, 0);

        $pdf->Ln(30.7);

        $date = $customer->reply_deadline->format('j. n. Y');
        if ($customer->language == 'cz') {
            $text = "               " . ($customer->is_woman ? "Vážená paní" : "Vážený pane") . " " . $customer->addressing . ",";
            $pdf->Write(13.5, $text);
            $pdf->Ln(204.6);
            $pdf->Write(13.5, "                                        $date");
        } else {
            $text = "               " . ($customer->is_woman ? "Dear Mrs" : "Dear Mr") . " " . $customer->addressing . ",";
            $pdf->Write(13.5, $text);
            $pdf->Ln(204.6);
            $pdf->Write(13.5, "                                         $date");
        }

        if (!is_dir(__INVITATIONS_DIR__ . "/" . $year . "/")) {
            mkdir(__INVITATIONS_DIR__ . "/" . $year . "/", 0777, true);
        }

        $pdf->Output(__INVITATIONS_DIR__ . "/" . $year . "/" . $customer->id . ".pdf", "F");
    }

    public function generateTicketPdf($customer)
    {
        $year = date("Y");
        /* @var Customer $customer */
        $pdf = $this->initPdf("", "P", false);
        $pdf->AddPage();
        $pdf->Image(
            __TICKET_BACKGROUNDS_DIR__ . $year . "/" . $customer->language . "_" . $customer->ticket_count . ".jpg",
            0,
            0, 210, 297, 'JPG', '', '', false, 300, '', false, false, 0);

        $pdf->Ln(30.7);

        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );

        $pdf->write2DBarcode($customer->hash, 'QRCODE,L', 146, 27, 43, 43, $style, 'N');

        if (!is_dir(__TICKETS_DIR__ . "/" . $year . "/")) {
            mkdir(__TICKETS_DIR__ . "/" . $year . "/", 0777, true);
        }

        $pdf->Output(__TICKETS_DIR__ . "/" . $year . "/" . $customer->id . ".pdf", "F");
    }

    private function initPdf($title = "", $orientation = "P", $addPage = true, $format = "A4")
    {
        $pdf = new TCPDF($orientation, PDF_UNIT, $format, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Multiplast');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);


        $pdf->SetMargins(0, 0, 0, 0);
        $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(false, 0);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont("DeJaVuSans",'', 11.5, true);

        if ($addPage) {
            $pdf->AddPage();

            if ($title !== "") {
                $pdf->Write(0, $title, '', 0, 'L', true, 0, false, false, 0);
            }

            $pdf->Ln(5);
        }

        return $pdf;
    }
}
