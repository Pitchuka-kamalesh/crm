$phpPath = "$env:USERPROFILE\.config\herd\bin\php85\php.exe"
if (Test-Path $phpPath) {
    & $phpPath "$PSScriptRoot\artisan" @args
} else {
    & php "$PSScriptRoot\artisan" @args
}

