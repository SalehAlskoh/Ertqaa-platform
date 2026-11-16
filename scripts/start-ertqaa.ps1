#requires -Version 5.1
param(
    [string]$RuntimeRoot = (Join-Path $env:LOCALAPPDATA 'Ertqaa-MariaDB-11.4.13'),
    [ValidateRange(5, 120)][int]$StartupTimeoutSeconds = 30
)

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$runtimePath = [System.IO.Path]::GetFullPath($RuntimeRoot)
$serverExe = Join-Path $runtimePath 'mariadb-11.4.13-winx64\bin\mariadbd.exe'
$dbScript = Join-Path $PSScriptRoot 'start-local-db.ps1'
$startupMutex = $null
$mutexAcquired = $false
$exitCode = 0

function Get-PortListener([int]$Port) {
    Get-NetTCPConnection -State Listen -LocalPort $Port -ErrorAction SilentlyContinue | Select-Object -First 1
}

function Test-LocalPort([int]$Port) {
    $client = [System.Net.Sockets.TcpClient]::new()
    try {
        $pending = $client.ConnectAsync('127.0.0.1', $Port)
        return $pending.Wait(300) -and $client.Connected
    }
    catch { return $false }
    finally { $client.Dispose() }
}

function Wait-LocalPort([int]$Port, [System.Diagnostics.Process]$Process) {
    $deadline = [DateTime]::UtcNow.AddSeconds($StartupTimeoutSeconds)
    do {
        if ($Process -and $Process.HasExited) {
            throw "توقفت عملية Laravel قبل اكتمال التشغيل. راجع storage/logs/ertqaa-server-*.log."
        }
        if (Test-LocalPort $Port) { return }
        Start-Sleep -Milliseconds 250
    } while ([DateTime]::UtcNow -lt $deadline)
    throw "انتهت مهلة انتظار المنفذ $Port. راجع سجل الخدمة ثم أعد التشغيل."
}

