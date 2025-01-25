<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Veterinaria</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .form-control {
            width: 100%;
            padding: 12px 40px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #764ba2;
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #764ba2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-login:hover {
            background: #667eea;
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 1rem;
            color: white;
            background-color: #ef4444;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Sistema Veterinaria</h1>
            <p>Ingrese sus credenciales</p>
        </div>

        @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route("login") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    const alert = document.createElement('div');
                    alert.className = 'alert';
                    alert.textContent = data.message;
                    document.querySelector('.login-header').insertAdjacentElement('afterend', alert);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
