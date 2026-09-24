param([string]$Version = '')

$src = Join-Path $PSScriptRoot 'papervoyage'

if (-not $Version) {
    $styleCss = Join-Path $src 'style.css'
    if (Test-Path $styleCss) {
        $content = Get-Content $styleCss -Raw
        if ($content -match 'Version:\s*([0-9\.]+)') {
            $Version = $matches[1]
        }
    }
}
if (-not $Version) {
    $Version = '1.4.11'
}

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

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
