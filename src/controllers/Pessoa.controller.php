<?php
require_once('./src/models/Pessoa.model.php');
require_once('./src/models/Funcionario.model.php');
require_once('./src/controllers/Geral.controller.php');
require_once('./src/general.php');

class ControllerPessoa {
  public function lista($app){
    $app->validarUsuario($app, "F");

    $mdlPessoa = new ModelPessoa();
    $lista_pessoa = $mdlPessoa->todas($app->db);

    $dados = [
      'pagina' => 'pessoa/lista',
      'pessoas' => $lista_pessoa
    ];
    $controllerGeral = new ControllerGeral();
    $controllerGeral->carregaTela($app, $dados);
  }

  public function listajson($app) {
    $mdlPessoa = new ModelPessoa();
    $lista_pessoa = $mdlPessoa->todas($app->db);

    echo(json_encode($lista_pessoa));
  }

  public function cadastro($app) {
    $app->validarUsuario($app, "F");

    $dados = [
      'pagina' => 'pessoa/cadastro',
      'acao' => "cadastrar",
      'pessoa' => [
        "id" => 0,
        "nome" => "",
        "email" => "",
        "telefone" => "",
        "data_nascimento" => "",
        "data_nascimento_original" => ""
      ]
    ];
    $controllerGeral = new ControllerGeral();
    $controllerGeral->carregaTela($app, $dados);
  }

  public function cadastrar($app) {
    $app->validarUsuario($app, "F", true);

    $pessoa = [
      "nome" => $_POST['nome'],
      "email" => $_POST['email'] == "" ? null : $_POST['email'],
      "telefone" => $_POST['telefone'] == "" ? null :  $_POST['telefone'],
      "data_nascimento" => $_POST['data_nascimento'] == "" ? null : $_POST['data_nascimento']
    ];

    if("" === $pessoa['nome'] || null === $pessoa['nome']) {
      echo(json_encode([ "success" => false, "message" => "Nome é obrigatório" ]));
      exit();
    }

    if("" === $pessoa['email'] || null === $pessoa['email']){
      echo(json_encode([ "success" => false, "message" => "Email é obrigatório" ]));
      exit();
    }

    if(!validateEmail($pessoa['email'])){
      echo(json_encode([ "success" => false, "message" => "Email inválido" ]));
      exit();
    }

    if("" !== $pessoa['data_nascimento'] && null !== $pessoa['data_nascimento']){
      if(date_create($pessoa['data_nascimento']) > date_create('now')){
        echo(json_encode([ "success" => false, "message" => "Data de nascimento não pode ser depois de hoje" ]));
        exit();
      }
    }

    $mdlPessoa = new ModelPessoa();
    $result = $mdlPessoa->cadastrar($app->db, $pessoa);

    echo(json_encode([ "success" => $result, "message" => "" ]));
  }

  public function deletar($app) {
    $app->validarUsuario($app, "F", true);

    $id = $_GET['id'];

    $mdlFuncionario = new ModelFuncionario();
    $lista_funcionarios = $mdlFuncionario->todosIdPessoa($app->db, $id);

    if(count($lista_funcionarios) > 0){
      echo(json_encode([ "success" => false, "message" => "Não é possível excluir a pessoa pois já existe vínculos" ]));
      exit();
    }

    $mdlPessoa = new ModelPessoa();
    $result = $mdlPessoa->excluir($app->db, $id);

    echo(json_encode([ "success" => $result, "message" => "" ]));
  }

  public function alteracao($app) {
    $app->validarUsuario($app, "F");
    $mdlPessoa = new ModelPessoa();
    $id = $_GET['id'];

    $dados = [
      'pagina' => 'pessoa/cadastro',
      'acao' => "alterar",
      'pessoa' => $mdlPessoa->uma($app->db, $id)
    ];
    $controllerGeral = new ControllerGeral();
    $controllerGeral->carregaTela($app, $dados);
  }

  public function alterar($app) {
    $app->validarUsuario($app, "F", true);

    $pessoa = [
      "id" => $_POST['id'],
      "nome" => $_POST['nome'],
      "email" => $_POST['email'] == "" ? null : $_POST['email'],
      "telefone" => $_POST['telefone'] == "" ? null :  $_POST['telefone'],
      "data_nascimento" => $_POST['data_nascimento'] == "" ? null : $_POST['data_nascimento']
    ];

    if(null === $pessoa['id'] || 0 === $pessoa['id']){
      echo(json_encode([ "success" => false, "message" => "Erro grave ao alterar a pessoa, atualize a página" ]));
      exit();
    }

    if("" === $pessoa['nome'] || null === $pessoa['nome']) {
      echo(json_encode([ "success" => false, "message" => "Nome é obrigatório" ]));
      exit();
    }

    if("" === $pessoa['email'] || null === $pessoa['email']){
      echo(json_encode([ "success" => false, "message" => "Email é obrigatório" ]));
      exit();
    }

    if(!validateEmail($pessoa['email'])){
      echo(json_encode([ "success" => false, "message" => "Email inválido" ]));
      exit();
    }

    if("" !== $pessoa['data_nascimento'] && null !== $pessoa['data_nascimento']){
      if(date_create($pessoa['data_nascimento']) > date_create('now')){
        echo(json_encode([ "success" => false, "message" => "Data de nascimento não pode ser depois de hoje" ]));
        exit();
      }
    }

    $mdlPessoa = new ModelPessoa();
    $result = $mdlPessoa->alterar($app->db, $pessoa);

    echo(json_encode([ "success" => $result, "message" => "" ]));
  }
}
?>
