<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Presentación - {{ $candidate->first_name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { background-color: #06302B; color: #fff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; color: #00DF81; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #00BEB5; }
        .content { padding: 20px; }
        h2 { border-bottom: 2px solid #00DF81; padding-bottom: 5px; color: #06302B; }
        .data-table { w-full; margin-top: 15px; border-collapse: collapse; width: 100%; }
        .data-table th, .data-table td { padding: 10px; border: 1px solid #eee; text-align: left; }
        .data-table th { background-color: #f9f9f9; width: 30%; color: #555; font-weight: bold; }
        .footer { text-align: center; font-size: 11px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRATAMOS S.A.S</h1>
        <p>Informe de Perfilamiento y Selección</p>
    </div>

    <div class="content">
        <h2>Información de la Vacante</h2>
        <table class="data-table">
            <tr><th>Cargo:</th><td>{{ $vacancy->title }}</td></tr>
            <tr><th>Cliente:</th><td>{{ $vacancy->client->business_name ?? 'Confidencial' }}</td></tr>
            <tr><th>Fecha de Envío:</th><td>{{ date('d-m-Y') }}</td></tr>
        </table>

        <h2>Datos del Candidato Recomendado</h2>
        <table class="data-table">
            <tr><th>Nombre Completo:</th><td>{{ $candidate->first_name }} {{ $candidate->last_name }}</td></tr>
            <tr><th>Documento:</th><td>{{ $candidate->document_type }} {{ $candidate->document_number }}</td></tr>
            <tr><th>Contacto:</th><td>{{ $candidate->email }} | {{ $candidate->phone }}</td></tr>
            <tr><th>Fuente:</th><td>{{ $candidate->source ?? 'N/A' }}</td></tr>
        </table>

        <h2>Concepto de Selección</h2>
        <p>
            El candidato ha superado nuestros filtros iniciales de pre-selección, validación de antecedentes y análisis técnico en base al requerimiento detallado por su organización. Adjunto junto a esta notificación podrá visualizar su hoja de vida extendida.
        </p>

        <p>
            Quedamos a la espera de su validación mediante nuestro Portal Mágico para avanzar a la fase final de contratación oficial.
        </p>
    </div>

    <div class="footer">
        Este documento es de uso confidencial y propiedad exclusiva de CONTRATAMOS S.A.S. No está autorizada su distribución sin consentimiento previo.<br>
        Generado automáticamente por el Sistema de Selección.
    </div>
</body>
</html>
