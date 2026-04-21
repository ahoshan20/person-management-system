<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {{ $statusCode ?? 500 }} - {{ $title ?? 'Something went wrong' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="bg-[#FAFAFA] text-gray-900 antialiased min-h-screen flex flex-col items-center justify-center p-4 sm:p-6">

    <div class="w-full max-w-lg bg-white border border-gray-200 rounded-xl shadow-sm p-8 sm:p-10 text-center relative overflow-hidden">

        {{-- Top color bar — changes by error type --}}
        <div class="absolute top-0 left-0 w-full h-1
            @if(($statusCode ?? 500) == 404) bg-yellow-500
            @elseif(($statusCode ?? 500) == 403) bg-orange-500
            @elseif(($statusCode ?? 500) == 429) bg-blue-500
            @elseif(($statusCode ?? 500) == 401) bg-purple-500
            @else bg-red-500
            @endif">
        </div>

        {{-- Icon — changes by error type --}}
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-6
            @if(($statusCode ?? 500) == 404) bg-yellow-50 border border-yellow-100
            @elseif(($statusCode ?? 500) == 403) bg-orange-50 border border-orange-100
            @elseif(($statusCode ?? 500) == 429) bg-blue-50 border border-blue-100
            @elseif(($statusCode ?? 500) == 401) bg-purple-50 border border-purple-100
            @else bg-red-50 border border-red-100
            @endif">
            @if(($statusCode ?? 500) == 404)
                <iconify-icon icon="solar:ghost-linear" stroke-width="1.5"
                    class="text-3xl text-yellow-500"></iconify-icon>
            @elseif(($statusCode ?? 500) == 403)
                <iconify-icon icon="solar:shield-warning-linear" stroke-width="1.5"
                    class="text-3xl text-orange-500"></iconify-icon>
            @elseif(($statusCode ?? 500) == 429)
                <iconify-icon icon="solar:clock-circle-linear" stroke-width="1.5"
                    class="text-3xl text-blue-500"></iconify-icon>
            @elseif(($statusCode ?? 500) == 401)
                <iconify-icon icon="solar:lock-linear" stroke-width="1.5"
                    class="text-3xl text-purple-500"></iconify-icon>
            @else
                <iconify-icon icon="solar:danger-triangle-linear" stroke-width="1.5"
                    class="text-3xl text-red-600"></iconify-icon>
            @endif
        </div>

        {{-- Error Code Badge --}}
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium mb-4
            @if(($statusCode ?? 500) == 404) bg-yellow-50 text-yellow-700 border border-yellow-200
            @elseif(($statusCode ?? 500) == 403) bg-orange-50 text-orange-700 border border-orange-200
            @elseif(($statusCode ?? 500) == 429) bg-blue-50 text-blue-700 border border-blue-200
            @elseif(($statusCode ?? 500) == 401) bg-purple-50 text-purple-700 border border-purple-200
            @else bg-red-50 text-red-700 border border-red-200
            @endif">
            <span>HTTP {{ $statusCode ?? 500 }}</span>
        </div>

        {{-- Title --}}
        <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 mb-3">
            {{ $title ?? 'Something went wrong' }}
        </h1>

        {{-- Message --}}
        <p class="text-sm text-gray-500 mb-8 leading-relaxed max-w-sm mx-auto">
            {{ $message ?? 'We encountered an unexpected error while processing your request. Our technical team has been automatically notified.' }}
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.history.back()"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" stroke-width="1.5"></iconify-icon>
                Go Back
            </button>
            <a href="/"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 border border-transparent rounded-md shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                <iconify-icon icon="solar:home-2-linear" stroke-width="1.5"></iconify-icon>
                Return to Directory
            </a>
        </div>

        {{-- Technical Details (Expandable) --}}
        <div class="mt-8 pt-6 border-t border-gray-100 text-left hidden" id="error-details">
            <span class="block text-xs font-medium text-gray-700 mb-2">
                Error Details (Provide to Support)
            </span>
            <div class="bg-gray-50 rounded border border-gray-200 p-3 font-mono text-xs text-gray-500 overflow-x-auto space-y-1">
                <div><span class="text-gray-400">Status Code :</span> {{ $statusCode ?? 500 }}</div>
                @if(!empty($errorCode))
                <div><span class="text-gray-400">Error Code  :</span> {{ $errorCode }}</div>
                @endif
                @if(!empty($file))
                <div><span class="text-gray-400">File        :</span> {{ $file }}</div>
                @endif
                @if(!empty($line))
                <div><span class="text-gray-400">Line        :</span> {{ $line }}</div>
                @endif
                <div><span class="text-gray-400">URL         :</span> {{ request()->fullUrl() }}</div>
                <div><span class="text-gray-400">Method      :</span> {{ request()->method() }}</div>
                <div><span class="text-gray-400">IP          :</span> {{ request()->ip() }}</div>
                <div><span class="text-gray-400">Timestamp   :</span> <span id="timestamp"></span></div>
            </div>
        </div>

        <button onclick="document.getElementById('error-details').classList.toggle('hidden')"
            class="mt-6 text-xs font-medium text-gray-400 hover:text-gray-600 transition-colors">
            Show technical details
        </button>
    </div>

    {{-- Footer --}}
    <div class="mt-8 text-center text-xs text-gray-400">
        Personnel Management System &copy; <span id="year"></span>
    </div>

    <script>
        document.getElementById('timestamp').textContent = new Date().toISOString();
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>