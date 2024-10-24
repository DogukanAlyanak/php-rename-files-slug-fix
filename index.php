<?php

function copyFile($oldName, $newName)
{
    if (file_exists($oldName)) {
        if (copy($oldName, $newName)) {
            return "Dosya başarıyla kopyalandı!" . "<br>";
        } else {
            return "Dosya kopyalanamadı: " . error_get_last()['message'] . "<br>";
        }
    } else {
        return "Dosya bulunamadı: $oldName<br>";
    }
}

function getAllFiles($dir)
{
    $result = [];
    if (!is_dir($dir)) {
        die("Dizin bulunamadı: $dir" . "<br>");
    }
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            $result = array_merge($result, getAllFiles($filePath));
        } else {
            $result[] = $filePath;
        }
    }
    return $result;
}

function slugify($text)
{
    $turkish = ['ş', 'Ş', 'ı', 'İ', 'ç', 'Ç', 'ü', 'Ü', 'ö', 'Ö', 'ğ', 'Ğ'];
    $english = ['s', 'S', 'i', 'I', 'c', 'C', 'u', 'U', 'o', 'O', 'g', 'G'];
    $text = str_replace($turkish, $english, $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9.]+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $arr = ["(1)", "(2)", "(3)", "(4)", "(5)", "(6)", "(7)", "(8)", "(9)", "(10)"];
    $text = str_replace($arr, "", $text);
    $text = trim($text, '-');
    return $text;
}

$indexDizini = __DIR__ . DIRECTORY_SEPARATOR . 'files'; // 'files' dizini altında dosyaları arayın
$dosyaYollari = getAllFiles($indexDizini);

$paths = [];
$newPaths = [];
foreach ($dosyaYollari as $dosyaYolu) {
    $fileArr = explode(DIRECTORY_SEPARATOR, $dosyaYolu);
    $fileArrSlug = array_map('slugify', $fileArr);

    $fileOrj = implode('/', $fileArr);
    $fileNew = implode('/', $fileArrSlug);

    $paths[] = $fileOrj;
    $newPaths[] = $fileNew;
}

function createFoldersIfNotExist($filePath)
{
    $directoryPath = dirname($filePath);
    if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0777, true);
    }
}

$content = "";
foreach ($paths as $i => $e) {
    // $e = str_replace("files/", "", $e); // 'files' dizinini kaldır
    $old = explode("test.local/", $e)[1];
    $new = explode("test.local/", $newPaths[$i])[1];
    $new = "new_files/" . substr($new, 6);

    createFoldersIfNotExist($new);
    $copyResult = copyFile($e, $new);

    if (strpos($copyResult, 'başarıyla kopyalandı') !== false) {
        $x = pathinfo($new, PATHINFO_FILENAME);
        $h = str_replace("\\", "/", $new);
        $content .= "<br><br><a target=\"_blank\" href=\"https://admin.antalya.edu.tr/$h\">$x</a>\n";
    } else {
        echo $copyResult; // Hata mesajını ekrana yazdır
    }
}

function yazdirDosyaya($dosyaAdi, $veri)
{
    $dosya = fopen($dosyaAdi, 'w');
    if ($dosya) {
        fwrite($dosya, $veri . PHP_EOL);
        fclose($dosya);
        return true;
    } else {
        return false;
    }
}

$bt5Head = "<!doctype html><html lang=\"tr\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><title>Title</title><link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\"></head><body><div class=\"container-fluid\"><div class=\"row\"><div class=\"col-12\">\n\n\n";
$bt5Footer = "\n\n\n</div></div></div></body></html>";
$content = $bt5Head . $content . $bt5Footer;

if (yazdirDosyaya("index.html", $content)) {
    echo "ok";
} else {
    echo "Dosya açılamadı.";
}
