<!DOCTYPE html>
<html>
<head><title>Error {{ $exception->getStatusCode() }}</title></head>
<body style="font-family:sans-serif;text-align:center;padding:80px;background:#f8f9fa">
  <h1 style="color:#dc3545">⚠️ Error {{ $exception->getStatusCode() }}</h1>
  <p style="font-size:18px">{{ $exception->getMessage() ?: 'Something went wrong. Please try again.' }}</p>
  <a href="/" style="background:#0d6efd;color:#fff;padding:10px 20px;border-radius:5px;text-decoration:none">Go Home</a>
</body>
</html>