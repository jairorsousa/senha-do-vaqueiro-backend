# JRSYSTEM - Backend Visão Assistência

- Documentação da API Postiman [Online documentation page](//postma.com)


- Sistema completo de gerênciamento de ordem de serviço 



### Laravel Quick Start

1. Clone do repositório.


2. Download e instalação do `Docker`.


3. Inicie uma janela de prompt de comando ou terminal e altere o diretório para [caminho descompactado]:


4. Criar os containers 1 -`Build`:
   
        docker-compose -f docker-compose.yml build

5. Criar os containers 2 -`up`:

        docker-compose -f docker-compose.yml up

6. Instalar o `Composer` fazer o download da versão mais recente https://getcomposer.org/download/ em seguida verificar se ta tudo certo:
   
        composer --version


7. Instalar `Composer` depedencias.
   
        composer install

8. Copia `.env.example` para. Use `.env` em linux

        cp .env.example .env

    se estiver usando  `Windows`, use `copy` ao ínves de `cp`.
   
        copy .env.example .env
   

9. Alterar os configurações do banco de dados no arquivo `.env` .


10. Entrar na branch develop.

        git checkout develop


11. Dar um pull :

        git pull origin develop


12. Abrir uma branch nova e iniciar os trabalhos:
    
        git checkout -b your-branch-name

# ANTES DE FAZER UM PUSH
- Certifique-se de que sua branch está sincronizada com a branch de develop
 ```console
 git checkout delevop
 git pull origin develop
 git checkout your-branch-name
 git merge develop
 ```
- Agora faça um push da sua branch para o repositório
 ```console
 git add *
 git commit -m "CVB-code YOUR COMMIT HERE"
 git push origin your-branch-name
 ```
