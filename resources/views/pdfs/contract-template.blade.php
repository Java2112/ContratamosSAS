<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato {{ $contract->contract_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; line-height: 1.6; color: #333; margin: 20px 40px; }
        .header { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #1a56db; }
        .logo-placeholder { font-size: 24px; font-weight: bold; color: #1a56db; letter-spacing: 1px; }
        .contract-title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin-top: 10px; }
        
        .section { margin-top: 30px; text-align: justify; }
        .clause-title { font-weight: bold; text-decoration: underline; margin-bottom: 10px; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 20px; }
        .table-data th, .table-data td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .table-data th { background-color: #f3f4f6; width: 30%; }

        .signatures { margin-top: 80px; width: 100%; }
        .signature-box { width: 45%; float: left; text-align: center; border-top: 1px solid #333; padding-top: 10px; }
        .signature-box.right { float: right; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-placeholder">SISTEMA CONTRATAMOS S.A.S.</div>
        <div class="contract-title">Contrato Comercial de Prestación de Servicios</div>
        <div>Número de Contrato: <strong>{{ $contract->contract_number }}</strong></div>
        <div>Fecha de emisión: {{ $date }}</div>
    </div>

    <div class="section">
        <p>Entre los suscritos a saber, de una parte <strong>SISTEMA CONTRATAMOS S.A.S.</strong>, y por la otra parte la empresa aliada identificada en las declaraciones de este documento, convienen en celebrar el presente Contrato de Prestación de Servicios Comerciales bajo las siguientes condiciones y declaraciones:</p>
    </div>

    <table class="table-data">
        <tr>
            <th>Razón Social del Cliente:</th>
            <td>{{ $client->business_name }}</td>
        </tr>
        <tr>
            <th>Identificación ({{ $client->document_type }}):</th>
            <td>{{ $client->document_number }}</td>
        </tr>
        <tr>
            <th>Contacto Principal:</th>
            <td>{{ $client->contact_name }} ({{ $client->phone }})</td>
        </tr>
        <tr>
            <th>Dirección Comercial:</th>
            <td>{{ $client->address }}</td>
        </tr>
        <tr>
            <th>Vigencia del Contrato:</th>
            <td>Desde: {{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} 
                <br>Hasta: {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : 'Indefinido' }}</td>
        </tr>
        <tr>
            <th>Porcentaje de Administración (AIU):</th>
            <td style="font-weight: bold; font-size: 16px;">{{ $contract->administration_fee_percentage }}%</td>
        </tr>
    </table>

    <div class="section">
        <div class="clause-title">CLÁUSULAS DEL SERVICIO</div>
        <p><strong>PRIMERA - OBJETO:</strong> El presente contrato tiene por objeto regular las condiciones comerciales mediante las cuales la EMPRESA TEMPORAL prestará los servicios de provisión de personal en misión para EL CLIENTE, según los requerimientos y vacantes solicitadas a través de su Portal Empresarial.</p>
        
        <p><strong>SEGUNDA - TARIFA Y FACTURACIÓN:</strong> EL CLIENTE reconocerá a la EMPRESA TEMPORAL el porcentaje de administración estipulado en este documento ({{ $contract->administration_fee_percentage }}%) sobre el valor total de los salarios, prestaciones y demás recargos de nómina facturados mensualmente.</p>

        <p><strong>TERCERA - VIGENCIA:</strong> El contrato entrará en vigor a partir de la fecha de inicio indicada y se prorrogará automáticamente a menos de notificación expresa por escrito.</p>
        
        <p><em>Documento generado electrónicamente a través de la plataforma Sistema Contratamos.</em></p>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <br><br>
            <strong>EL CLIENTE</strong><br>
            Representante Legal<br>
            {{ $client->business_name }}<br>
            {{ $client->document_type }} {{ $client->document_number }}
        </div>
        <div class="signature-box right">
            <br><br>
            <strong>LA EMPRESA TEMPORAL</strong><br>
            Gerencia Comercial<br>
            SISTEMA CONTRATAMOS S.A.S.
        </div>
    </div>

    <div class="footer">
        Página 1 de 1. Sistema Contratamos S.A.S. &copy; {{ date('Y') }}. Generado el {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
