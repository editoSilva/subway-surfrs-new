<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);
*/

// Verificar se a sessão existe
session_start();

if (isset($_SESSION['email'])) {
    // Sessão não existe, redirecionar para outra página
    header("Location: ../painel");
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET['src']) || isset($_GET['utm_campaign']) || isset($_GET['utm_source']) || isset($_GET['utm_medium']) || isset($_GET['utm_campaign']) || isset($_GET['utm_term']) || isset($_GET['utm_content'])) {
    $_SESSION['src'] = $_GET['src'];
    $_SESSION['utm_source'] = isset($_GET['utm_source']) ? $_GET['utm_source'] : null;
    $_SESSION['utm_medium'] = isset($_GET['utm_medium']) ? $_GET['utm_medium'] : null;
    $_SESSION['utm_campaign'] = isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : null;
    $_SESSION['utm_term'] = isset($_GET['utm_term']) ? $_GET['utm_term'] : null;
    $_SESSION['utm_content'] = isset($_GET['utm_content']) ? $_GET['utm_content'] : null;
}

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

include_once './../connection.php';
include_once './../core/guards.php';

function validate_form($form)
{
    // email: unique, required, max 256, format = [a-z0-9._%+-]+@[a-z0-9.-]
    // senha: required, max 256
    // telefone: required, max 20, format (mask) = (99) 99999-9999
    // lead_aff: max 256, exists = appconfig.lead_aff

    // remove whitespaces
    $form = array_map('trim', $form);

    // remove -()' 'from phone number
    $form['phone'] = str_replace('-', '', $form['phone']);
    $form['phone'] = str_replace('(', '', $form['phone']);
    $form['phone'] = str_replace(')', '', $form['phone']);
    $form['phone'] = str_replace(' ', '', $form['phone']);

    $form['email'] = trim($form['email']);

    return guard(
        [
            'email' => $form['email'],
            'phone' => $form['phone'],
            'password' => $form['password'],
            'password_confirmation' => $form['password_confirmation'],
        ],
        [
            'email' => [
                'required',
                'lmax' => [256],
                // 'email',
                'unique' => ['appconfig', 'email']
            ],
            'password' => [
                'required',
                'lmax' => [256],
                // 'regex' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/'
            ],
            'phone' => [
                'required',
                'lmax' => [20],
                'regex' => '/^\d{11}$/'
            ],
            'password_confirmation' => [
                'required',
                'equals' => $form['password']
            ]
        ],
        [
            'email.required' => 'O e-mail é obrigatório',
            'email.lmax' => 'O e-mail deve conter no máximo 256 caracteres',
            // 'email.email' => 'O e-mail deve ser válido',
            'email.unique' => 'Já existe uma conta com esse e-mail',
            'phone.required' => 'O telefone é obrigatório',
            'phone.lmax' => 'O telefone deve conter no máximo 20 caracteres',
            'phone.regex' => 'O telefone deve conter 11 dígitos',
            'password.required' => 'A senha é obrigatória',
            'password.lmax' => 'A senha deve conter no máximo 256 caracteres',
            // 'password.regex' => 'A senha deve conter pelo menos 8 caracteres, uma letra maiúscula, uma letra minúscula e um número',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória',
            'password_confirmation.equals' => 'As senhas não coincidem',
        ]
    );
}

