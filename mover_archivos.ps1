# Script para mover los archivos de la subcarpeta salon_de_belleza a la raíz
$source = "salon_de_belleza"
$destination = "."

if (Test-Path $source) {
    Write-Host "Iniciando la copia de archivos desde '$source' hacia la raíz..." -ForegroundColor Cyan
    
    # Copiar todos los archivos y carpetas recursivamente
    # -Force sobreescribe archivos existentes
    Get-ChildItem -Path $source | ForEach-Object {
        Copy-Item -Path $_.FullName -Destination $destination -Recurse -Force -ErrorAction SilentlyContinue
    }
    
    Write-Host "`n¡Proceso completado!" -ForegroundColor Green
    Write-Host "Todos los archivos han sido copiados a la carpeta principal."
    Write-Host "Ahora puedes verificar que todo esté bien y eliminar la carpeta '$source' manualmente."
} else {
    Write-Host "Error: No se encontró la carpeta '$source' en este directorio." -ForegroundColor Red
}

Write-Host "`nPresiona cualquier tecla para cerrar esta ventana..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
