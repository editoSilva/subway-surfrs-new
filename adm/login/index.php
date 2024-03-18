<?php
$vida_sessao = 15 * 60; // 15 minutos em segundos
session_set_cookie_params($vida_sessao);
session_start();

try {

    include './../../connection.php';
    require '../../core/guards.php';
    $conn = connect();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Consultar o banco de dados para verificar o login
        $user = stmt("SELECT * FROM appconfig WHERE email = ? AND is_adm=1", 's', [$email]);
        if (count($user) == 0 || !password_verify($senha, $user['senha'])) {
            $error_message = "Credenciais inválidas";
        } else {
            if ($user) {
                $bloc = $user['bloc'];

                if ($bloc == 1) {
                    $error_message = "Usuário bloqueado. Contate o suporte!";
                }

                // Credenciais corretas, armazene o email na sessão para uso posterior
                $_SESSION["emailadm"] = $email;
                $successMessage = "Login efetuado com sucesso!";
                header('Location: ../');
                exit;
            } else {
                // Credenciais incorretas, exiba uma mensagem de erro
                $error_message = "Credenciais incorretas. Tente novamente.";
            }
        }
    }

    $conn->close();
} catch (Exception $e) {
    header('Location: ../login');
    exit;
}
?>


<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta
            name="keywords"
            content="Dashboard "
    />
    <meta
            name="description"
            content="Matrix Admin Lite Free Version is powerful and clean admin dashboard template, inpired from Bootstrap Framework"
    />
    <meta name="robots" content="noindex,nofollow"/>
    <title>Login ADM</title>

    <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="../assets/images/<?php echo $_ENV['LOGOICON_DESENVOLVEDORA'];?>"
    />

    <link href="../dist/css/style.min.css" rel="stylesheet"/>

</head>


<style>

    body {
        background-color: #343a40;
        margin: 0;
        padding: 0;
    }


    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .rotate-image {
        animation: rotate infinite 15s linear;
    }


</style>


<body>
<div class="main-wrapper">


    <div
            class="
          auth-wrapper
          d-flex
          no-block
          justify-content-center
          align-items-center
          bg-dark
        "
    >
        <div class="auth-box bg-dark border-top border-secondary">
            <div>
                <div class="text-center pt-3 pb-3">
              <span class="db"
              ><img src="../assets/images/<?php echo $_ENV['LOGO_DESENVOLVEDORA'];?>" alt="logo" width="80" height="80" class="rotate-image"/></span>
                </div>


                <?php
                if (isset($erro)) {
                    echo "<p style='color:red;'>$erro</p>";
                }
                ?>
                <!-- Form -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                    <div class="row pb-4">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                      <span
                              class="input-group-text bg-dark text-white h-100"
                              id="basic-addon1"
                      ><i class="mdi mdi-account fs-4"></i
                          ></span>
                                </div>
                                <input
                                        type="text"
                                        class="form-control form-control-lg"
                                        placeholder="Email"
                                        aria-label="Email"
                                        name="email"
                                        id="email"
                                        aria-describedby="basic-addon1"
                                        required
                                />
                            </div>


                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                      <span
                              class="input-group-text bg-dark text-white h-100"
                              id="basic-addon2"
                      ><i class="mdi mdi-lock fs-4"></i
                          ></span>
                                </div>
                                <input
                                        type="Password"
                                        class="form-control form-control-lg"
                                        placeholder="Password"
                                        id="senha"
                                        name="senha"
                                        aria-label="Password"

                                        aria-describedby="basic-addon1"
                                        required
                                />
                            </div>

                        </div>
                    </div>
                    <div class="row border-top border-secondary">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="pt-3 d-grid">
                                    <button
                                            class="btn btn-block btn-lg btn-info"
                                            type="submit"
                                    >
                                        Logar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

</body>
</html>
