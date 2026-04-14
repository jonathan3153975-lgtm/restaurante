<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Restaurante SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: var(--text-main);
            background:
                radial-gradient(circle at 20% 5%, rgba(201, 161, 74, 0.12), transparent 25%),
                radial-gradient(circle at 85% 15%, rgba(201, 161, 74, 0.08), transparent 30%),
                var(--bg-main);
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            padding: 1rem;
        }

        .login-card {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01)),
                var(--bg-panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.38);
            max-width: 420px;
            margin: 0 auto;
        }

        .login-header {
            text-align: center;
            padding: 2.5rem 2rem 2rem;
            border-bottom: 1px solid var(--border);
        }

        .login-brand {
            display: inline-block;
            width: 48px;
            height: 48px;
            background: linear-gradient(160deg, var(--gold-soft), var(--gold));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 8px 16px rgba(201, 161, 74, 0.2);
        }

        .login-brand i {
            font-size: 1.5rem;
            color: #18140b;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #f4f4f4;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: var(--text-soft);
            margin-top: 0.5rem;
            margin-bottom: 0;
        }

        .login-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--gold-soft);
            font-size: 1rem;
        }

        .form-control {
            background: #17191c;
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control::placeholder {
            color: var(--text-soft);
        }

        .form-control:focus {
            background: #1d1f24;
            color: #fff;
            border-color: rgba(201, 161, 74, 0.6);
            box-shadow: 0 0 0 0.2rem rgba(201, 161, 74, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 0.85rem 1rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 10px;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-size: 0.9rem;
        }

        .alert-error i {
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .login-footer {
            padding: 1.5rem 2rem;
            background: rgba(20, 21, 24, 0.5);
            border-top: 1px solid var(--border);
            border-radius: 0 0 16px 16px;
        }

        .demo-info {
            background: rgba(201, 161, 74, 0.08);
            border: 1px solid rgba(201, 161, 74, 0.25);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0;
        }

        .demo-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gold-soft);
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .demo-label i {
            font-size: 0.85rem;
        }

        .demo-credentials {
            font-size: 0.85rem;
            color: var(--text-soft);
            font-family: 'Courier New', monospace;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .demo-credentials strong {
            color: var(--text-main);
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .login-header {
                padding: 2rem 1.5rem 1.5rem;
            }

            .login-body {
                padding: 1.5rem;
            }

            .login-footer {
                padding: 1.25rem 1.5rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .demo-credentials {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="login-brand">
                <i class="bi bi-door-open"></i>
            </div>
            <h1 class="login-title">Restaurante SaaS</h1>
            <p class="login-subtitle">Acesso ao Sistema</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="/login">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) $csrf, ENT_QUOTES, 'UTF-8') ?>">

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-at"></i>
                        E-mail
                    </label>
                    <input 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        type="email" 
                        placeholder="seu@email.com"
                        required
                        autofocus
                    >
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i>
                        Senha
                    </label>
                    <input 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        type="password" 
                        placeholder="Digite sua senha"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button class="btn btn-gold btn-submit" type="submit">
                    <i class="bi bi-arrow-right-circle"></i>
                    Entrar no Sistema
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <div class="demo-info">
                <div class="demo-label">
                    <i class="bi bi-lightbulb-fill"></i>
                    Credenciais de Demonstração
                </div>
                <div class="demo-credentials">
                    <span><strong>Usuário:</strong> admin@restaurante.local</span>
                    <span><strong>Senha:</strong> 123456</span>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
