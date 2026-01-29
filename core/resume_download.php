
<?php

// echo "Download resume";
include_once('resume_download_html.php');

$date = date('d/m/Y');
global $htmldata;
// echo ($htmldata); die;

require('core/mpdf-autoload.php');
// require('core/mpdf/src/Strict.php');
require('core/mpdf/src/Mpdf.php');

$mpdf = new \Mpdf\Mpdf();
// $mpdf->WriteHTML('<h1>Hello World!</h1>');
$mpdf->WriteHTML($htmldata);
$mpdf->Output('my-pdf.pdf', 'I');
/* $tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// $tcpdf->AddPage();

// convert TTF font to TCPDF format and store it on the fonts folder
// $fontname = TCPDF_FONTS::addTTFfont(TAOH_PLUGIN_PATH.'/core/TCPDF-main/fonts/Autography.ttf', 'TrueTypeUnicode', '', 96);

// use the font
// $tcpdf->SetFont($fontname, '', 16, '', false);

// set default monospaced font
// $tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf

$tcpdf->SetTitle('Resume');
// set header and footer in pdf
$tcpdf->setPrintHeader(false);
$tcpdf->setPrintFooter(false);

// set margins
$tcpdf->SetMargins(10, 10, 10, 10);
$tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);


// $tcpdf->setListIndentWidth(3);

// set auto page breaks
// $tcpdf->SetAutoPageBreak(TRUE, 10);
// $tcpdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$tcpdf->SetAutoPageBreak(TRUE, 15);
$tcpdf->AddPage();

// set image scale factor
// $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// $tcpdf->SetFont('times', '', 10.5);

if (!empty($htmldata)) {
    // $htmldata = '<table><thead><tr><th>Company</th><th>Role</th></tr></thead><tbody>';
    $tcpdf->writeHTML($htmldata, true, false, false, false, '');
    // $tcpdf->AddPage();
} else {
    // Optionally log or fallback
    $tcpdf->writeHTML('<p>No content found.</p>', true, false, false, false, '');
}
// $tcpdf->writeHTML($htmldata, true, false, false, false, '');
// $tcpdf->writeHTMLCell(0, 0, '', '', $htmldata, 0, 1, 0, true, '', true);
// $tcpdf->writeHTML($htmldata, true, false, true, false, '');

//Close and output PDF document
ob_end_clean();
$tcpdf->Output('resume.pdf', 'I'); */


?>