<!DOCTYPE html>
<html>

<head>
    <title>Subscrição Renovada - Lenda</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px;">
        <h1 style="color: #333;">Olá, {{ $subscription->user->first_name }}!</h1>

        <p style="font-size: 16px; color: #555; line-height: 1.6;">
            A sua subscrição foi renovada com sucesso! O pagamento foi processado e o seu acesso continua ativo.
        </p>

        <div style="background-color: #f8f9fa; border-left: 4px solid #4CAF50; padding: 15px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Plano Renovado:</strong> {{ $subscription->plan->name ?? $subscription->plan->slug }}</p>
            <p style="margin: 5px 0 0;"><strong>Valor Pago:</strong> {{ number_format($subscription->plan->price, 2) }} MT</p>
            <p style="margin: 5px 0 0;"><strong>Referência M-Pesa:</strong> {{ $subscription->mpesa_transaction_id ?? $subscription->mpesa_reference }}</p>
            <p style="margin: 5px 0 0;"><strong>Nova Validade:</strong> {{ \Carbon\Carbon::parse($subscription->end_date)->format('d/m/Y') }}</p>
        </div>

        <p style="font-size: 16px; color: #555;">
            Continue a aproveitar todo o nosso conteúdo exclusivo sem interrupções.
        </p>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ config('app.frontend_url') }}/app/profile"
                style="background-color: #6d28d9; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Gerir Minha Conta
            </a>
        </div>

        <p style="margin-top: 40px; font-size: 12px; color: #999; text-align: center;">
            Obrigado por continuar com a Lenda.<br>
            Se tiver dúvidas, responda a este email.
        </p>
    </div>
</body>

</html>