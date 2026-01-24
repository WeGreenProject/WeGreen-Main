# SCRIPT DE IMPORTAÇÃO DA BASE DE DADOS WEGREEN

param(
    [Parameter(Mandatory=$true)]
    [string]$SqlFile,
    [string]$MySQLPath = "C:\xampp\mysql\bin",
    [string]$DatabaseName = "wegreen",
    [string]$Username = "root",
    [string]$Password = "",
    [switch]$CreateDatabase
)

Write-Host "IMPORTAÇÃO DA BASE DE DADOS WEGREEN" -ForegroundColor Cyan
Write-Host ""

if (-not (Test-Path $SqlFile)) {
    Write-Host "ERRO: Ficheiro não encontrado: $SqlFile" -ForegroundColor Red
    exit 1
}

$mysqlPath = Join-Path $MySQLPath "mysql.exe"

if ($CreateDatabase) {
    Write-Host "Criando base de dados..." -ForegroundColor Yellow
    $createDb = "CREATE DATABASE IF NOT EXISTS $DatabaseName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    $createDb | & $mysqlPath -u $Username $(if($Password){"-p$Password"}) 2>&1 | Out-Null
}

Write-Host "Importando dados..." -ForegroundColor Yellow
Get-Content $SqlFile | & $mysqlPath -u $Username $(if($Password){"-p$Password"}) $DatabaseName 2>&1 | Out-Null

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERRO ao importar" -ForegroundColor Red
    exit 1
}

Write-Host "Ativando event scheduler..." -ForegroundColor Yellow
"SET GLOBAL event_scheduler = ON;" | & $mysqlPath -u $Username $(if($Password){"-p$Password"}) 2>&1 | Out-Null

Write-Host "" 
Write-Host "IMPORTAÇÃO CONCLUÍDA!" -ForegroundColor Green
Write-Host ""

# Verificações
Write-Host "Verificando..." -ForegroundColor Yellow
$planosCount = "SELECT COUNT(*) FROM planos;" | & $mysqlPath -u $Username $(if($Password){"-p$Password"}) $DatabaseName -s -N 2>&1
Write-Host "Planos: $planosCount" -ForegroundColor White

$events = "SHOW EVENTS FROM $DatabaseName;" | & $mysqlPath -u $Username $(if($Password){"-p$Password"}) -s -N 2>&1
if ($events -match "expire_old_plans") {
    Write-Host "Events: OK" -ForegroundColor Green
} else {
    Write-Host "Events: NÃO ENCONTRADOS" -ForegroundColor Red
}
