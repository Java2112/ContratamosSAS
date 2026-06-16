<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Descargos Disciplinarios - {{ $record->record_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #2d3748;
            margin: 20px 35px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1e3a8a;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #4a5568;
            margin-top: 5px;
            text-transform: uppercase;
        }
        .record-number {
            font-size: 12px;
            color: #718096;
            margin-top: 3px;
        }
        
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-table th, .meta-table td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .meta-table th {
            background-color: #f8fafc;
            color: #1e3a8a;
            font-weight: bold;
            width: 25%;
        }
        .meta-table td {
            width: 25%;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 20px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .textbox {
            padding: 10px;
            background-color: #f8fafc;
            border-left: 3px solid #1e3a8a;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            text-align: justify;
            margin-bottom: 15px;
        }

        .qa-container {
            margin-top: 15px;
        }
        .qa-item {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .question {
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 4px;
        }
        .answer {
            padding: 8px 12px;
            background-color: #fafafa;
            border: 1px solid #e2e8f0;
            text-align: justify;
            min-height: 40px;
            color: #4a5568;
        }

        .signatures-table {
            margin-top: 50px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures-table td {
            width: 50%;
            padding: 15px;
            text-align: center;
            border: none;
        }
        .signature-line {
            width: 80%;
            margin: 0 auto 8px auto;
            border-top: 1px solid #4a5568;
        }
        .signer-name {
            font-weight: bold;
            color: #2d3748;
        }
        .signer-details {
            font-size: 10px;
            color: #718096;
        }

        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-title">SISTEMA CONTRATAMOS S.A.S.</div>
        <div class="doc-title">Acta de Diligencia de Descargos Disciplinarios</div>
        <div class="record-number">Diligencia N°: <strong>{{ $record->record_number }}</strong></div>
    </div>

    <div class="section-title">1. DATOS PRINCIPALES DE LA DILIGENCIA</div>
    <table class="meta-table">
        <tr>
            <th>Nombre Trabajador:</th>
            <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
            <th>Documento de Identidad:</th>
            <td>{{ $employee->document_type }} {{ $employee->document_number }}</td>
        </tr>
        <tr>
            <th>Cargo del Trabajador:</th>
            <td>{{ $employee->cargo }}</td>
            <th>Empresa Cliente:</th>
            <td>{{ $clientName }}</td>
        </tr>
        <tr>
            <th>Fecha de Diligencia:</th>
            <td>{{ \Carbon\Carbon::parse($record->scheduled_date)->format('d/m/Y') }}</td>
            <th>Hora de Diligencia:</th>
            <td>{{ \Carbon\Carbon::parse($record->scheduled_time)->format('H:i') }}</td>
        </tr>
        <tr>
            <th>Representante Compañía:</th>
            <td>{{ $record->representative_name }}</td>
            <th>Cargo Representante:</th>
            <td>{{ $record->representative_role }}</td>
        </tr>
        <tr>
            <th>Testigo de Asistencia:</th>
            <td colspan="3">{{ $record->witness_name ?? 'Ninguno registrado' }}</td>
        </tr>
    </table>

    <div class="section-title">2. MARCO INTRODUCTORIO Y LEGAL</div>
    <div class="textbox">
        {{ $record->introductory_text }}
    </div>

    <div class="section-title">3. MOTIVO DE LA CITACIÓN Y FALTAS IMPUTADAS</div>
    <table class="meta-table" style="margin-bottom: 10px;">
        <tr>
            <th style="width: 25%;">Hechos y Motivo:</th>
            <td style="width: 75%;" colspan="3">{{ $record->reason }}</td>
        </tr>
        @if($record->rules_violated)
        <tr>
            <th>Normas Infringidas:</th>
            <td colspan="3">{{ $record->rules_violated }}</td>
        </tr>
        @endif
        @if($record->initial_observations)
        <tr>
            <th>Observaciones Iniciales:</th>
            <td colspan="3">{{ $record->initial_observations }}</td>
        </tr>
        @endif
    </table>

    <div style="page-break-after: always;"></div>

    <div class="section-title">4. CUESTIONARIO Y RESPUESTAS DEL TRABAJADOR</div>
    <div class="qa-container">
        @foreach($record->questions as $question)
            <div class="qa-item">
                <div class="question">Pregunta {{ $question->sort_order }}: {{ $question->question_text }}</div>
                <div class="answer">
                    {{ $question->answer->answer_text ?? 'SIN RESPUESTA REGISTRADA' }}
                </div>
            </div>
        @endforeach
    </div>

    @if($record->final_observations)
    <div class="section-title">5. OBSERVACIONES Y ACLARACIONES FINALES</div>
    <div class="textbox">
        {{ $record->final_observations }}
    </div>
    @endif

    <div class="section-title">6. CIERRE DE LA DILIGENCIA Y CONFORMIDAD</div>
    <p style="text-align: justify; font-size: 11px; margin-top: 10px; color: #4a5568;">
        Habiéndose leído la presente acta y estando de acuerdo con su contenido, los comparecientes la suscriben en señal de conformidad ante los firmantes del proceso. La presente acta presta mérito y se archiva formalmente en el expediente laboral del trabajador contratado.
    </p>

    <table class="signatures-table">
        <tr>
            <td>
                <br><br><br>
                <div class="signature-line"></div>
                <span class="signer-name">{{ $employee->first_name }} {{ $employee->last_name }}</span><br>
                <span class="signer-details">TRABAJADOR COMPARECIENTE<br>C.C. {{ $employee->document_number }}</span>
            </td>
            <td>
                <br><br><br>
                <div class="signature-line"></div>
                <span class="signer-name">{{ $record->representative_name }}</span><br>
                <span class="signer-details">REPRESENTANTE EMPLEADOR<br>{{ $record->representative_role }}</span>
            </td>
        </tr>
        @if($record->witness_name)
        <tr>
            <td colspan="2" style="padding-top: 40px;">
                <div class="signature-line" style="width: 40%;"></div>
                <span class="signer-name">{{ $record->witness_name }}</span><br>
                <span class="signer-details">TESTIGO DE LA DILIGENCIA</span>
            </td>
        </tr>
        @endif
    </table>

    <div class="footer">
        Página 1. Sistema Contratamos S.A.S. &copy; {{ date('Y') }}. Documento emitido electrónicamente con fines de registro de descargos disciplinarios.
    </div>

</body>
</html>
