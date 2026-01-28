<!DOCTYPE html>
<html>

<head>
    <title>Bem-vindo a Arret!</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px;">
        <h1 style="color: #333;">Parabéns, {{ $user->first_name }}!</h1>

        <p style="font-size: 16px; color: #555; line-height: 1.6;">
            Sua conta foi criada com sucesso e seu pagamento foi confirmado.
        </p>

        <div style="background-color: #f8f9fa; border-left: 4px solid #4CAF50; padding: 15px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Plano Ativo:</strong> {{ $plan->name }}</p>
            <p style="margin: 5px 0 0;"><strong>Valor Pago:</strong> {{ number_format($plan->price, 2) }} MT</p>
            <p><strong>Referência de Pagamento:</strong> {{ $subscription->payment_reference }}</p>
            <p><strong>Válido até:</strong> {{ $subscription->end_date->format('d/m/Y') }}</p>
        </div>

        <p style="font-size: 16px; color: #555;">
            Agora você tem acesso total à nossa plataforma. Clique no botão abaixo para começar:
        </p>


        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ config('app.frontend_url') }}/login"
                style="background-color: #6d28d9; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Aceder Minha Conta
            </a>
        </div>

        <p style="margin-top: 40px; font-size: 12px; color: #999; text-align: center;">
            Obrigado por escolher a Lenda.<br>
            Se tiver dúvidas, responda a este email.
        </p>
    </div>
</body>

</html>