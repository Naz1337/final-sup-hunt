<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Innovisory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
          :root {
            --primary-bg: #C8D9E6;
            --gradient-start: #2193b0;
            --gradient-end: #6dd5ed;
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            min-height: 100vh;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .login-btn {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 147, 176, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-container flex items-center justify-center p-6">
        <div class="login-card w-full max-w-md p-8">
            <div class="text-center mb-8">
                <img src="{{ asset('images/innovisory.png') }}" alt="Innovisory Logo" class="h-20 mx-auto mb-4">
                <h2 class="text-3xl font-bold text-gray-800">Forgot Password</h2>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-2 mt-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 text-red-800 p-2 mt-4 rounded">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        <form method="POST" action="{{ route('forgot.password.send') }}" class="space-y-6">
            @csrf

            <div class="mb-4">
                <label for="matric_id" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input id="email" name="email" type="email" required
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900 placeholder-gray-400"
                       placeholder="Enter your email address">
            </div>

            <button type="submit" class="login-btn w-full text-white py-3 px-6 rounded-lg font-medium">
                Send Temporary Password
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-blue-600 hover:text-blue-800">Back to Home</a>
        </div>
    </div>
</body>
</html> 