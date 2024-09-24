<?php
$session = session();
$xApp = $session->get('xApp');
?>

<aside class="main-sidebar elevation-4 sidebar-light-info">
    <a href="#" class="brand-link" style="text-align: center">
        <span class="brand-text font-weight-light"><b><?= $xApp ?></b></span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <?php if(isset($dados['tipo'])): ?>
                    <?php if($dados['tipo'] == 1): ?>
                        <li class="nav-item">
                            <a id="1" href="/inicio/admin" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Inicio</p>
                            </a>
                        </li>

                        <li class="nav-header" data-toggle="collapse" data-target="#menuControleAdmin" style="cursor:pointer;">
                            <span>            <i class="nav-icon fas fa-columns" style="margin-right: 3px;"></i>CONTROLE</span>
                            <i class="right fas fa-angle-right" id="iconControleAdmin"></i>
                        </li>
                        <ul id="menuControleAdmin" class="nav nav-treeview" style="display:none;">
                            <li class="nav-item">
                                <a id="2" href="/contadores" class="nav-link">
                                    <i class="nav-icon fas fa-building"></i>
                                    <p>Contadores</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="3" href="/relatorios/contadores" class="nav-link">
                                    <i class="nav-icon fas fa-file-pdf"></i>
                                    <p>Relatório</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="4" href="/configuracoes/edit" class="nav-link">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>Configurações</p>
                                </a>
                            </li>
                        </ul>

                    <?php elseif($dados['tipo'] == 2): ?>
                        <li class="nav-item">
                            <a id="1" href="/inicio/contador" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt">
                                <p>Inicio</p>
                            </a>
                        </li>

                        <?php if($dados['status'] == "Ativo" || $dados['status'] == "Vencido"): ?>
                            <li class="nav-header" data-toggle="collapse" data-target="#menuControleContador" style="cursor:pointer;">
                                <span>CONTROLE</span>
                                <i class="right fas fa-angle-right" id="iconControleContador"></i>
                            </li>
                            <ul id="menuControleContador" class="nav nav-treeview" style="display:none;">
                                <li class="nav-item">
                                    <a id="2" href="/empresas" class="nav-link">
                                        <i class="nav-icon fas fa-building"></i>
                                        <p>Empresas</p>
                                    </a>
                                </li>
                            </ul>

                            <li class="nav-header" data-toggle="collapse" data-target="#menuRelatoriosContador" style="cursor:pointer;">
                                <span>RELATÓRIOS</span>
                                <i class="right fas fa-angle-right" id="iconRelatoriosContador"></i>
                            </li>
                            <ul id="menuRelatoriosContador" class="nav nav-treeview" style="display:none;">
                                <li class="nav-item">
                                    <a id="3" href="/relatorios/empresas" class="nav-link">
                                        <i class="nav-icon fas fa-file-pdf"></i>
                                        <p>Empresas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a id="4" href="/relatorios/pagamentos" class="nav-link">
                                        <i class="nav-icon fas fa-file-pdf"></i>
                                        <p>Pagamentos</p>
                                    </a>
                                </li>
                            </ul>

                            <li class="nav-item" style="background: rgba(99, 218, 125);">
                                <a id="5" href="/suporte" class="nav-link" style="padding-left: 70px; color: white; font-weight: bold">
                                    <i class="nav-icon fas fa-headset"></i>
                                    <p>SUPORTE</p>
                                </a>
                            </li>
                        <?php endif;?>

                    <?php elseif($dados['tipo'] == 3): ?>
                        <li class="nav-item">
                            <a id="1" href="/inicio/emissor" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt">
                                <p>Inicio</p>
                            </a>
                        </li>

                        <li class="nav-header" data-toggle="collapse" data-target="#menuEmitirNotas" style="cursor:pointer;">
                            <span><i class="nav-icon fas fa-copy" style="margin-right: 3px;"></i>EMITIR NOTAS</span>
                            <i class="right fas fa-angle-right" id="iconEmitirNotas"></i>
                        </li>
                        <ul id="menuEmitirNotas" class="nav nav-treeview" style="display:none;">
                            <li class="nav-item">
                                <a id="2" href="/notaDeEntrada/emitir" class="nav-link">
                                    <i class="nav-icon far fa-circle text-success"></i>
                                    <p>Nota de Entrada</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="3" href="/notaDeSaida/emitir" class="nav-link">
                                    <i class="nav-icon far fa-circle text-primary"></i>
                                    <p>Nota de Saída</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="4" href="/notaDeDevolucao/emitir" class="nav-link">
                                    <i class="nav-icon far fa-circle text-warning"></i>
                                    <p>Nota de Devolução</p>
                                </a>
                            </li>
                        </ul>

                        <li class="nav-header" data-toggle="collapse" data-target="#menuControleGeral" style="cursor:pointer;">
                            <span><i class="nav-icon fas fa-columns" style="margin-right: 3px;"></i>CONTROLE GERAL</span>
                            <i class="right fas fa-angle-right" id="iconControleGeral"></i>
                        </li>
                        <ul id="menuControleGeral" class="nav nav-treeview" style="display:none;">
                            <li class="nav-item">
                                <a id="5" href="/clientes" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Clientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="6" href="/produtos" class="nav-link">
                                    <i class="nav-icon fas fa-box-open"></i>
                                    <p>Produtos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="7" href="/fornecedores" class="nav-link">
                                    <i class="nav-icon fas fa-dolly"></i>
                                    <p>Fornecedores</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="8" href="/transportadoras" class="nav-link">
                                    <i class="nav-icon fas fa-truck"></i>
                                    <p>Transportadoras</p>
                                </a>
                            </li>
                        </ul>

                        <li class="nav-header" data-toggle="collapse" data-target="#menuControleFiscal" style="cursor:pointer;">
                            <span><i class="nav-icon fas fa-copy" style="margin-right: 3px;"></i>CONTROLE FISCAL</span>
                            <i class="right fas fa-angle-right" id="iconControleFiscal"></i>
                        </li>
                        <ul id="menuControleFiscal" class="nav nav-treeview" style="display:none;">
                            <li class="nav-item">
                                <a id="9" href="/emissor/listaXMLsNFe" class="nav-link">
                                    <i class="nav-icon fas fa-code"></i>
                                    <p>NFe</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="10" href="/emissor/listaXMLsNFCe" class="nav-link">
                                    <i class="nav-icon fas fa-code"></i>
                                    <p>NFCe</p>
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>

<script>
    $(document).ready(function() {
        $('.nav-header').click(function() {
            var target = $(this).data('target');
            $(target).slideToggle();
            var icon = $(this).find('i');
            if (icon.hasClass('fa-angle-right')) {
                icon.removeClass('fa-angle-right').addClass('fa-angle-down');
            } else {
                icon.removeClass('fa-angle-down').addClass('fa-angle-right');
            }
        });
    });
</script>
