###################
Sistema para gerência de eventos
###################


*******************
Instalação
*******************

1.  Instale o xampp-7
 - PHP 7+
 - MySql

2.  Configure o database no arquivo /application/config/database.php

3. Configure a url base em /application/config/config.php

.. code-block:: php
	
	$config['base_url'] = 'https://eventos.ifrn.edu.br/expotecnc/2021/';


3. Dê permissão de escrita nos seguintes diretórios:

	- /static/fotos
	- /static/captcha
	- /uploads/minicursos
	- /uploads/trabalhos
	- /uploads/trabalhos/correcoes
	- /certificados/matriculados
	- /certificados/minicursos
	- /certificados/participacao
	- /certificados/avaliador
	- /certificados/trabalhos

4.  Para criar as tabelas entre no endereço http://localhost/eventos/admin

As seguintes linhas de código que estão dentro do application/controller/admin irão criar as tabelas no banco de dados:

.. code-block:: php

	$this->load->model("Database_model");
	$this->Database_model->creat_all_if_not_exists();

O e-mail/senha de administrador é admin@ifrn.edu.br - 123456


5.  O endereço do site é http://localhost/eventos

Obs:
Para desativar o captcha vá em application/libraries/CaptchaLib.php

.. code-block:: php

	private $active = false; 


6. Para permitir o envio de e-mails através do GMAIL é preciso ir em: https://myaccount.google.com/u/2/personal-info
SEGURANÇA->Acesso a app menos seguro->Ativar


*******************
Roteiro de testes
*******************

Verifique se o envio de e-mails está desativado em application/config/config.php. Isso fará com que os e-mails não sejam enviados e sim impressos na tela.
.. code-block:: php
	$config['envio_emails_ativo'] = false;

1.  Inscrever-se como participante
2.  Realizar login como participante
3.  Veja se tudo foi salvo corretamente em "Ver perfil"
4.  Faça modificações em todos os campos para verificar se está salvando
5.  Usar o esqueci minha senha como participante

Submissões
**********************

1.  Submeta um trabalho sem nada preenchido
	- Verifique se deu as mensagens de erro
2.  Adicione a quantidade máxima de coautores e orientadores
	- Insira um e-mail inválido para dos autores e um dos orientadores.
	- Verifique se deu as mensagens de erro
3.  Corrija todos os campos e submeta novamente.
	- Grave o e-mail do 2° orientador para usar no próximo teste.
4.  Acesse o link para dar ciência do trabalho no e-mail de um do 1° orientador apenas. Deixe o outro sem dar ciência.
	- Dê ciência também como 1° ou 2° coautor
5.  Abra o trabalho submetido para verificar se está tudo correto.
	- Verifique se o download do arquivo está funcionando
6.  Submeta mais um trabalho e cancele-o
	- Obs: Trabalhos não podem ser alterados depois de submetidos. Tem que-se cancelar e reenviar.

Dar ciência
**********************

1.  Se inscreva com o e-mail que você usou para o orientador do trabalho enviado
2.  Ao fazer o login já deve ser possível visualizar os trabalhos submetidos com seu nome como orientador.
3.  Abra e verifique as informações
	- Não deve ser possível fazer submissões nesse formulário
4.  Registre a ciência do trabalho
	- Verifique se os dois orientadores ficaram com o registro de ciência ok
5.  Submeta outro trabalho como orientador, se inclua como orientador e coautor do trabalho
	- Verifique que o sistema não permite você se incluir como orientador ou coautor do próprio trabalho

Aprovação de trabalhos
**********************

1.  Em outro navegador acesse: http://localhost/eventos/admin
2.  Entre com admin@ifrn.edu.br senha 123456
3.  Vá em Evento e marque "Aceitando submissões de correções de trabalhos"
4.  Vá em trabalhos e:
	- Reprove o trabalho e verifique no outro navegador com a conta da participante que submeteu o trabalho
		- A partir deste ponto não deve mais ser possível cancelar o trabalho ou realizar qualquer alteração
	- Faça o mesmo para "Aprovado com correções pendentes" e adicione uma observação no trabalho
	- Mas agora, com a conta do participante que submeteu o trabalho, submeta também um arquivo de correção
	- Verifique se a observação do Admin aparece.
	- Após submeter a correção, volte para a tela do admin e teste se todos os arquivos estão funcionando
	- Marque o trabalho como "Aprovado com correções finalizadas"
	- Verifique na tela do participante
		- Neste ponto não deve mais ser possível reenviar o trabalho
5.  Na tela do admin, marque o trabalho como "Apresentado" e gere o certificado
	- Verifique se os e-mails foram enviados para todos os coautores/orientadores
	- Verifique se o link de 1 coautor, 1 orientador e do autor principal estão funcionando
	- Verifique se o PDF foi gerado corretamente
	- Na tela do participante, verifique se os certificados estão disponíveis e funcionando
6.  Na tela de admin, marque o outro trabalho (Submetido pelo orientador) como aprovado.
	- Volte para a listagem de trabalhos e mande gerar o certificado de todos.
	- Verifique se os PDF foram gerados e se estão funcionando
	- Na tela do participante (Orientador), verifique se os certificados estão disponíveis


Minicurso
**********************

