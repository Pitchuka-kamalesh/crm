<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form | VFSTR Vignan's University Admissions</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-primary: #1e3a8a;
            --brand-blue: #2563eb;
            --brand-blue-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-light: #cbd5e1;
            --bg-body: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #ffffff;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /* Top Header Bar */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        }

        .nav-inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0.65rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
        }

        .brand-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .nav-menu-links {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            list-style: none;
        }

        .nav-item-link {
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .nav-item-link:hover,
        .nav-item-link.active {
            color: #2563eb;
        }

        .btn-apply-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #0f2b5c;
            color: #ffffff;
            padding: 0.55rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-apply-nav:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        /* Main Registration Split Section */
        .page-wrapper {
            max-width: 1320px;
            width: 100%;
            margin: 0 auto;
            padding: 3rem 1.5rem 4rem 1.5rem;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 3.5rem;
            align-items: start;
            flex: 1;
        }

        /* Left Hero Column */
        .hero-left {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding-top: 2rem;
        }

        .hero-headline {
            font-size: 3.2rem;
            font-weight: 900;
            color: #0b1e42;
            line-height: 1.15;
            letter-spacing: -0.025em;
        }

        .hero-headline span {
            color: #1e3a8a;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: #475569;
            line-height: 1.6;
            max-width: 520px;
        }

        .accreditation-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .accred-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Right Form Card (Matching Screenshot exactly) */
        .registration-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 2.25rem 2.25rem 2rem 2.25rem;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.02);
        }

        .card-header-title {
            text-align: center;
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .reg-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }

        .input-text-field {
            width: 100%;
            padding: 0.78rem 1rem;
            border: 1px solid #94a3b8;
            border-radius: 8px;
            font-size: 0.88rem;
            color: #0f172a;
            font-family: inherit;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .input-text-field:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .input-text-field::placeholder {
            color: #64748b;
            font-size: 0.86rem;
        }

        .phone-group-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .phone-flag-box {
            position: absolute;
            left: 12px;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.86rem;
            font-weight: 700;
            color: #475569;
            pointer-events: none;
            padding-right: 8px;
            border-right: 1px solid #cbd5e1;
        }

        .phone-with-flag {
            padding-left: 4.25rem;
        }

        .select-field {
            width: 100%;
            padding: 0.78rem 1rem;
            border: 1px solid #94a3b8;
            border-radius: 8px;
            font-size: 0.88rem;
            color: #0f172a;
            font-family: inherit;
            background: #ffffff;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        /* Choose Program Level Tabs */
        .level-section {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-top: 0.15rem;
        }

        .level-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
        }

        .level-pills-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.4rem;
            background: #f1f5f9;
            padding: 0.35rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .level-pill-btn {
            padding: 0.55rem 0.5rem;
            border: none;
            background: transparent;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .level-pill-btn.pill-active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        /* Captcha Card */
        .captcha-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.65rem 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
        }

        .captcha-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.84rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
        }

        .captcha-checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #2563eb;
        }

        .captcha-badge-secure {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.62rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Register Submit Button */
        .btn-register-submit {
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.96rem;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
            transition: all 0.2s ease;
            margin-top: 0.5rem;
        }

        .btn-register-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.5);
            background: linear-gradient(135deg, #1e40af 0%, #172554 100%);
        }

        .login-back-link {
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
            margin-top: 0.85rem;
        }

        .login-back-link a {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }

        .login-back-link a:hover {
            text-decoration: underline;
        }

        /* Banner alerts */
        .status-alert {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 960px) {
            .page-wrapper {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding-top: 1.5rem;
            }
            .hero-headline {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <header class="top-navbar">
        <div class="nav-inner">
            <a href="/" class="brand-logo-area">
                <img src="/images/vignan-logo.png" alt="Vignan's University" class="brand-logo-img">
                <div style="display:flex;flex-direction:column;">
                    <span style="font-size:0.95rem;font-weight:900;color:#0b1e42;letter-spacing:-0.01em;">VIGNAN'S</span>
                    <span style="font-size:0.65rem;font-weight:700;color:#2563eb;letter-spacing:0.04em;">DEEMED TO BE UNIVERSITY &bull; NAAC A+</span>
                </div>
            </a>

            <nav class="nav-menu-links">
                <a href="/" class="nav-item-link">Home</a>
                <a href="/" class="nav-item-link">Programs</a>
                <a href="/" class="nav-item-link">Process</a>
                <a href="/" class="nav-item-link">Testimonials</a>
                <a href="/" class="nav-item-link">Contact</a>
            </nav>

            <a href="{{ route('login') }}" class="btn-apply-nav">
                <span>Sign In Portal &rarr;</span>
            </a>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-wrapper">

        <!-- Left Hero Area -->
        <section class="hero-left">
            <h1 class="hero-headline">
                Empower as <br>leader at <br><span>Vignan's University</span>
            </h1>

            <p class="hero-subtitle">
                Join a legacy of academic excellence, cutting-edge research, state-of-the-art innovation centers, and top-tier placements across multinational leaders.
            </p>

            <div class="accreditation-row">
                <div class="accred-badge">
                    <span style="color:#059669;">✔</span> NAAC A+ Accredited
                </div>
                <div class="accred-badge">
                    <span style="color:#2563eb;">✔</span> NIRF Ranked Top 70
                </div>
                <div class="accred-badge">
                    <span style="color:#d97706;">✔</span> UGC Category-1 Graded
                </div>
                <div class="accred-badge">
                    <span style="color:#7c3aed;">✔</span> ABET &amp; NBA Certified
                </div>
            </div>
        </section>

        <!-- Right Side: Registration Form -->
        <section class="registration-card">
            <h2 class="card-header-title">Registration Form</h2>

            @if(session('status'))
                <div class="status-alert">
                    &bull; {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-alert">
                    @foreach($errors->all() as $err)
                        <div>&bull; {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="reg-form">
                @csrf
                <input type="hidden" name="program_level" id="input_program_level" value="UG">

                <!-- Applicant Full Name -->
                <div>
                    <input type="text" 
                           name="applicant_name" 
                           value="{{ old('applicant_name') }}" 
                           required 
                           placeholder="Applicant's Full Name - As Per SSC Records" 
                           class="input-text-field">
                </div>

                <!-- Email Address -->
                <div>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           placeholder="Enter Your e-mail address" 
                           class="input-text-field">
                </div>

                <!-- Mobile Number -->
                <div class="phone-group-wrap">
                    <span class="phone-flag-box">
                        <span style="font-size:1.1rem;">🇮🇳</span> +91
                    </span>
                    <input type="tel" 
                           name="mobile" 
                           id="reg_mobile"
                           value="{{ old('mobile', $prefilledPhone) }}" 
                           required 
                           maxlength="10" 
                           placeholder="Enter Mobile No." 
                           oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                           class="input-text-field phone-with-flag">
                </div>

                <!-- Password and Confirm Password -->
                <div class="form-row-2col">
                    <input type="password" 
                           name="password" 
                           required 
                           placeholder="Password (min 8 char)" 
                           class="input-text-field">

                    <input type="password" 
                           name="password_confirmation" 
                           required 
                           placeholder="Confirm Password" 
                           class="input-text-field">
                </div>

                <!-- Select Campus & State -->
                <div class="form-row-2col">
                    <select name="campus_id" required class="select-field">
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->campus_id }}" {{ old('campus_id') == $campus->campus_id ? 'selected' : '' }}>
                                {{ $campus->campus_short_name }} ({{ $campus->city }})
                            </option>
                        @endforeach
                    </select>

                    <select name="state" class="select-field">
                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                        <option value="Telangana">Telangana</option>
                        <option value="Tamil Nadu">Tamil Nadu</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Maharashtra">Maharashtra</option>
                        <option value="Other States">Other State / UT</option>
                    </select>
                </div>

                <!-- Choose Program Level (Pills) -->
                <div class="level-section">
                    <label class="level-label">Choose Program Level</label>
                    <div class="level-pills-row">
                        <button type="button" class="level-pill-btn pill-active" onclick="selectProgramLevel('UG', this)">Undergraduate</button>
                        <button type="button" class="level-pill-btn" onclick="selectProgramLevel('PG', this)">Postgraduate</button>
                        <button type="button" class="level-pill-btn" onclick="selectProgramLevel('Doctoral', this)">Doctoral</button>
                    </div>
                </div>

                <!-- Course & Specialization -->
                <div class="form-row-2col">
                    <select name="progid" id="course_select" required class="select-field" onchange="updateBranches(this.value)">
                        <option value="">Select Course</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->program_id }}" data-level="{{ $prog->program_level }}" {{ old('progid') == $prog->program_id ? 'selected' : '' }}>
                                {{ $prog->program_short_name }} - {{ $prog->program_long_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="branch_id" id="branch_select" class="select-field">
                        <option value="">Specialization</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->branch_id }}" data-prog="{{ $branch->program_id }}" {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                {{ $branch->branch_long_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Captcha Mock Checkbox -->
                <div class="captcha-card">
                    <label class="captcha-checkbox-label">
                        <input type="checkbox" name="captcha_verified" checked required>
                        <span>I'm not a robot</span>
                    </label>
                    <div class="captcha-badge-secure">
                        <svg width="22" height="22" fill="none" stroke="#2563eb" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>SECURE</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-register-submit">
                    <span>Register Now &rarr;</span>
                </button>

                <div class="login-back-link">
                    Already registered? <a href="{{ route('login') }}">Sign In to Student Portal</a>
                </div>
            </form>
        </section>

    </main>

    <!-- Dynamic Course & Branch Scripts -->
    <script>
        const allPrograms = @json($programs);
        const allBranches = @json($branches);

        function selectProgramLevel(level, btnEl) {
            document.querySelectorAll('.level-pill-btn').forEach(b => b.classList.remove('pill-active'));
            btnEl.classList.add('pill-active');
            document.getElementById('input_program_level').value = level;

            const courseSelect = document.getElementById('course_select');
            courseSelect.innerHTML = '<option value="">Select Course</option>';

            const filterLevel = (level === 'Doctoral') ? ['PhD', 'Doctoral'] : [level];
            const filtered = allPrograms.filter(p => filterLevel.includes(p.program_level));

            filtered.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.program_id;
                opt.textContent = `${p.program_short_name} - ${p.program_long_name}`;
                courseSelect.appendChild(opt);
            });

            if (filtered.length > 0) {
                courseSelect.value = filtered[0].program_id;
                updateBranches(filtered[0].program_id);
            } else {
                updateBranches('');
            }
        }

        function updateBranches(progId) {
            const branchSelect = document.getElementById('branch_select');
            branchSelect.innerHTML = '<option value="">Specialization</option>';

            if (!progId) return;

            const filtered = allBranches.filter(b => String(b.program_id) === String(progId));
            if (filtered.length > 0) {
                filtered.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.branch_id;
                    opt.textContent = b.branch_long_name;
                    branchSelect.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = 1;
                opt.textContent = 'General / Core Track';
                branchSelect.appendChild(opt);
            }
        }

        // Initialize with default UG level
        document.addEventListener('DOMContentLoaded', () => {
            const currentLevel = document.getElementById('input_program_level').value || 'UG';
            const activeBtn = Array.from(document.querySelectorAll('.level-pill-btn'))
                .find(b => b.textContent.toLowerCase().includes(currentLevel.toLowerCase()));
            if (activeBtn) {
                selectProgramLevel(currentLevel, activeBtn);
            }
        });
    </script>
</body>
</html>