function create_salt()
{
    $text = md5(uniqid(rand(), true));
    return substr($text, 0, 3);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate form
    $errors = validate_form($_POST);

    $email = $_POST["email"];
    $senha = $_POST["password"];
    $telefone = $_POST["phone"];
    $pwd_hashed = password_hash($senha, PASSWORD_DEFAULT);

    $data = [
        'email' => $email,
        'senha' => $pwd_hashed,
        'telefone' => $telefone,
        'saldo' => 0,
        'indicados' => 0,
        'plano' => 0,
        'saldo_comissao' => 0,
        'origem' => isset($_SESSION['src']) ? $_SESSION['src'] : null
    ];

    if (!empty($_POST['lead_aff'])) {
        $data['lead_aff'] = $_POST['lead_aff'];
    }

    // if there are no errors
    if (empty($errors)) {
        // insert data into database
        insert('appconfig', $data);

        if (isset($_SESSION['src'])) {
            $existe = get_by('origem_src', ['origem' => $_SESSION['src']]);
            if (!empty($existe)) {
                update('origem_src', ['origem' => $_SESSION['src'], 'quantidade' => 'quantidade + 1'], ['origem' => $_SESSION['src']]);
            } else {
                insert('origem_src', [
                    'origem' => isset($_SESSION['src']) ? $_SESSION['src'] : null,
                    'quantidade' => 1
                ]);
            }
        }

        if ($_SESSION['utm_source'] || $_SESSION['utm_medium'] || $_SESSION['utm_campaign'] || $_SESSION['utm_term'] || $_SESSION['utm_content']) {
            insert('utm', [
                'email' => $email,
                'utm_campaign' => isset($_SESSION['utm_campaign']) ? $_SESSION['utm_campaign'] : null,
                'utm_source' => isset($_SESSION['utm_source']) ? $_SESSION['utm_source'] : null,
                'utm_medium' => isset($_SESSION['utm_medium']) ? $_SESSION['utm_medium'] : null,
                'utm_term' => isset($_SESSION['utm_term']) ? $_SESSION['utm_term'] : null,
                'utm_content' => isset($_SESSION['utm_content']) ? $_SESSION['utm_content'] : null,
            ]);
        }

        // get id
        $id = get_by('appconfig', ['email' => $email])['id'];

        // update link
        $link_afiliado = getenv('LINK_AFILIADO') . $id;
        update('appconfig', ['linkafiliado' => $link_afiliado], ['id' => $id]);

        // set email as session variable
        $_SESSION['email'] = $email;

        // redirect to deposit page
        header("Location: /jogodemo");
        exit();
    }
}


?>


<!DOCTYPE html>