1.  Com a conta do participante, faça uma submissão de minicurso sem nada preenchido.
	- Verifique as mensagens de erro
2.  Submeta um minicurso com um coautor com o e-mail errado
	- Verifique as mensagens de erro
3.  Submeta o minicurso com os coautores corretos, de forma que um deles deve ser alguém já cadastrado, preferencialmente o 2° orientador do trabalho submetido no teste anterior.
	- Verifique se os e-mails foram enviados para todos os coautores
	- Verifique se o link de ciência está funcionando para o coautor que não estava cadastrado
	- Dê ciência para o coautor não cadastrado
4.  Verifique se tudo foi salvo corretamente e se o arquivo está funcionando
	- Faça modificações no minicurso, nesta etapa ainda é possível editar
	- As modificações não devem gerar novos e-mails solicitando ciência
5.  Na tela de Admin
	- Faça modificações em todos os campos e sete como "Reprovado"
	- Verifique que na tela do participante não é mais possível cancelar/editar o minicurso
6.  Na tela de Admin marque o "Disponível para matrícula" e submeta
	- O status tem que ir para "Aprovado" automaticamente
	- Ele deve dar erro pois você não escolheu os horários do minicurso
	- Sete os horários
		- No primeiro dia, 4 horários seguidos
			- 8:00, 9:30, 14:00 e 15:30
		- No segundo dia, apenas 1 horários
	- Sete alguma observação
7.  Na tela do participante, verifique como ficou
	- Nos horários seguidos ele deve mostrar como das 08:00 às 11:00 e das 14:00 às 17:00
	- No horário sozinho ele deve mostrar apenas o horário escolhido
	- A observação deve aparecer
	- O campo para setar a URL deve aparecer
	- A listagem dos alunos matriculados também aparecerá
8.  Salve uma URL de teste
9.  Na área de minicursos, verifique que o seu minicurso não apareceu, você não pode visualizar os próprios minicursos naquela área

10.  Cadastre mais um minicurso e com o Admin, faça com que o minicurso coincida em pelo menos um dos horários

Matriculando
**********************

1.  Crie um usuário novo ou entre com: aluno@ifrn.edu.br senha 123456
2.  Verifique se o minicurso aparece corretamente na área de minicursos
3.  Matricule-se, cancele a matrícula e matricule-se novamente
4.  Tente se matricular no outro minicurso que coincide o horário
	- Ele não deve permitir

5.  Acesse o banco de dados do minicurso onde você conseguiu se matricular e mude a quantidade de vagas manualmente para 1
6.  Com outro usuário tente se matricular neste mesmo minicurso, como já existe 1 matriculado e só tem 1 vaga ele não deve permitir.

7.  Acesse novamente com o participante que submeteu o minicurso
	- Acesse o minicurso e sete o inscrito para aprovado e com a ch máxima
	- Gere a lista de frequência e verifique

Certificados do minicurso
**********************

1.  Na tela de Admin
	- Verifique se a ch e a aprovação do inscrito do minicurso está aparecendo normalmente
	- Verifique se a lista de frequência foi gerada normalmente
2.  Gere o certificado para ministrantes
	- Cheque o link de validação dos certificados dos autores/coautores
	- Verifique se o PDF está correto
3.  Desmarque a opção de "Aprovado" do inscrito e gere o certificado para os inscritos
	- Ele não deve gerar, pois não tem ninguém aprovado
4.  Aprove o inscrito e gere o certificado
	- Cheque o link de validação 
	- Verifique se o PDF está correto

5.  Com a conta do ministrante, verifique se o certificado está Disponível na área de submissões
6.  Com a conta do inscrito, verifique se o certificado está disponível na área de minicursos.
7.  Com a conta do orientador/coautor do minicurso, registre ciência e verifique se o certificado está disponível.
8.  Com o Admin, na listagem de minicursos, gere o certificado para todos os minicursos
	- Ele irá gerar certificado para todos os inscritos aprovados e todos os ministrantes dos cursos aprovados

Certificado de participação/revisão
**********************

1.  Como admin, na tela de usuários
	- Marque um usuário como aprovado para certificado de participante e salve
	- Gere o certificado
2.  Filtre por Revisor
	- marque como "Aprovado para certificado de revisor" e "aprovado para certificado de participante"
	- Volte na lista de usuários e gere o certificado para todos
3.  Marque um usuário como "aprovado para certificado de participante" e salve
	- Verifique que na lista ele destaca esse usuário dizendo que o certificado ainda não foi gerado

4.  Entre como o participante que você gerou o certificado e verifique se a mensagem com o certificado aparece.


Revisor de trabalhos
**********************

1.  Entre como revisor na área de admin: revisor@ifrn.edu.br senha 123456
	- Verifique que você só poderá ver trabalhos e não conseguirá ver os autores
	- Mas você pode salvar observações e mudar o status do trabalho
2.  Verifique que ele disponibiliza seu certificado na tela principal

Encerramento do evento
**********************

1.  Como Admin, na área de configuração do evento, marque como encerrado.
2.  Tente se inscrever como um novo usuário
3.  Tente submeter um trabalho ou minicurso
4.  Tente se matricular em um minicurso

(Não será possível realizar nenhuma das operações)