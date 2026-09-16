<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ $title }}</title>
    <style>
        :root {
            color-scheme: light dark;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f8fafc;
            text-align: center;
        }

        .card {
            width: 100%;
            max-width: 26rem;
            padding: 2.5rem 2rem;
            border-radius: 1rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px -12px rgba(15, 23, 42, 0.12);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 3.5rem;
            padding: 0.375rem 0.875rem;
            margin: 0 0 1.25rem;
            border-radius: 9999px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        h1 {
            font-size: 1.375rem;
            line-height: 1.4;
            font-weight: 600;
            margin: 0 0 0.5rem;
            color: #0f172a;
        }

        p.description {
            margin: 0 0 1.75rem;
            font-size: 0.9375rem;
            line-height: 1.6;
            color: #64748b;
        }

        .button {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            background: #0f172a;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }

        .button:hover {
            background: #1e293b;
        }

        @media (prefers-color-scheme: dark) {
            body { background: #0b1120; }

            .card {
                background: #0f172a;
                border-color: #1e293b;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2), 0 8px 24px -12px rgba(0, 0, 0, 0.5);
            }

            .badge { background: #1e293b; color: #cbd5e1; }
            h1 { color: #f1f5f9; }
            p.description { color: #94a3b8; }
            .button { background: #e2e8f0; color: #0f172a; }
            .button:hover { background: #cbd5e1; }
        }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">{{ $code }}</span>
        <h1>{{ $title }}</h1>
        <p class="description">{{ $description }}</p>
        <a class="button" href="{{ url('/') }}">
            {{ __('error-pages::messages.back_home') }}
        </a>
    </div>
</body>
</html>