<html lang="pt-br" class="w-mod-js wf-spacemono-n4-active wf-spacemono-n7-active wf-active w-mod-ix">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        #hero {
            background-image: url('../_next/background.png') !important;
            background-size: cover;
            background-color: #ffffff;
        }

        .wf-force-outline-none[tabindex="-1"]:focus {
            outline: none;
        }
    </style>
    <meta charset="pt-br">
    <title><?php echo $_ENV['NOME_SITE']; ?> 🌊</title>
    <meta property="og:image" content="../img/logo.png">

    <meta content="<?php echo $_ENV['NOME_SITE']; ?> 🌊" property="og:title">

    <meta name="twitter:image" content="../img/logo.png">
    <meta content="<?php echo $_ENV['NOME_SITE']; ?> 🌊" property="twitter:title">
    <?php require '../components/disable.php'; ?>
    <meta property="og:type" content="website">
    <meta content="summary_large_image" name="twitter:card">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="arquivos/page.css" rel="stylesheet" type="text/css">
    <script src="arquivos/webfont.js" type="text/javascript"></script>


    <script type="text/javascript">
        WebFont.load({
            google: {
                families: ["Space Mono:regular,700"]
            }
        });
    </script>


    <link rel="apple-touch-icon" sizes="180x180" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/logo.png">


    <link rel="icon" type="image/x-icon" href="../img/logo.png">

    <?php
    if (stmt("SELECT count(*) as count from pixels WHERE local='header' AND (pagina='cadastrar' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1) {
        $pixel = stmt("SELECT * from pixels WHERE local='header' AND (pagina='cadastrar' OR pagina='todas' OR pagina='todo-sem-jogo')");
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    } else {
        foreach (stmt("SELECT * from pixels WHERE local='header' AND (pagina='cadastrar' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) {
            echo file_get_contents('../uploads/pixels/' . $pixel['script']);
        }
    };
    ?>


</head>
<body>
<?php
if (stmt("SELECT count(*) as count from pixels WHERE local='body' AND (pagina='cadastrar' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1) {
    $pixel = stmt("SELECT * from pixels WHERE local='body' AND (pagina='cadastrar' OR pagina='todas' OR pagina='todo-sem-jogo')");
    echo file_get_contents('../uploads/pixels/' . $pixel['script']);
} else {
    foreach (stmt("SELECT * from pixels WHERE local='body' AND (pagina='cadastrar' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) {
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    }
};
?>
<div>
    <div data-collapse="small" data-animation="default" data-duration="400" role="banner" class="navbar w-nav">
        <div class="container w-container">
            <a href="../" aria-current="page" class="brand w-nav-brand" aria-label="home">
                <img src="arquivos/l2.png" loading="lazy" height="28" alt="" class="image-6">
                <div class="nav-link logo"><?php echo $_ENV['NOME_SITE']; ?></div>
            </a>
            <nav role="navigation" class="nav-menu w-nav-menu">
                <a href="../login" class="nav-link w-nav-link" style="max-width: 940px;">Jogar</a>
                <a href="../login" class="nav-link w-nav-link" style="max-width: 940px;">Login</a>
                <a href="../cadastrar" class="button nav w-button w--current">Cadastrar</a>
            </nav>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var menuButton = document.querySelector('.menu-button');
                    var navBar = document.querySelector('.nav-bar');

                    menuButton.addEventListener('click', function () {
                        if (navBar.style.display === 'block') {
                            navBar.style.display = 'none';
                        } else {
                            navBar.style.display = 'block';
                        }
                    });
                });
            </script>
            <style>
                .nav-bar {
                    display: none;
                    background-color: #333; /* Cor de fundo do menu */
                    padding: 20px; /* Espaçamento interno do menu */
                    width: 90%; /* Largura total do menu */

                    position: fixed; /* Fixa o menu na parte superior */
                    top: 0;
                    left: 0;
                    z-index: 1000; /* Garante que o menu está acima de outros elementos */
                }

                .nav-bar a {
                    color: white; /* Cor dos links no menu */
                    text-decoration: none;
                    padding: 10px; /* Espaçamento interno dos itens do menu */
                    display: block;
                    margin-bottom: 10px; /* Espaçamento entre os itens do menu */
                }

                .nav-bar a.login {
                    color: white; /* Cor do texto para o botão Login */
                }

                .button.w-button {
                    text-align: center;
                }

                .notification-container {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    padding: 20px;
                    box-sizing: border-box;
                    z-index: 1000;
                }

                .notification-container.error-message {
                    background-color: #f44336;
                    color: white;
                }

                .notification-container.success-message {
                    background-color: #4CAF50;
                    color: white;
                }

                .notification-container p {
                    margin: 0;
                }

                .error-message {
                    color: white;
                    background-color: rgba(255, 0, 0, 0.5);
                    padding: 10px;
                    margin-bottom: 10px;
                }

            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var menuButton = document.querySelector(' . menu - button');
                    var navBar = document.querySelector(' . nav - bar');

                    menuButton.addEventListener('click', function () {
                        // Toggle the visibility of the navigation bar
                        if (navBar.style.display === 'block') {
                            navBar.style.display = 'none';
                        } else {
                            navBar.style.display = 'block';
                        }
                    });
                });
            </script>


            <div class="w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button" tabindex="0"
                 aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
            </div>
            <div class="menu-button w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button"
                 tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                <div class="icon w-icon-nav-menu"></div>
            </div>
        </div>
        <div class="w-nav-overlay" data-wf-ignore="" id="w-nav-overlay-0"></div>
    </div>
    <div class="nav-bar">
        <a href="../login/" class="button w-button w--current">
            <div>Jogar</div>
        </a>
        <a href="../login/" class="button w-button w--current">
            <div>Login</div>
        </a>
        <a href="../cadastrar/" class="button w-button w--current">Cadastrar</a>
    </div>
    <section id="hero" class="hero-section dark wf-section">
        <div class="minting-container w-container">
            <img src="arquivos/asset.gif" loading="lazy" width="240" data-w-id="6449f730-ebd9-23f2-b6ad-c6fbce8937f7"
                 alt="Roboto #6340" class="mint-card-image">
            <h2>CADASTRO</h2>
            <p>É rapidinho, menos de 1 minuto. <br>Vai perder a oportunidade de faturar com o jogo do surfista?
                <br>
            </p>
            <img src="arquivos/banner.png" style="max-width: 640px;width: 100%;">


            <?php
            // Exibir a notificação de sucesso ou erro
            if (!empty($error_message)) {
                echo ' < div class="notification-container error-message" > ' . $error_message . '</div > ';
            } elseif (!empty($success_message)) {
                echo '<div class="notification-container success-message" > ' . $success_message . '</div > ';
            }
            ?>

            <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
                <div class="properties">
                    <h4 class="rarity-heading">E-mail</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="e-mail" class="large-input-field w-input <?php if (!empty($errors['email'])) {
                            echo 'input - error';
                        } ?>" maxlength="256" name="email"
                               placeholder="seuemail@gmail.com" id="email" required>
                        <?php if (!empty($errors['email'])) { ?>
                            <div class="error-message"><?php echo $errors['email'] ?></div>
                        <?php } ?>
                    </div>
                    <h4 class="rarity-heading">Telefone</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="tel" class="large-input-field w-input <?php if (!empty($errors['phone'])) {
                            echo 'input - error';
                        } ?>" maxlength="20" name="phone"
                               placeholder="Seu telefone" id="phone" required>
                        <?php if (!empty($errors['phone'])) { ?>
                            <div class="error-message"><?php echo $errors['phone'] ?></div>
                        <?php } ?>
                    </div>
                    <h4 class="rarity-heading">Senha</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="password" class="large-input-field w-input <?php if (!empty($errors['password'])) {
                            echo 'input - error';
                        } ?>" maxlength="256" name="password"
                               data-name="password" placeholder="Uma senha segura" id="password" required>
                        <?php if (!empty($errors['password'])) { ?>
                            <div class="error-message"><?php echo $errors['password'] ?></div>
                        <?php } ?>
                    </div>
                    <h4 class="rarity-heading">Confirme sua Senha</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="password"
                               class="large-input-field w-input <?php if (!empty($errors['password_confirmation'])) {
                                   echo 'input - error';
                               } ?>" maxlength="256"
                               name="password_confirmation" data-name="password" placeholder="Confirme sua senha"
                               id="myInput" required>
                        <?php if (!empty($errors['password_confirmation'])) { ?>
                            <div class="error-message"><?php echo $errors['password_confirmation'] ?></div>
                        <?php } ?>
                        <input type="hidden" name="lead_aff" id="lead_aff" value="">
                    </div>
                    <br>
                    <input type="checkbox" onclick="show_password()"> Mostrar senha
                </div>
                <script>
                    phoneInput = document.getElementById('phone');
                    phoneInput.addEventListener('input', function (e) {
                        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
                        e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                    });

                    function show_password() {
                        let passwordInput = document.getElementById('password');
                        let confirmPasswordInput = document.getElementById('myInput');

                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            confirmPasswordInput.type = 'text';
                        } else {
                            passwordInput.type = 'password';
                            confirmPasswordInput.type = 'password';
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        // Obtenha os parâmetros da URL
                        const urlParams = new URLSearchParams(window.location.search);
                        const leadAff = urlParams.get('aff');

                        // Atualize o valor do campo oculto 'lead_aff'
                        document.getElementById('lead_aff').value = leadAff;
                    });
                </script>
                <div class="">
                    <button type="submit" class="primary-button w-button">
                        <i class="fa fa-check fa-fw"></i>
                        Criar Conta
                    </button>
                    <br>
                    <p class="medium-paragraph _3-2vw-margin">Ao registrar você concorda com os
                        <a href="#">termos de serviço</a> e que possui pelo menos 18 anos.
                    </p>
                </div>
            </form>
        </div>
    </section>
    <div class="intermission wf-section"></div>
    <div id="rarity" class="rarity-section wf-section">
        <div class="minting-container left w-container">
            <div>
                <h2>💸 Tudo via PIX & na hora. 🔥</h2>
                <p>Seu dinheiro cai na hora na sua conta bancária, sem burocracia e sem taxas.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var notificationContainer = document.querySelector(' . notification - container');
            var loadingGif = document.querySelector('carregando . gif');

            // Exibir notificação após o envio do formulário
            <?php if (!empty($success_message) || !empty($error_message)) { ?>
            notificationContainer.style.display = 'block';
            <?php } ?>


            <?php if (empty($success_message) && empty($error_message)) { ?>
            loadingGif.style.display = 'block';
            setTimeout(function () {
                loadingGif.style.display = 'none';
                notificationContainer.style.display = 'block';
            }, 2000);
            <?php } ?>
        });
    </script>

    <div class="footer-section wf-section">
        <div class="domo-text">SUBWAY <br>
        </div>
        <div class="domo-text purple">SURFERS <br>
        </div>
        <div class="follow-test">© Copyright Postbrands Limited, with registered offices at Dr. M.L. King Boulevard 117,
            accredited by license GLH-16286002012.<a/></a> </div>
        <div class="follow-test">
            <a href="#">
                <strong class="bold-white-link">Termos de uso</strong>
            </a>
        </div>
        <div class="follow-test">contato@subwaysurf.com</div>
        <?php include '../components/footer.php'; ?>
    </div>
