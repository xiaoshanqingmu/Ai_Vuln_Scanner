Set-Location (Split-Path -Parent $PSScriptRoot)

Write-Host "Running ruff..." -ForegroundColor Cyan
python -m ruff check .
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Running black (check)..." -ForegroundColor Cyan
python -m black --check .
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "OK" -ForegroundColor Green

