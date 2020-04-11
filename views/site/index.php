<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="col-md-6">
        <button class="btn btn-success" id="permissao">
            Solicitar Permissão
        </button>
    </div>

    <div class="col-md-6">
        <button class="btn btn-success" id="send">
            Enviar Notificação
        </button>
    </div>
</div>


<?php
$url = \yii\helpers\Url::to(['site/send-notification']);
$urlSave = \yii\helpers\Url::to(['site/save-token']);
$script = <<< JS
$('#permissao').on('click', function() {
  pedirPermissaoParaReceberNotificacoes();
});

$('#send').on('click', function() {
    $.post('$url', function() {
        
    });
});

async function pedirPermissaoParaReceberNotificacoes(){
  try {
    const messaging = firebase.messaging();
    await messaging.requestPermission();
    const token = await messaging.getToken();
    $.post('$urlSave', {token:token}, function(data) {
        if (data){
            alert('Salvo com Sucesso');
        }else{
            alert('Erro ao Salvar');
        }
    });
    return token;
  } catch (error) {
    console.error(error);
  }
}


JS;
$this->registerJs($script);
?>