</div>


</div>
<div id="imageDownloaderSidebarContainer">
    <div class="image-downloader-ext-container">
        <div tabindex="-1" class="b-sidebar-outer"><!---->
            <div id="image-downloader-sidebar" tabindex="-1" role="dialog" aria-modal="false" aria-hidden="true"
                 class="b-sidebar shadow b-sidebar-right bg-light text-dark" style="width: 500px; display: none;">
                <!---->
                <div class="b-sidebar-body">
                    <div></div>
                </div><!---->
            </div><!----><!---->
        </div>
    </div>
</div>
<div style="visibility: visible;">
    <div></div>
    <div>

        <style>
            @-webkit-keyframes ww-51fbc3b8-5830-4bee-ad15-8955338512d0-launcherOnOpen {
                0% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }

                30% {
                    -webkit-transform: translateY(-5px) rotate(2deg);
                    transform: translateY(-5px) rotate(2deg);
                }

                60% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }


                90% {
                    -webkit-transform: translateY(-1px) rotate(0deg);
                    transform: translateY(-1px) rotate(0deg);

                }

                100% {
                    -webkit-transform: translateY(-0px) rotate(0deg);
                    transform: translateY(-0px) rotate(0deg);
                }
            }

            @keyframes ww-51fbc3b8-5830-4bee-ad15-8955338512d0-launcherOnOpen {
                0% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }

                30% {
                    -webkit-transform: translateY(-5px) rotate(2deg);
                    transform: translateY(-5px) rotate(2deg);
                }

                60% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }


                90% {
                    -webkit-transform: translateY(-1px) rotate(0deg);
                    transform: translateY(-1px) rotate(0deg);

                }

                100% {
                    -webkit-transform: translateY(-0px) rotate(0deg);
                    transform: translateY(-0px) rotate(0deg);
                }
            }

            @keyframes ww-51fbc3b8-5830-4bee-ad15-8955338512d0-widgetOnLoad {
                0% {
                    opacity: 0;
                }
                100% {
                    opacity: 1;
                }
            }

            @-webkit-keyframes ww-51fbc3b8-5830-4bee-ad15-8955338512d0-widgetOnLoad {
                0% {
                    opacity: 0;
                }
                100% {
                    opacity: 1;
                }
            }
        </style>
    </div>
