@echo off
title EVENTIX - Cloudflare Public Tunnel
echo =======================================================
echo          EVENTIX LIVE SHARING (CLOUDFLARE)
echo =======================================================
echo.
echo Menghubungkan localhost:80 ke Cloudflare Tunnel...
echo Mohon tunggu sebentar sampai link publik muncul...
echo.
echo Tekan Ctrl + C jika ingin menghentikan sharing.
echo =======================================================
echo.
"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://localhost:80
pause
