<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Role::create([ 'name' => 'super-admin',]);
        
        $user = User::factory()->create(attributes: [
            'first_name' => 'Wilton',
            'last_name' => 'Munguambe',
            'email' => 'admin@lendamais.com',
        ]);

        $user->assignRole('super-admin');

        // Create pages
        Page::create([
            'title' => 'Sobre a Lenda',
            'slug' => 'sobre-a-lenda',
            'content' => '<h2>A Lenda de Oãda: Uma Marca que Une Fantasia, Teologia e Criatividade</h2><p>Histórias que Misturam Fantasia e Teologia</p><p>"A Lenda de Oãda" é uma marca de histórias que combina elementos de fantasia e teologia, oferecendo uma nova forma de expressão criativa para divulgar o evangelho de Cristo. Mais do que apenas narrativas envolventes, essa marca busca inspirar e conectar leitores através de histórias que exploram questões espirituais profundas, enquanto mergulham em mundos imaginários ricos e detalhados.</p><p><br></p><h3>Criatividade como Expressão do Evangelho</h3><p>A essência de "A Lenda de Oãda" está na sua capacidade de usar a criatividade como um veículo poderoso para transmitir valores espirituais e teológicos. As histórias apresentam personagens e tramas que, embora fictícios, refletem verdades universais e temas centrais da fé cristã, como redenção, sacrifício e esperança. A marca vai além do entretenimento, transformando a fantasia em uma ferramenta para estimular diálogos sobre fé e espiritualidade.</p><h3>Impacto Literário e Espiritual</h3><p>"A Lenda de Oãda" também incentiva a criatividade como meio de expressão para a divulgação do evangelho, valorizando a arte, a escrita e outras formas criativas que refletem os ensinamentos de Cristo. Com isso, a marca estabelece um novo padrão para histórias que unem o imaginário e o divino, impactando tanto no campo literário quanto espiritual.</p>',
        ]);

        Page::create([
            'title' => 'Termos e condições',
            'slug' => 'termos-e-condicoes',
            'content' => '<h2><strong>1. Introdução</strong></h2><p><br>Bem-vindo à aplicação "Lenda Mais", um serviço fornecido pela marca A Lenda de Oãda. Ao utilizar a nossa aplicação, você concorda com os presentes Termos e Condições de Uso. Estes termos regem o acesso e uso da aplicação, incluindo o conteúdo gratuito e o conteúdo premium disponível apenas para assinantes pagantes. Pedimos que leia atentamente antes de utilizar os nossos serviços.<br><br></p><h2><strong>2. Conteúdo e Acesso</strong></h2><p><br><strong>2.1 Conteúdo Gratuito</strong><br>Os utilizadores podem aceder gratuitamente a determinados conteúdos da aplicação sem a necessidade de uma conta premium. Este conteúdo inclui:<br><br>-&nbsp; Podcasts: Rumores da Lenda.<br>-&nbsp; Amostras de livros disponíveis na versão premium.<br>-&nbsp; Amostras de audiobooks disponíveis na versão premium.<br><br><strong>2.2 Conteúdo Premium</strong><br>O conteúdo premium está disponível apenas para utilizadores que adquirirem uma conta premium paga. Este conteúdo inclui:<br><br>- Acesso completo aos livros.<br>- Acesso completo aos audiobooks.<br>- Outros conteúdos exclusivos que possam ser adicionados futuramente.<br><br></p><h2>3. Pagamentos</h2><p><br><strong>3.1 Métodos de Pagamento</strong><br>Atualmente, os pagamentos para a aquisição da conta premium são feitos offline. O utilizador deverá seguir as instruções fornecidas na aplicação para concluir o pagamento.<br><br><strong>3.2 Validação da Conta Premium</strong><br>Após a realização do pagamento, o utilizador deverá fornecer o seu endereço de email. Esse email será utilizado para a criação da conta premium e envio das credenciais de acesso. O utilizador receberá um email com o seu nome de utilizador e senha, permitindo o acesso ao conteúdo premium.<br><br></p><h2>4. Duração da Conta Premium</h2><p><br><strong>4.1 Plano Anual</strong><br>A conta premium tem uma validade de 12 meses a partir da data de ativação. O custo da subscrição anual é de 1.800 meticais, sujeito a alterações. A aplicação informará os utilizadores com antecedência em caso de ajustes no valor.<br><br></p><h2>5. Uso de Credenciais</h2><p><br>As credenciais fornecidas para o acesso ao conteúdo premium são pessoais e intransferíveis. O utilizador não deve partilhar o seu nome de utilizador ou senha com terceiros. O uso indevido das credenciais poderá resultar na suspensão ou cancelamento da conta premium, sem direito a reembolso.<br><br></p><h2>6. Alterações nos Termos e Condições</h2><p><br>A Lenda Mais reserva-se o direito de modificar estes Termos e Condições a qualquer momento. Quaisquer alterações significativas serão comunicadas aos utilizadores através da aplicação ou por email.<br><br></p><h2>7. Suporte e Contacto</h2><p><br>Caso tenha dúvidas sobre o uso da aplicação, o processo de pagamento ou precise de assistência, entre em contacto connosco através do email fornecido na aplicação.<br><br></p><h2>8. Lei Aplicável</h2><p><br>Estes Termos e Condições são regidos pela legislação da República de Moçambique. Quaisquer disputas relacionadas ao uso da aplicação serão resolvidas pelos tribunais competentes no país.</p>',
        ]);

        Page::create([
            'title' => 'Políticas de Privacidade',
            'slug' => 'politicas-de-privacidade',
            'content' => '<p>Esta Política de Privacidade descreve como coletamos, usamos, compartilhamos e protegemos suas informações pessoais ao utilizar a aplicação "Lenda Mais", oferecida pela marca *A Lenda de Oãda*. Ao utilizar a aplicação, você concorda com as práticas descritas nesta política.<br><br></p><h2>1. Informações Coletadas</h2><p><br><strong>1.1 Informações Fornecidas pelo Utilizador&nbsp; </strong><br><br>Ao utilizar a aplicação, poderemos solicitar informações pessoais como:<br>- Nome completo.<br>- Endereço de email (necessário para a criação da conta premium).<br>- Detalhes de pagamento, apenas quando necessário para o processamento offline (nenhuma informação financeira é coletada diretamente na aplicação).<br><br><strong>1.2 Informações Coletadas Automaticamente</strong><br> &nbsp;<br>Podemos coletar algumas informações automaticamente quando o utilizador acessa a aplicação, como:<br>- Dados de uso da aplicação, incluindo informações sobre como o utilizador interage com o conteúdo.<br>- Identificadores de dispositivos e endereço IP.<br><br></p><h2>2. Uso das Informações</h2><p><br>Utilizamos as informações fornecidas pelos utilizadores para os seguintes fins:</p><ul><li>Criação e Gestão de Contas Premium: Usamos o email do utilizador para criar e ativar a conta premium, bem como para enviar as credenciais de acesso.</li><li>Comunicações: Podemos utilizar o seu email para enviar informações relacionadas ao serviço, como atualizações, confirmações de pagamento, notificações de mudanças nas condições de serviço ou outras comunicações relevantes.</li><li>Melhoria da Aplicação: Analisamos os dados de uso para melhorar a funcionalidade e experiência do utilizador dentro da aplicação.</li></ul><p><br></p><h2>3. Compartilhamento de Informações</h2><p><br>Não compartilhamos as suas informações pessoais com terceiros, exceto nos seguintes casos:</p><ul><li>Conformidade Legal: Podemos divulgar as suas informações quando exigido por lei ou em resposta a solicitações de autoridades governamentais.</li><li>Proteção de Direitos: Podemos compartilhar informações se acreditarmos que é necessário para proteger os nossos direitos, a segurança dos utilizadores ou cumprir com os termos e condições de uso da aplicação.</li></ul><p><br></p><h2>4. Segurança das Informações</h2><p><br>Levamos a segurança dos seus dados a sério e utilizamos medidas apropriadas para proteger suas informações contra acesso não autorizado, alteração, divulgação ou destruição. No entanto, nenhum sistema de segurança é completamente infalível, e não podemos garantir a segurança absoluta das suas informações.<br><br></p><h2>5. Retenção de Dados</h2><p><br>As suas informações serão mantidas durante o período necessário para cumprir os fins descritos nesta Política de Privacidade, incluindo a manutenção de contas premium durante o período de validade de 12 meses. Após o término da validade da conta premium, as informações pessoais podem ser retidas conforme exigido por lei ou para resolver disputas e garantir o cumprimento dos nossos termos.<br><br></p><h2>6. Direitos do Utilizador</h2><p><br>O utilizador tem o direito de:</p><ul><li>Acessar e Atualizar Informações: Pode solicitar o acesso ou a correção de qualquer informação pessoal que tenhamos coletado.</li><li>Revogar Consentimento: Pode revogar o consentimento para o uso de suas informações pessoais a qualquer momento, no entanto, isso pode limitar a capacidade de usar a aplicação, especialmente os serviços premium.</li><li>Exclusão de Dados: Pode solicitar a exclusão de seus dados pessoais, salvo em casos em que somos obrigados por lei a retê-los.</li></ul><p><br></p><h2>7. Alterações a Esta Política de Privacidade</h2><p><br>Podemos atualizar esta Política de Privacidade periodicamente para refletir alterações nos nossos serviços ou regulamentos aplicáveis. Quaisquer alterações serão comunicadas através da aplicação ou por email, quando aplicável.<br><br></p><h2>8. Contacto</h2><p><br>Se tiver dúvidas ou preocupações sobre esta Política de Privacidade ou sobre o tratamento dos seus dados pessoais, entre em contacto connosco através do email fornecido na aplicação.<br><br><br></p>',
        ]);

        $this->call([
            PlanSeeder::class,
        ]);
    }
}
