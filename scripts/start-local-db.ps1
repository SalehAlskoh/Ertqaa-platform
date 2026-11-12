param([string]$RuntimeRoot = (Join-Path $env:LOCALAPPDATA 'Ertqaa-MariaDB-11.4.13'))
$ErrorActionPreference = 'Stop'
$runtimePath = [System.IO.Path]::GetFullPath($RuntimeRoot)
$serverExe = Join-Path $runtimePath 'mariadb-11.4.13-winx64\bin\mariadbd.exe'
$configFile = Join-Path $runtimePath 'data\my.ini'
if (-not (Test-Path -LiteralPath $serverExe) -or -not (Test-Path -LiteralPath $configFile)) {
    throw 'Independent MariaDB runtime is missing. See docs/LOCAL_DEVELOPMENT.md.'
}
$listener = Get-NetTCPConnection -State Listen -LocalPort 3308 -ErrorAction SilentlyContinue
if ($listener) {
    $owner = Get-Process -Id $listener[0].OwningProcess
    if ($owner.Path -ne $serverExe) { throw 'Port 3308 belongs to another process; no service changed.' }
    Write-Output 'Independent MariaDB is already listening on port 3308.'
    exit 0
}
$process = Start-Process -FilePath $serverExe -ArgumentList "--defaults-file=`"$configFile`"" -WorkingDirectory $runtimePath -WindowStyle Hidden -PassThru
for ($attempt = 0; $attempt -lt 30; $attempt++) {
    if ($process.HasExited) { throw "MariaDB exited. Inspect $runtimePath\server.log" }
    $connection = [System.Net.Sockets.TcpClient]::new()
    try { $connection.Connect('127.0.0.1', 3308); Write-Output 'Independent MariaDB is listening on 127.0.0.1:3308.'; exit 0 }
    catch { Start-Sleep -Milliseconds 200 }
    finally { $connection.Dispose() }
}
throw 'MariaDB startup timed out; inspect its own server.log. Other services were not changed.'
