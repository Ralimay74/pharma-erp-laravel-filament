<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta Lote {{ $lote->numero_lote }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            min-height: 100vh;
        }
        
        .botones {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .boton {
            background: #1e40af;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        .boton:hover {
            background: #1e3a8a;
        }
        
        .boton.cerrar {
            background: #6b7280;
        }
        
        .boton.cerrar:hover {
            background: #4b5563;
        }
        
        .etiqueta-wrapper {
            transform: scale(2.5);
            transform-origin: top center;
            margin-top: 20px;
        }
        
        .etiqueta {
            width: 100mm;
            height: 50mm;
            padding: 2.5mm;
            border: 1px solid #000;
            background: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 0.5mm solid #000;
            padding-bottom: 1.5mm;
            margin-bottom: 2mm;
        }
        
        .empresa {
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            line-height: 1.2;
        }
        
        .empresa-sub {
            font-size: 6pt;
            color: #666;
        }
        
        .tipo-producto {
            font-size: 6pt;
            text-align: right;
            color: #666;
            line-height: 1.2;
        }
        
        .cuerpo {
            display: flex;
            align-items: center;
            flex: 1;
            gap: 2mm;
        }
        
        .info {
            flex: 1.2;
            min-width: 0;
        }
        
        .producto-nombre {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 1.5mm;
            line-height: 1.2;
        }
        
        .info-linea {
            font-size: 7pt;
            margin-bottom: 0.8mm;
            display: flex;
            align-items: baseline;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 20mm;
            color: #333;
        }
        
        .info-valor {
            flex: 1;
        }
        
        .barcode-container {
            flex: 0.8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 0;
        }
        
        /* Imagen PNG del barcode: se imprime siempre bien */
        .barcode-container img {
            width: 100%;
            height: auto;
            max-height: 22mm;
            display: block;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
        
        .barcode-texto {
            font-size: 6pt;
            font-family: 'Courier New', monospace;
            margin-top: 1mm;
            letter-spacing: 0.5px;
            text-align: center;
            font-weight: bold;
        }
        
        .footer {
            border-top: 0.5mm solid #000;
            padding-top: 1.5mm;
            display: flex;
            justify-content: space-between;
        }
        
        .footer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        
        .footer-label {
            font-weight: bold;
            color: #666;
            font-size: 5pt;
            margin-bottom: 0.5mm;
            text-transform: uppercase;
        }
        
        .footer-valor {
            font-size: 7pt;
            font-weight: bold;
        }
        
        .caducidad-alerta {
            color: #dc2626;
        }
        
        @media print {
            @page {
                size: 100mm 50mm;
                margin: 0;
            }
            
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .botones {
                display: none !important;
            }
            
            .etiqueta-wrapper {
                transform: none;
                margin-top: 0;
            }
            
            .etiqueta {
                border: 1px solid #000;
                box-shadow: none;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="botones">
        <button class="boton" onclick="window.print()">🖨️ Imprimir Etiqueta</button>
        <button class="boton cerrar" onclick="window.close()">✖ Cerrar</button>
    </div>
    
    <div class="etiqueta-wrapper">
        <div class="etiqueta">
            <div class="header">
                <div>
                    <div class="empresa">PharmaERP</div>
                    <div class="empresa-sub">Laboratorio Farmacéutico</div>
                </div>
                <div class="tipo-producto">
                    MEDICAMENTO<br>
                    USO HUMANO
                </div>
            </div>
            
            <div class="cuerpo">
                <div class="info">
                    <div class="producto-nombre">{{ $producto->nombre ?? 'Producto no disponible' }}</div>
                    <div class="info-linea">
                        <span class="info-label">Concentración:</span>
                        <span class="info-valor">{{ $producto->concentracion ?? '-' }}</span>
                    </div>
                    <div class="info-linea">
                        <span class="info-label">Forma:</span>
                        <span class="info-valor">{{ $producto->forma_farmaceutica ?? '-' }}</span>
                    </div>
                    <div class="info-linea">
                        <span class="info-label">Presentación:</span>
                        <span class="info-valor">{{ $producto->presentacion ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="barcode-container">
                    <img src="{{ $barcodeBase64 }}" alt="Código de barras {{ $lote->numero_lote }}">
                    <div class="barcode-texto">{{ $lote->numero_lote }}</div>
                </div>
            </div>
            
            <div class="footer">
                <div class="footer-item">
                    <span class="footer-label">Nº Lote</span>
                    <span class="footer-valor">{{ $lote->numero_lote }}</span>
                </div>
                <div class="footer-item">
                    <span class="footer-label">Fabricación</span>
                    <span class="footer-valor">{{ $lote->fecha_fabricacion->format('d/m/Y') }}</span>
                </div>
                <div class="footer-item">
                    <span class="footer-label">Caducidad</span>
                    <span class="footer-valor {{ $lote->fecha_caducidad < now()->addMonths(6) ? 'caducidad-alerta' : '' }}">
                        {{ $lote->fecha_caducidad->format('d/m/Y') }}
                    </span>
                </div>
                <div class="footer-item">
                    <span class="footer-label">Cantidad</span>
                    <span class="footer-valor">{{ number_format($lote->cantidad_producida, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
