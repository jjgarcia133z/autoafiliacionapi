<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
class PdfController extends Controller
{
    public function pdfToHtml(Request $request)
    {

      //  $pdf_file = public_path('pdfs/1.pdf');
        $pdfUrl = asset('pdfs/1.pdf');



// chemin du fichier PDF à convertir

// inclure la bibliothèque Poppler
require_once 'Poppler/autoload.php';

// créer un objet Poppler
$poppler = new Poppler\Poppler();
dd( $pdf_file);

// extraire le contenu du fichier PDF
$content = $poppler->getText($pdf_file);

// convertir le contenu en HTML
$html = '<html><head></head><body>' . nl2br(htmlspecialchars($content)) . '</body></html>';

// afficher le résultat



















    }
}
