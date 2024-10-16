<?php





// Kullanım
// $eskiDosyaAdi = 'eski_dosya.txt';
// $yeniDosyaAdi = 'yeni_dosya.txt';
// 
function copyFile($oldName, $newName)
{
    // Dosyanın var olup olmadığını kontrol et
    if (file_exists($oldName)) {
        // Dosya kopyalama işlemi
        if (copy($oldName, $newName)) {
            return "Dosya başarıyla kopyalandı!";
        } else {
            return "Dosya kopyalanamadı!";
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


    // files name number fix
    $arr = [];
    for ($x = 0; $x < 150; $x++) {
        $arr[] = "($x)";
    }
    $e = trim(str_replace($arr, "", substr($e, 1)));
    $paths[$i] = $e;
}

foreach ($newPaths as $i => $e) {

    // klasörlerden nokta sil
    $dotAfter = explode('.', $e);
    $dotAfter = end($dotAfter);
    $dotbefore = substr($e, 0, (strlen($dotAfter) + 1) * -1);
    $dotbefore = str_replace('.', '', $dotbefore);

    $e = $dotbefore . "." . $dotAfter;
    $e = substr($e, 1);


    $newPaths[$i] = $e;
}


$content = "";
foreach ($paths as $i => $e) {
    if (substr($e, 0, strlen("files")) == "files") {
        createFoldersIfNotExist("new/" . $newPaths[$i]);
        copyFile($e, "new/" . $newPaths[$i]);

        $x = explode("/", $e);
        $x = end($x);
        $x = trim(explode(".", $x)[0]);
        $h = str_replace("\\", "/", $newPaths[$i]);
        $content .= "<br><br><a target=\"_blank\" href=\"https://admin.antalya.edu.tr/$h\">$x</a>\n";
    }
}

function yazdirDosyaya($dosyaAdi, $veri)
{
    // Dosyayı yazma modunda açar (eğer yoksa oluşturur)
    $dosya = fopen($dosyaAdi, 'w'); // 'a' modu, dosyanın sonuna ekler

    if ($dosya) {
        // Veriyi dosyaya yazar
        fwrite($dosya, $veri . PHP_EOL);
        // Dosyayı kapatır
        fclose($dosya);
        return true; // Başarıyla yazıldıysa true döner
    } else {
        return false; // Dosya açılamazsa false döner
    }
}

$bt5Head = "<!doctype html><html lang=\"en\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><title>Title</title><link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\"integrity=\"sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN\" crossorigin=\"anonymous\"><script src=\"https://code.jquery.com/jquery-3.7.1.min.js\"integrity=\"sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=\" crossorigin=\"anonymous\"></script></head><body><div class=\"container-fluid\"><div class=\"row\"><div class=\"col-12\">\n\n\n";
$bt5Footer = "\n\n\n</div></div></div><script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js\"integrity=\"sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL\"crossorigin=\"anonymous\"></script><script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js\"integrity=\"sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r\"crossorigin=\"anonymous\"></script><script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js\"integrity=\"sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+\"crossorigin=\"anonymous\"></script></body></html>";
$content = $bt5Head . $content . $bt5Footer;

if (yazdirDosyaya("index.html", $content)) {
    echo "ok";
} else {
    echo "Dosya açılamadı.";
}
