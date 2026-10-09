<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0; padding:0; background-color:#f5f1ec; font-family: Arial, Helvetica, sans-serif; color:#2b2420;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding:28px 32px; background-color:#2b2420; color:#f5f1ec;">
                            <h1 style="margin:0; font-size:18px;">Nueva compra en Influessence Academy</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px; font-size:14px;">Orden <strong>#{{ $order->id }}</strong></p>

                            <p style="margin:0 0 4px; font-size:14px;"><strong>Cliente</strong></p>
                            <p style="margin:0 0 16px; font-size:14px;">{{ $order->user->name }} &lt;{{ $order->user->email }}&gt;</p>

                            <p style="margin:0 0 8px; font-size:14px;"><strong>Cursos/paquetes comprados</strong></p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px; font-size:14px;">
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td style="padding:6px 0; border-bottom:1px solid #eee;">{{ $item->course->title }}</td>
                                        <td style="padding:6px 0; border-bottom:1px solid #eee; text-align:right;">${{ number_format($item->price_cents / 100, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="padding:10px 0; font-weight:bold;">Total pagado</td>
                                    <td style="padding:10px 0; font-weight:bold; text-align:right;">${{ number_format($order->total_cents / 100, 2) }} {{ strtoupper($order->currency) }}</td>
                                </tr>
                            </table>

                            <a href="{{ $adminUrl }}" style="display:inline-block; padding:12px 24px; background-color:#2b2420; color:#f5f1ec; text-decoration:none; border-radius:4px; font-size:14px;">Ver orden en el panel</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