function Assert-ErtqaaServer($Listener) {
    $owner = Get-CimInstance Win32_Process -Filter ('ProcessId = ' + $Listener.OwningProcess)
    $command = if ($owner.CommandLine) { $owner.CommandLine.Replace('/', '\') } else { '' }
    if ($owner.Name -ne 'php.exe' -or $command.IndexOf($projectRoot + '\', [StringComparison]::OrdinalIgnoreCase) -lt 0) {
        throw 'المنفذ 8000 مستخدم بواسطة عملية لا يمكن تأكيد أنها تخص هذا المشروع. لم تُوقف أو تُغيّر أي عملية.'
    }
}

try {
    # Serialize repeated launches, including double-clicks, without stopping any service.
    $startupMutex = [System.Threading.Mutex]::new($false, 'Local\Ertqaa-Startup-3308-8000')
    try { $mutexAcquired = $startupMutex.WaitOne($StartupTimeoutSeconds * 1000) }
    catch [System.Threading.AbandonedMutexException] { $mutexAcquired = $true }
    if (-not $mutexAcquired) { throw 'توجد محاولة تشغيل أخرى قيد التنفيذ. انتظر اكتمالها ثم أعد الأمر عند الحاجة.' }

    foreach ($file in @('artisan', 'vendor\autoload.php', 'bootstrap\app.php', '.env')) {
        if (-not (Test-Path -LiteralPath (Join-Path $projectRoot $file) -PathType Leaf)) {
            throw "ملف مطلوب غير موجود داخل المشروع: $file. لم تتغير إعدادات المشروع."
        }
    }
    if (-not (Test-Path -LiteralPath $dbScript -PathType Leaf)) { throw 'سكربت scripts/start-local-db.ps1 غير موجود.' }
    $phpExe = (Get-Command php -CommandType Application -ErrorAction SilentlyContinue | Select-Object -First 1).Source
    if (-not $phpExe) { throw 'تعذر العثور على PHP في PATH. أضف مجلد PHP الحالي إلى PATH ثم أعد الأمر.' }

    $dbListener = Get-PortListener 3308
    if ($dbListener) {
        if ((Get-Process -Id $dbListener.OwningProcess).Path -ne $serverExe) {
            throw 'المنفذ 3308 يخص عملية أخرى غير MariaDB المستقلة المعتمدة. لم تُغيّر أي خدمة.'
        }
        Write-Host '[1/3] MariaDB تعمل بالفعل على المنفذ 3308؛ لن تُنشأ عملية أخرى.' -ForegroundColor Green
    }
    else {
        Write-Host '[1/3] يجري تشغيل MariaDB المستقلة باستخدام السكربت الحالي...'
        $windowsPowerShell = Join-Path $env:WINDIR 'System32\WindowsPowerShell\v1.0\powershell.exe'
        # A separate shell contains the existing script's exit statements and output.
        $previousPreference = $ErrorActionPreference
        try {
            $ErrorActionPreference = 'Continue'
            $null = & $windowsPowerShell -NoProfile -ExecutionPolicy Bypass -File $dbScript -RuntimeRoot $runtimePath 2>&1
            $dbStartCode = $LASTEXITCODE
        }
        finally { $ErrorActionPreference = $previousPreference }
        if ($dbStartCode -ne 0) {
            throw "تعذر تشغيل MariaDB. تحقق من ملفات التشغيل ومن السجل $runtimePath\server.log."
        }
    }
    Wait-LocalPort -Port 3308

    # Bootstrap Laravel itself: use its effective configuration, including any config cache.
    # Only read queries are issued. Suppress raw exceptions so credentials never reach output.
    $phpCheck = @'
try {
    if (!extension_loaded('pdo_mysql')) { exit(10); }
    require $argv[1].'/vendor/autoload.php';
    $app = require $argv[1].'/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $connection = $app->make('db')->connection();
    if ($connection->getDriverName() !== 'mysql'
        || !in_array($connection->getConfig('host'), ['127.0.0.1', 'localhost'], true)
        || (int)$connection->getConfig('port') !== 3308
        || $connection->getDatabaseName() !== 'ertqaa_platform') { exit(11); }
    try {
        $row = $connection->selectOne('SELECT 1 AS connected, DATABASE() AS current_database');
        if ((int)$row->connected !== 1 || $row->current_database !== 'ertqaa_platform') { exit(11); }
    } catch (Throwable $e) {
        $previous = $e->getPrevious();
        $info = $previous instanceof PDOException ? $previous->errorInfo : ($e instanceof PDOException ? $e->errorInfo : []);
        if (($info[1] ?? null) === 1045) { exit(12); }
        if (($info[1] ?? null) === 1049) { exit(13); }
        exit(14);
    }
    exit(0);
} catch (Throwable $e) { exit(15); }
'@
    $previousPreference = $ErrorActionPreference
    try {
        $ErrorActionPreference = 'Continue'
        $null = & $phpExe -d display_errors=0 -d log_errors=0 -r $phpCheck $projectRoot 2>&1
        $dbCheckCode = $LASTEXITCODE
    }
    finally { $ErrorActionPreference = $previousPreference }
    switch ($dbCheckCode) {
        0 { Write-Host '[2/3] نجح اتصال Laravel الفعلي بقاعدة ertqaa_platform على 3308.' -ForegroundColor Green }
        10 { throw 'امتداد PHP pdo_mysql غير متاح في نسخة PHP الحالية.' }
        11 { throw 'إعداد Laravel الفعلي لا يطابق mysql وقاعدة ertqaa_platform المحلية على 3308. راجع .env والكاش؛ لم تتغير الإعدادات.' }
        12 { throw 'رفضت MariaDB بيانات دخول التطبيق. راجع بيانات الاتصال في .env دون نشر كلمة المرور.' }
        13 { throw 'قاعدة ertqaa_platform غير موجودة. لم يُنشئ السكربت قواعد أو ينفذ Migrations.' }
        14 { throw 'المنفذ 3308 جاهز لكن فشل اتصال Laravel أو استعلام القراءة. راجع إعدادات الاتصال وسجل MariaDB.' }
        default { throw 'تعذر تهيئة Laravel للتحقق من الاتصال. راجع PHP واعتمادات المشروع وسجل Laravel.' }
    }

    $webListener = Get-PortListener 8000
    if ($webListener) {
        Assert-ErtqaaServer $webListener
        Write-Host '[3/3] Laravel يعمل بالفعل على المنفذ 8000؛ لن تُنشأ عملية أخرى.' -ForegroundColor Green
    }
    else {
        Write-Host '[3/3] يجري تشغيل Laravel على 127.0.0.1:8000 في الخلفية...'
        $logPrefix = Join-Path $projectRoot ('storage\logs\ertqaa-server-' + (Get-Date -Format 'yyyyMMdd-HHmmss') + '-' + $PID)
        $serverArguments = @(('"' + (Join-Path $projectRoot 'artisan') + '"'), 'serve', '--host=127.0.0.1', '--port=8000', '--no-reload')
        $webProcess = Start-Process -FilePath $phpExe -ArgumentList $serverArguments -WorkingDirectory $projectRoot -WindowStyle Hidden -PassThru -RedirectStandardOutput ($logPrefix + '.out.log') -RedirectStandardError ($logPrefix + '.err.log')
        Wait-LocalPort -Port 8000 -Process $webProcess
        Assert-ErtqaaServer (Get-PortListener 8000)
    }

    # A real public page also checks the running HTTP process, not just a TCP listener.
    try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/' -UseBasicParsing -TimeoutSec 10 -MaximumRedirection 5 }
    catch { throw 'الخادم يستمع على 8000 لكن لم تنجح استجابة المنصة. راجع storage/logs/laravel.log وسجلات ertqaa-server-*.log؛ لم تُوقف أي عملية.' }
    if ($response.StatusCode -ne 200) { throw 'لم تُرجع المنصة استجابة HTTP ناجحة على المنفذ 8000.' }
    Write-Host 'ارتقاء جاهزة: http://127.0.0.1:8000' -ForegroundColor Green
}
catch {
    Write-Host ('فشل تشغيل ارتقاء: ' + $_.Exception.Message) -ForegroundColor Red
    $exitCode = 1
}
finally {
    if ($mutexAcquired) { $startupMutex.ReleaseMutex() }
    if ($startupMutex) { $startupMutex.Dispose() }
}
exit $exitCode
