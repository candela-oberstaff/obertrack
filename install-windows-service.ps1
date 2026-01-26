# PowerShell script to install Obertrack Queue Worker as Windows Service
# Requires NSSM (Non-Sucking Service Manager)
# Download from: https://nssm.cc/download

$serviceName = "ObertrackQueueWorker"
$phpPath = "C:\php\php.exe"  # Adjust to your PHP installation path
$artisanPath = "C:\Users\cande\Documents\AnyDoBetter\obertrack\artisan"
$workingDir = "C:\Users\cande\Documents\AnyDoBetter\obertrack"

Write-Host "Installing $serviceName as Windows Service..." -ForegroundColor Green

# Check if NSSM is available
if (!(Get-Command nssm -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: NSSM not found. Please install NSSM first." -ForegroundColor Red
    Write-Host "Download from: https://nssm.cc/download" -ForegroundColor Yellow
    exit 1
}

# Remove existing service if it exists
$existingService = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
if ($existingService) {
    Write-Host "Removing existing service..." -ForegroundColor Yellow
    nssm stop $serviceName
    nssm remove $serviceName confirm
}

# Install the service
nssm install $serviceName $phpPath "$artisanPath queue:work --sleep=3 --tries=3 --timeout=90"
nssm set $serviceName AppDirectory $workingDir
nssm set $serviceName DisplayName "Obertrack Queue Worker"
nssm set $serviceName Description "Laravel queue worker for Obertrack WhatsApp mass messaging"
nssm set $serviceName Start SERVICE_AUTO_START
nssm set $serviceName AppStdout "$workingDir\storage\logs\queue-worker.log"
nssm set $serviceName AppStderr "$workingDir\storage\logs\queue-worker-error.log"

Write-Host "Service installed successfully!" -ForegroundColor Green
Write-Host "Starting service..." -ForegroundColor Green
nssm start $serviceName

Write-Host ""
Write-Host "Service Status:" -ForegroundColor Cyan
nssm status $serviceName

Write-Host ""
Write-Host "Useful commands:" -ForegroundColor Yellow
Write-Host "  Start:   nssm start $serviceName"
Write-Host "  Stop:    nssm stop $serviceName"
Write-Host "  Restart: nssm restart $serviceName"
Write-Host "  Status:  nssm status $serviceName"
Write-Host "  Remove:  nssm remove $serviceName"
