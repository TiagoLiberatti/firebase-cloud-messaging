Sobre
-------------------

      - Aplicação web desenvolvida com a linguagem PHP utilizando o framework Yii 2 integrado com
         com o serviço firebase cloud messaging.
      - Possui as funcionalidades de recepção e envio de notificações, 'pushs', para dispositivos
        assinados a um grupo.

Funcionamento
-------------------
       - É registrado um Service Worker no navegador do usuário através de um pop-up de permissão.
       - Com requisições realizadas ao web service do firebase, cloud messaging, é adicionado ou 
         removido usuários a um determinado grupo e também feito o envio de notificações a esse grupo.

Requisitos
-------------------
       - Gerenciador de pacotes composer. 
       - Servidor web capaz de interpretar a linguagem PHP.