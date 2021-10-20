<?php
session_start();
?>
<!--**
 * @author Esdras Abreu
 *-->
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8' />
    <link href='css/core/main.min.css' rel='stylesheet' />
    <link href='css/daygrid/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="css/personalizado.css">
    <title>
        LABEST
    </title>
    
</head>

<body>
    <?php
    if (isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
    ?>
    
    <div class="text-center"><p class="h2 font-weight-light m-0 p-0"><strong>AGENDAMENTO LABEST</strong></p></div>
    
    <div id='calendar'></div>

    <div class="modal fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalhes do Agendamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="visevent">
                        <dl class="row">
                            <dt class="col-sm-3">ID do Agendamento</dt>
                            <dd class="col-sm-9" id="id"></dd>

                            <dt class="col-sm-3">Título do Agendamento</dt>
                            <dd class="col-sm-9" id="title"></dd>

                            <dt class="col-sm-3">Início do Agendamento</dt>
                            <dd class="col-sm-9" id="start"></dd>

                            <dt class="col-sm-3">Fim do Agendamento</dt>
                            <dd class="col-sm-9" id="end"></dd>
                        </dl>
                        <button hidden id="btn-editar" class="btn btn-warning btn-canc-vis">Editar</button>
                        <a hidden id="btn-apagar" href=""  class="btn btn-danger">Apagar</a>
                    </div>

                    

                    <div  class="formedit">
                        <span id="msg-edit"></span>

                        <form id="editevent" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Título</label>
                                <div class="col-sm-10">
                                    <select name="title" class="form-control" required >
                                        <option value="">Equipamento desejado</option>
                                        <option value="1">Balança com Capacidade 3 kg, 2 Casas Decimais</option>
                                        <option value="2">Balança com Capacidade 4 kg, 2 Casas Decimais</option>
                                        <option value="3">Balança com Capacidade 220 g, 4 Casas Decimais</option>
                                        <option value="4">Banho Maria / Bomba de Água Capacidade 18 Litros</option>
                                        <option value="5">Microscópio Óptico</option>
                                        <option value="6">Compressor 140 Libras</option>
                                        <option value="7">Bomba Servo Controlada/Atuador - Pressurizador</option>
                                        <option value="8">Estufa Potência 4000 W</option>
                                        <option value="9">Autoclave Vertical- Linha Av Potência 4 kW</option>
                                        <option value="10">Máquina Universal de Ensaios Mecânicos de 100 kN</option>
                                        <option value="11">Pórtico Universal</option>
                                        <option value="12">Macaco Hidráulico/Atuador</option>
                                        <option value="13">Termo Hidrômetro</option>
                                        <option value="14">Moinho de Facas</option>
                                        <option value="15">Talha com Capacidade 1000 KGS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Color</label>
                                <div class="col-sm-10">
                                    <select name="color" class="form-control" id="color">
                                        <option value="">Selecione</option>
                                        <option style="color:#FFD700;" value="#FFD700">Amarelo</option>
                                        <option style="color:#0071c5;" value="#0071c5">Azul Turquesa</option>
                                        <option style="color:#FF4500;" value="#FF4500">Laranja</option>
                                        <option style="color:#8B4513;" value="#8B4513">Marrom</option>
                                        <option style="color:#1C1C1C;" value="#1C1C1C">Preto</option>
                                        <option style="color:#436EEE;" value="#436EEE">Royal Blue</option>
                                        <option style="color:#A020F0;" value="#A020F0">Roxo</option>
                                        <option style="color:#40E0D0;" value="#40E0D0">Turquesa</option>
                                        <option style="color:#228B22;" value="#228B22">Verde</option>
                                        <option style="color:#8B0000;" value="#8B0000">Vermelho</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Início do Agendamento</label>
                                <div class="col-sm-10">
                                    <input type="text" name="start" class="form-control" id="start" onkeypress="DataHora(event, this)">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Final do Agendamento</label>
                                <div class="col-sm-10">
                                    <input type="text" name="end" class="form-control" id="end" onkeypress="DataHora(event, this)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="button" class="btn btn-primary btn-canc-edit">Cancelar</button>
                                    <button hidden type="submit" name="CadEvent" id="CadEvent" value="CadEvent" class="btn btn-warning">Salvar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="form-email-verificacao" class="formemail">
                        <form id="confirma-email" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id">

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" name="email" class="form-control" id="email-verificacao" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" id="btn-verificacao" class="btn btn-warning">Verificar Email</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="cadastrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Agendamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="msg-cad"></span>
                    <form id="addevent" method="POST" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Título</label>
                            <div class="col-sm-10">
                                <select name="title" class="form-control" id="title" required>
                                    <option value="">Equipamento desejado</option>
                                    <option value="">Equipamento desejado</option>
                                        <option value="1">Balança com Capacidade 3 kg, 2 Casas Decimais</option>
                                        <option value="2">Balança com Capacidade 4 kg, 2 Casas Decimais</option>
                                        <option value="3">Balança com Capacidade 220 g, 4 Casas Decimais</option>
                                        <option value="4">Banho Maria / Bomba de Água Capacidade 18 Litros</option>
                                        <option value="5">Microscópio Óptico</option>
                                        <option value="6">Compressor 140 Libras</option>
                                        <option value="7">Bomba Servo Controlada/Atuador - Pressurizador</option>
                                        <option value="8">Estufa Potência 4000 W</option>
                                        <option value="9">Autoclave Vertical- Linha Av Potência 4 kW</option>
                                        <option value="10">Máquina Universal de Ensaios Mecânicos de 100 kN</option>
                                        <option value="11">Pórtico Universal</option>
                                        <option value="12">Macaco Hidráulico/Atuador</option>
                                        <option value="13">Termo Hidrômetro</option>
                                        <option value="14">Moinho de Facas</option>
                                        <option value="15">Talha com Capacidade 1000 KGS</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Color</label>
                            <div class="col-sm-10">
                                <select name="color" class="form-control" id="color">
                                    <option value="">Selecione</option>
                                    <option style="color:#FFD700;" value="#FFD700">Amarelo</option>
                                    <option style="color:#0071c5;" value="#0071c5">Azul Turquesa</option>
                                    <option style="color:#FF4500;" value="#FF4500">Laranja</option>
                                    <option style="color:#8B4513;" value="#8B4513">Marrom</option>
                                    <option style="color:#1C1C1C;" value="#1C1C1C">Preto</option>
                                    <option style="color:#436EEE;" value="#436EEE">Royal Blue</option>
                                    <option style="color:#A020F0;" value="#A020F0">Roxo</option>
                                    <option style="color:#40E0D0;" value="#40E0D0">Turquesa</option>
                                    <option style="color:#228B22;" value="#228B22">Verde</option>
                                    <option style="color:#8B0000;" value="#8B0000">Vermelho</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Início do Agendamento</label>
                            <div class="col-sm-10">
                                <input type="text" name="start" class="form-control" id="start" onkeypress="DataHora(event, this)">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Final do Agendamento</label>
                            <div class="col-sm-10">
                                <input type="text" name="end" class="form-control" id="end" onkeypress="DataHora(event, this)">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nome</label>
                            <div class="col-sm-10">
                                <input type="text" name="nome" class="form-control" id="nome" placeholder="Nome" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" name="CadEvent" id="CadEvent" value="CadEvent" class="btn btn-success">Cadastrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src='js/core/main.min.js'></script>
    <script src='js/interaction/main.min.js'></script>
    <script src='js/daygrid/main.min.js'></script>
    <script src='js/core/locales/pt-br.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="js/personalizado.js"></script>
</body>

</html>