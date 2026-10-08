Add-Type -AssemblyName System.Drawing
$srcPath = 'C:\Users\kaua.fsouza\.gemini\antigravity-ide\brain\4deb6020-e44d-429a-88ac-5375bfac20a4\.user_uploaded\media_1791466670368.png'
$bmp = [System.Drawing.Bitmap]::FromFile($srcPath)

# Card 1 bottle (Una Somos)
$crop1 = New-Object System.Drawing.Rectangle(100, 180, 145, 148)
$target1 = New-Object System.Drawing.Bitmap(145, 148)
$g1 = [System.Drawing.Graphics]::FromImage($target1)
$g1.DrawImage($bmp, 0, 0, $crop1, [System.Drawing.GraphicsUnit]::Pixel)
$target1.Save('C:\xampp\htdocs\Sillage\assets\img\samples\una_somos_real.png', [System.Drawing.Imaging.ImageFormat]::Png)
$g1.Dispose()
$target1.Dispose()

# Card 2 bottle (Fame)
$crop2 = New-Object System.Drawing.Rectangle(100, 390, 145, 148)
$target2 = New-Object System.Drawing.Bitmap(145, 148)
$g2 = [System.Drawing.Graphics]::FromImage($target2)
$g2.DrawImage($bmp, 0, 0, $crop2, [System.Drawing.GraphicsUnit]::Pixel)
$target2.Save('C:\xampp\htdocs\Sillage\assets\img\samples\fame_real.png', [System.Drawing.Imaging.ImageFormat]::Png)
$g2.Dispose()
$target2.Dispose()

# Also crop the official Sillage logo from media_1791466670310.png if needed
$srcLogoPath = 'C:\Users\kaua.fsouza\.gemini\antigravity-ide\brain\4deb6020-e44d-429a-88ac-5375bfac20a4\.user_uploaded\media_1791466670310.png'
$bmpLogo = [System.Drawing.Bitmap]::FromFile($srcLogoPath)
$cropLogo = New-Object System.Drawing.Rectangle(7, 30, 215, 120)
$targetLogo = New-Object System.Drawing.Bitmap(215, 120)
$gLogo = [System.Drawing.Graphics]::FromImage($targetLogo)
$gLogo.DrawImage($bmpLogo, 0, 0, $cropLogo, [System.Drawing.GraphicsUnit]::Pixel)
$targetLogo.Save('C:\xampp\htdocs\Sillage\assets\img\logo_original.png', [System.Drawing.Imaging.ImageFormat]::Png)
$gLogo.Dispose()
$targetLogo.Dispose()
$bmpLogo.Dispose()

$bmp.Dispose()
Write-Output "Extracted assets successfully!"
