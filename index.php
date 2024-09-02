<?php





// Kullanım
// $eskiDosyaAdi = 'eski_dosya.txt';
// $yeniDosyaAdi = 'yeni_dosya.txt';
// 
// echo changeFileName($eskiDosyaAdi, $yeniDosyaAdi);
function changeFileName($oldName, $newName)
{
    // Dosyanın var olup olmadığını kontrol et
    if (file_exists($oldName)) {
        // Dosya adını değiştirme işlemi
        if (rename($oldName, $newName)) {
            return "Dosya adı başarıyla değiştirildi!";
        } else {
            return "Dosya adı değiştirilemedi!";
        }
    } else {
        return "Dosya bulunamadı!";
    }
}



function getAllFiles($dir)
{
    $result = [];

    if (!is_dir($dir)) {
        die("Dizin bulunamadı: $dir");
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

    // Türkçe karakterleri İngilizce eşdeğerlerine dönüştür
    $turkish = ['ş', 'Ş', 'ı', 'İ', 'ç', 'Ç', 'ü', 'Ü', 'ö', 'Ö', 'ğ', 'Ğ'];
    $english = ['s', 'S', 'i', 'I', 'c', 'C', 'u', 'U', 'o', 'O', 'g', 'G'];
    $text = str_replace($turkish, $english, $text);

    // Metni küçük harflere dönüştür
    $text = strtolower($text);

    // Noktalar dışındaki özel karakterleri kaldır (noktalar korunur)
    $text = preg_replace('/[^a-z0-9.]+/', '-', $text);

    // Birden fazla tireyi tek bir tireye indir
    $text = preg_replace('/-+/', '-', $text);

    // files name fix
    $arr = ["(1)", "(2)", "(3)", "(4)", "(5)", "(6)", "(7)", "(8)", "(9)", "(10)"];
    $text = str_replace($arr, "", $text);

    // Başı ve sonundaki tireleri kaldır
    $text = trim($text, '-');

    return $text;
}


// Kullanım
$indexDizini = __DIR__ . DIRECTORY_SEPARATOR . '/';
$dosyaYollari = getAllFiles($indexDizini);


// Tüm dosya yollarını yazdır
$paths  = [];
$newPaths  = [];
foreach ($dosyaYollari as $dosyaYolu) {

    $fileArr = explode("/", $dosyaYolu)[1];
    $fileArr = explode("\\", $fileArr);

    $fileArrSlug = [];
    foreach ($fileArr as $i => $e) {
        $fileArrSlug[$i] = slugify($e);
    }

    $fileOrj = "";
    foreach ($fileArr as $i => $e) {
        if (!empty($e)) {
            $fileOrj .= "/" . $e;
        }
    }

    $fileNew = "";
    foreach ($fileArrSlug as $i => $e) {
        if (!empty($e)) {
            $fileNew .= "/" . $e;
        }
    }

    $paths[] = $fileOrj;
    $newPaths[] = $fileNew;
}

function createFoldersIfNotExist($filePath)
{
    // Dosya yolundan dizin (klasör) yolunu al
    $directoryPath = dirname($filePath);

    // Dizin var mı kontrol et, yoksa oluştur
    if (!file_exists($directoryPath)) {
        // Dizin yoksa, recursive olarak tüm ara dizinleri de oluşturarak dizini oluştur
        mkdir($directoryPath, 0777, true);
    }
}

function dd($e)
{
    header('Content-Type: application/json');
    echo json_encode($e, JSON_PRETTY_PRINT);
    exit;
}




foreach ($paths as $i => $e) {

    // files name fix
    $arr = ["(1)", "(2)", "(3)", "(4)", "(5)", "(6)", "(7)", "(8)", "(9)", "(10)"];
    $e = trim(str_replace($arr, "", substr($e, 1)));

    $paths[$i] = $e;
}
foreach ($newPaths as $i => $e) {
    $newPaths[$i] = substr($e, 1);
}


foreach ($paths as $i => $e) {
    if ($e != "index.php") {
        createFoldersIfNotExist("new/" . $newPaths[$i]);
        changeFileName($e, "new/" . $newPaths[$i]);
    }
}

$content = "";
foreach ($paths as $i => $e) {
    $x = explode("/", $e);
    $x = end($x);
    $x = trim(explode(".", $x)[0]);
    $h = str_replace("\\", "/", $newPaths[$i]);
    $content .= "<a href=\"https://admin.antalya.edu.tr/files/ikk-files/$h\">$x</a>\n";
}

echo $content;
