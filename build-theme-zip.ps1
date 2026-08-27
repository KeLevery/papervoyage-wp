# Build papervoyage theme zip (force forward slashes + top-level papervoyage/ folder)
# Usage: powershell -File build-theme-zip.ps1 [version]   e.g. .\build-theme-zip.ps1 1.4.2
param([string]$Version = '1.4.1')

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$src = Join-Path $PSScriptRoot 'papervoyage'
$out = Join-Path $PSScriptRoot "papervoyage-$Version.zip"

Remove-Item $out -Force -ErrorAction SilentlyContinue

$fs  = [System.IO.File]::Open($out, [System.IO.FileMode]::Create)
$zip = New-Object System.IO.Compression.ZipArchive($fs, [System.IO.Compression.ZipArchiveMode]::Create)

Get-ChildItem $src -Recurse -File | ForEach-Object {
    $rel = $_.FullName.Substring($src.Length + 1).Replace('\', '/')
    $entryName = 'papervoyage/' + $rel
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, $entryName, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
    Write-Host $entryName
}

$zip.Dispose()
$fs.Dispose()
Write-Host "DONE: $out"
