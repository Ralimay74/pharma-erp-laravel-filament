<?php

namespace App\Http\Controllers;

use App\Models\LoteProduccion;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class EtiquetaController extends Controller
{
    public function imprimir(LoteProduccion $lote)
    {
        $generator = new BarcodeGeneratorPNG();
        
        // Generar barcode como imagen PNG en base64
        $barcodeImage = $generator->getBarcode(
            $lote->numero_lote, 
            $generator::TYPE_CODE_128,
            2,    // factor de escala (más grueso para mejor lectura)
            50    // altura en píxeles
        );
        
        // Convertir a base64 para embeber en HTML
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeImage);

        $producto = $lote->productoTerminado;

        return view('etiquetas.lote', compact('lote', 'producto', 'barcodeBase64'));
    }
}
