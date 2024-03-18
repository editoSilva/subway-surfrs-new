<aside class="left-sidebar" data-sidebarbg="skin5">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="pt-4">
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                       href="<?php echo $_ENV['BASE_URL'] ?>/adm"
                       aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Página inicial</span></a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                       href="<?php echo $_ENV['BASE_URL'] ?>/adm/GGR"
                       aria-expanded="false"><i class="mdi mdi-margin"></i><span class="hide-menu">GGR</span></a>
                </li>
                <li class="sidebar-item">
                    <a
                            class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo $_ENV['BASE_URL'] ?>/adm/configuracoes"
                            aria-expanded="false"
                    ><i class="mdi mdi-settings"></i
                        ><span class="hide-menu">Configurações</span></a
                    >
                </li>

                <li class="sidebar-item">
                    <a
                            class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo $_ENV['BASE_URL'] ?>/adm/planos"
                            aria-expanded="false"
                    ><i class="mdi mdi-square-inc-cash"></i
                        ><span class="hide-menu">Afiliados</span></a
                    >
                </li>

                <li class="sidebar-item">
                    <a
                            class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo $_ENV['BASE_URL'] ?>/adm/bonus"
                            aria-expanded="false"
                    ><i class="mdi mdi-gift"></i
                        ><span class="hide-menu">Bônus</span></a
                    >
                </li>


                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                       href="<?php echo $_ENV['BASE_URL'] ?>/adm/usuarios" aria-expanded="false"><i
                                class="mdi mdi-account"></i><span
                                class="hide-menu">Usuários</span></a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                       href="<?php echo $_ENV['BASE_URL'] ?>/adm/depositos" aria-expanded="false"><i
                                class="mdi mdi-cash"></i><span class="hide-menu">Depositos</span></a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                       href="<?php echo $_ENV['BASE_URL'] ?>/adm/saques" aria-expanded="false"><i
                                class="mdi mdi-cash"></i><span
                                class="hide-menu">Saques Apostadores</span></a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                       href="<?php echo $_ENV['BASE_URL'] ?>/adm/saques-afiliados" aria-expanded="false"><i
                                class="mdi mdi-cash"></i><span class="hide-menu">Saques Afiliados</span></a>
                </li>

                <li class="sidebar-item">
                    <a
                            class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo $_ENV['BASE_URL'] ?>/adm/pixels"
                            aria-expanded="false"
                    ><i class="mdi mdi-code-tags"></i
                        ><span class="hide-menu">Pixels</span></a
                    >
                </li>

                <?php if($_ENV['NOME_DESENVOLVEDORA'] != 'Profit Igaming'){ ?>
                    <li class="sidebar-item">
                        <a
                                class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="<?php echo $_ENV['BASE_URL'] ?>/adm/franqueado"
                                aria-expanded="false"
                        ><i class="mdi mdi-chart-line"></i
                            ><span class="hide-menu">Seja um Franqueado</span></a
                        >
                    </li>


                    <li class="sidebar-item">
                        <a
                                class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="<?php echo $_ENV['BASE_URL'] ?>/adm/premios"
                                aria-expanded="false"
                        ><i class="mdi mdi-star-circle"></i
                            ><span class="hide-menu">Premiações</span></a
                        >
                    </li>
                <?php } ?>


            </ul>
        </nav>
    </div>
</aside>