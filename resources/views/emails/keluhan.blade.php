<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Keluh Kesah</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #374151;
            background: #fef2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(220, 38, 38, 0.1);
        }

        .header-red {
            background: #dc2626;
            padding: 20px;
            color: white;
            text-align: center;
        }

        .header-red h1 {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .content {
            padding: 30px;
        }

        .phone-section {
            background: #fee2e2;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .phone-icon {
            color: #dc2626;
            flex-shrink: 0;
        }

        .phone-text h3 {
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }

        .phone-text p {
            color: #374151;
            font-size: 15px;
            font-weight: 500;
            margin: 0;
        }

        .message-section {
            border: 2px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
        }

        .message-section h3 {
            color: #dc2626;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message-content {
            color: #4b5563;
            font-size: 15px;
        }

        .time {
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .footer {
            background: #fef2f2;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-red">
            <h1>Keluh Kesah Baru!</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Phone Section -->
            <div class="phone-section">
                <div class="phone-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <div class="phone-text">
                    <h3>DARI NOMOR TELEPON</h3>
                    <p>{{ $phone }}</p>
                </div>
            </div>

            <!-- Message Section -->
            <div class="message-section">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    ISI KELUH KESAH
                </h3>
                <div class="message-content">
                    {{ $keluh }}
                </div>
            </div>

            <!-- Time -->
            <div class="time">
                Dikirim: {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            TenAspiration &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
