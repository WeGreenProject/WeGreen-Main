# SCRIPT DE EXPORTACAO DA BASE DE DADOS WEGREEN

param(
    [string]$OutputPath = ".\wegreen_backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql",
    [string]$MySQLPath = "C:\xampp\mysql\bin",
    [string]$DatabaseName = "wegreen",
    [string]$Username = "root",
    [string]$Password = ""
)

Write-Host "EXPORTACAO DA BASE DE DADOS WEGREEN" -ForegroundColor Cyan
Write-Host ""

$mysqldumpPath = Join-Path $MySQLPath "mysqldump.exe"
if (-not (Test-Path $mysqldumpPath)) {
    Write-Host "ERRO: mysqldump.exe nao encontrado" -ForegroundColor Red
    exit 1
}

Write-Host "[1/3] Verificando conexao..." -ForegroundColor Yellow
$mysqlPath = Join-Path $MySQLPath "mysql.exe"

try {
    $testCmd = "SELECT 1;"
    $null = $testCmd | & $mysqlPath -u $Username $(if($Password){"-p$Password"}) $DatabaseName 2>&1
    Write-Host "OK Conexao estabelecida!" -ForegroundColor Green
} catch {
    Write-Host "ERRO: Nao foi possivel conectar" -ForegroundColor Red
    exit 1
}
Write-Host ""

Write-Host "[2/3] Exportando base de dados..." -ForegroundColor Yellow

$arguments = @(
    "-u$Username"
    if ($Password) { "-p$Password" }
    "--events"
    "--routines"
    "--triggers"
    "--single-transaction"
    "--result-file=$OutputPath"
    $DatabaseName
)

& $mysqldumpPath @arguments 2>&1 | Out-Null

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERRO: Falha ao exportar" -ForegroundColor Red
    exit 1
}

Write-Host "OK Exportado com sucesso!" -ForegroundColor Green
Write-Host ""

Write-Host "[3/3] Adicionando configuracoes..." -ForegroundColor Yellow

$sqlHeader = "-- BACKUP WEGREEN`nSET GLOBAL event_scheduler = ON;`nSET FOREIGN_KEY_CHECKS = 0;`n`n"

$originalContent = Get-Content $OutputPath -Raw -Encoding UTF8
$sqlFooter = "`n`nSET FOREIGN_KEY_CHECKS = 1;`nSHOW EVENTS;"
$newContent = $sqlHeader + $originalContent + $sqlFooter
Set-Content -Path $OutputPath -Value $newContent -Encoding UTF8

Write-Host "OK Concluido!" -ForegroundColor Green
Write-Host ""

$fileInfo = Get-Item $OutputPath
$fileSizeMB = [math]::Round($fileInfo.Length / 1MB, 2)

Write-Host "EXPORTACAO CONCLUIDA!" -ForegroundColor Green
Write-Host "Ficheiro: $OutputPath" -ForegroundColor White
Write-Host "Tamanho: $fileSizeMB MB" -ForegroundColor Gray
Write-Host ""
Write-Host "Para importar use:" -ForegroundColor Yellow
Write-Host ".\import_database.ps1 -SqlFile `"$OutputPath`"" -ForegroundColor Cyan
