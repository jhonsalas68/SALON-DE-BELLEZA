<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Servicio #{{ $cita->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 280px;
            margin: 0 auto;
            padding: 10px;
            color: #000;
            background-color: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .header {
            margin-bottom: 15px;
        }

        .logo {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .info-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .footer {
            margin-top: 25px;
            font-size: 10px;
        }

        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 8px;
            background-color: #f43f5e;
            color: white;
            text-align: center;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 15px;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 13px;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Imprimir Ticket</button>
    </div>

    <div class="header text-center">
        <div class="logo">SALÓN ANITA</div>
        <div>Belleza Premium</div>
        <div>Nit: 4892019022</div>
        <div>Cel: +591 76543210</div>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="bold">Ticket:</td>
            <td>#SERV-{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="bold">Fecha:</td>
            <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}</td>
        </tr>
        <tr>
            <td class="bold">Cliente:</td>
            <td>{{ $cita->cliente->name ?? 'Casual' }}</td>
        </tr>
        <tr>
            <td class="bold">Estilista:</td>
            <td>{{ $cita->estilista->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="bold text-center" style="margin-bottom: 5px;">DETALLE DE SERVICIO</div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
        <span>{{ $cita->servicio->nombre }}</span>
        <span>Bs{{ number_format($cita->servicio->precio, 2) }}</span>
    </div>

    <div class="divider"></div>

    <table class="totals-table">
        <tr>
            <td>Precio Regular:</td>
            <td class="text-right">Bs{{ number_format($cita->servicio->precio, 2) }}</td>
        </tr>
        @if($promocion)
        <tr>
            <td>Desc. Promo ({{ number_format($promocion->descuento_porcentaje, 0) }}%):</td>
            <td class="text-right">-Bs{{ number_format(($cita->servicio->precio * $promocion->descuento_porcentaje) / 100, 2) }}</td>
        </tr>
        @endif
        <tr class="bold">
            <td style="font-size: 13px;">TOTAL PAGADO:</td>
            <td class="text-right" style="font-size: 13px;">
                @if($promocion)
                    Bs{{ number_format($cita->servicio->precio - (($cita->servicio->precio * $promocion->descuento_porcentaje) / 100), 2) }}
                @else
                    Bs{{ number_format($cita->servicio->precio, 2) }}
                @endif
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer text-center">
        <p>¡Gracias por su visita!</p>
        <p>Conserve su boleta para reclamos.</p>
        <p>Desarrollado por Salon Anita</p>
    </div>

    @php
        $valoracionExistente = \App\Models\Valoracion::where('cita_id', $cita->id)->first();
    @endphp

    <div class="no-print" style="margin-top: 20px; padding: 12px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; font-family: sans-serif;">
        <h4 style="margin: 0 0 8px 0; font-size: 13px; text-align: center; color: #be123c;">¿Qué tal fue tu experiencia? (Calificación NPS)</h4>
        @if($valoracionExistente)
            <div style="text-align: center; color: #047857; font-weight: bold; font-size: 12px;">
                ✓ ¡Ya calificaste este servicio con {{ $valoracionExistente->estrellas }} ★!
            </div>
        @else
            <form action="{{ route('valoraciones.store') }}" method="POST" style="text-align: center;">
                @csrf
                <input type="hidden" name="cita_id" value="{{ $cita->id }}">
                <div style="margin-bottom: 8px; font-size: 12px; font-weight: bold; color: #d97706;">
                    <label style="cursor: pointer;"><input type="radio" name="estrellas" value="5" checked> ★★★★★ (5)</label>
                    <label style="cursor: pointer; margin-left: 8px;"><input type="radio" name="estrellas" value="4"> ★★★★ (4)</label>
                    <label style="cursor: pointer; margin-left: 8px;"><input type="radio" name="estrellas" value="3"> ★★★ (3)</label>
                </div>
                <input type="text" name="comentario" placeholder="Dejar un comentario opcional..." style="width: 90%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 11px; margin-bottom: 8px;">
                <button type="submit" style="width: 95%; padding: 6px; background-color: #e11d48; color: white; font-weight: bold; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">Enviar Opinión</button>
            </form>
        @endif
    </div>

</body>
</html>
