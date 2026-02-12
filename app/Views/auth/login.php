<!DOCTYPE html>
<html>

<head>
    <title>Login</title>

    <link href="<?= base_url('vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/sb-admin-2.min.css') ?>" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 50%, #1cc88a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border-radius: 25px;
            background: #ffffff;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 50px 40px;
            animation: fadeIn 0.6s ease-in-out;
        }

        .login-icon-wrapper {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .login-icon-wrapper i {
            font-size: 38px;
            color: #fff;
        }

        .login-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #858796;
            margin-bottom: 30px;
        }

        .form-control-user {
            border-radius: 50px;
            padding-left: 45px;
            height: 48px;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 18px;
            transform: translateY(-50%);
            color: #858796;
        }

        .btn-login {
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="login-card">
                    <!-- ICON -->
                    <div class="login-icon-wrapper">
                        <i class="fas fa-user-shield"></i>
                    </div>

                    <!-- TITLE -->
                    <div class="text-center">
                        <h2 class="login-title text-gray-800">Admin Login</h2>
                        <div class="login-subtitle">
                            Silakan masuk untuk melanjutkan
                        </div>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- FORM -->
                    <form method="post" action="<?= base_url('login/process') ?>">
                        <?= csrf_field() ?>

                        <div class="form-group position-relative">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="username"
                                class="form-control form-control-user"
                                placeholder="Username"
                                required>
                        </div>

                        <div class="form-group position-relative">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password"
                                class="form-control form-control-user"
                                placeholder="Password"
                                required>
                        </div>

                        <button type="submit"
                            class="btn btn-primary btn-login btn-block shadow">
                            Login
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            © <?= date('Y') ?> Asset Management System
                        </small>
                    </div>

                </div>

            </div>
        </div>
    </div>

</body>

</html>