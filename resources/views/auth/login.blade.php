<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System Login | SR Dream</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('inventory/css/login.css') }}" />
</head>
<body>

<div class="pos-login-container">
    
    <div class="brand-panel">
        <div class="brand-overlay"></div>
        <div class="brand-content">
            <div class="brand-logo">
                <i class="fa-solid fa-cash-register fa-2x text-warning"></i>
            </div>
            <h1 class="company-name">SR Dream</h1>
            <p class="system-tagline">Next-Generation Unified POS & Inventory Management System Suite</p>
            
            <div class="system-pills mt-4">
                <span class="system-pill"><i class="fa-solid fa-bolt me-1"></i> Fast Checkout</span>
                <span class="system-pill"><i class="fa-solid fa-boxes-stacked me-1"></i> Live Store Stock</span>
            </div>
        </div>
        <div class="brand-footer">
            <small>&copy; {{ date('Y') }} SR Dream Enterprise. All Rights Reserved.</small>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-wrapper">
            <div class="form-header">
                <h2>Welcome Back Staff</h2>
                <p class="text-muted">Provide authorization keys to boot register metrics terminal session.</p>
            </div>

            <x-auth-session-status class="auth-status-alert" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="modern-pos-form">
                @csrf

                <div class="form-group">
                    <label class="field-label">Operator Email Address</label>
                    <div class="input-field-wrapper">
                        <i class="fa-solid fa-user-tie field-icon"></i>
                        <input type="email" name="email" placeholder="operator@srdream.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <div class="error-msg"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="field-label">Security Access Password</label>
                    <div class="input-field-wrapper">
                        <i class="fa-solid fa-key field-icon"></i>
                        <input type="password" name="password" placeholder="••••••••••••" required autocomplete="current-password">
                    </div>
                    @error('password')
                        <div class="error-msg"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-utility">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember_me">
                        <span>Keep station terminal authenticated</span>
                    </label>
                </div>

                <button class="btn-pos-submit" type="submit">
                    <span>Initialize Terminal Session</span>
                    <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>
        </div>
    </div>

</div>

</body>
</html>