</div>
<div class="elementor-element elementor-element-8ae2ec4 e-con-boxed e-con" data-id="8ae2ec4"
     data-element_type="container" data-settings="{" content_width
":"boxed"}"="">
<div class="e-con-inner">
    <div class="elementor-element elementor-element-64c1a37 elementor-widget elementor-widget-html" data-id="64c1a37"
         data-element_type="widget" data-widget_type="html.default">
        <div class="elementor-widget-container">
            <div class="elementor-element elementor-element-5e3d6ce elementor-widget elementor-widget-html"
                 data-id="5e3d6ce" data-element_type="widget" data-widget_type="html.default">
                <div class="elementor-widget-container">
                    <script src="https://cdn.jsdelivr.net/npm/notiflix@2.6.0/dist/notiflix-aio-2.6.0.min.js"></script>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    var position = "left-bottom"; // Posição da notificação na tela
    var animation = "from-left"; // Animação da notificação
    var timeout = 4000; // Tempo que a notificação fica visível na tela

    // Arrays com os nomes dos clientes e os pacotes/reservas
    var notifications = [
        '<strong>Adriana</strong> Acabou de Sacar <strong>R$ 160.00 </strong>',
        '<strong>Marcelo</strong> Acabou de Sacar <strong>R$ 150.00 </strong>',
        '<strong>Patricia</strong> Acabou de Sacar <strong>R$ 150.00 </strong>',
        '<strong>Carlos</strong> Acabou de Sacar <strong>R$ 130,00 </strong>',
        '<strong>Luiza</strong> Acabou de Sacar <strong>R$ 165,00 </strong>',
        '<strong>Fabricio</strong> Acabou de Sacar <strong>R$ 125,00 </strong>',
        '<strong>Matheus</strong> Acabou de Sacar <strong>R$ 178,00 </strong>',
        '<strong>Geovane</strong> Acabou de Sacar <strong>R$ 11120,00 </strong>',
        '<strong>Lia</strong> Acabou de Sacar <strong>R$ 175,00 </strong>',
        '<strong>Isabela</strong> Acabou de Sacar <strong>R$ 145,00 </strong>',
        '<strong>Marcio</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Maria</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Felipe</strong> Acabou de Sacar <strong>R$ 167,00 </strong>',
        '<strong>Geovane</strong> Acabou de Sacar <strong>R$ 175,00 </strong>',
        '<strong>Dávila</strong> Acabou de Sacar <strong>R$ 130,00 </strong>',
        '<strong>Levi</strong> Acabou de Sacar <strong>R$ 150.00 </strong>',
        '<strong>Enzo</strong> Acabou de Sacar <strong>R$ 165,00 </strong>',
        '<strong>Ravi</strong> Acabou de Sacar <strong>R$ 125,00 </strong>',
        '<strong>Aline</strong> Acabou de Sacar <strong>R$ 178,00 </strong>',
        '<strong>Jéssica</strong> Acabou de Sacar <strong>R$ 145,00 </strong>',
        '<strong>Leticia</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Antonela</strong> Acabou de Sacar <strong>R$ 13760,00 </strong>',
        '<strong>Babi</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Renan</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Wesley</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Thalysson</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Thay</strong> Acabou de Sacar <strong>R$ 617,00 </strong>',
        '<strong>Lira</strong> Acabou de Sacar <strong>R$ 162,00 </strong>',
        '<strong>Cefas</strong> Acabou de Sacar <strong>R$ 167,00 </strong>',
        '<strong>Tom</strong> Acabou de Sacar <strong>R$ 132,00 </strong>',
        '<strong>Rodrigo</strong> Acabou de Sacar <strong>R$ 167,00 </strong>',
        '<strong>Yuri</strong> Acabou de Sacar <strong>R$ 147,00 </strong>',
        '<strong>Dyno</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Caio</strong> Acabou de Sacar <strong>R$ 125,00 </strong>',

    ];

    var option = {
        position: position,
        cssAnimationStyle: animation,
        plainText: false,
        timeout: timeout
    };

    function show_notification() {
        // Escolhe uma notificação aleatória da lista
        var notification = notifications[Math.floor(Math.random() * notifications.length)];

        // Exibe a notificação com a cor configurada para verde (sucesso)
        Notiflix.Notify.Success(notification, option);

        // Define um intervalo fixo de 8 segundos para a próxima notificação
        setTimeout(show_notification, 8000);
    }

    // Inicia a primeira notificação após 8 segundos
    setTimeout(show_notification, 8000);
</script>
</body>

</html>