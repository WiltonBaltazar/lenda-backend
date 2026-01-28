<!DOCTYPE html>
<html>

<head>
    <title>Confirmação de Cancelamento - Lenda</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px;">
        <h1 style="color: #333;">Olá, {{ $subscription->user->first_name }}.</h1>

        <p style="font-size: 16px; color: #555; line-height: 1.6;">
            Recebemos o seu pedido de cancelamento da subscrição. Lamentamos vê-lo partir.
        </p>

        <p style="font-size: 16px; color: #555; line-height: 1.6;">
            A sua subscrição não será renovada, mas você ainda terá acesso aos conteúdos premium até o fim do período atual.
        </p>

        <div style="background-color: #fff5f5; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Plano Cancelado:</strong> {{ $subscription->plan->name ?? $subscription->plan->slug }}</p>
            <p style="margin: 5px 0 0;"><strong>Acesso disponível até:</strong> {{ \Carbon\Carbon::parse($subscription->end_date)->format('d/m/Y') }}</p>
        </div>

        <p style="font-size: 16px; color: #555;">
            Se mudar de ideias, pode reativar a sua subscrição a qualquer momento através do seu perfil.
        </p>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ config('app.frontend_url') }}/app/profile"
                style="background-color: #6d28d9; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Ver Meu Perfil
            </a>
        </div>

        <p style="margin-top: 40px; font-size: 12px; color: #999; text-align: center;">
            Esperamos vê-lo novamente em breve na Lenda.<br>
        </p>
    </div>
</body>

</html>