<?php

$datei = fopen(__DIR__ . '/fotos.csv', 'r');
$fotos = [];

while ($zeile = fgetcsv($datei)) {
    $fotos[] = [
        'dateiname' => $zeile[0],
        'tags' => utf8_encode($zeile[1]),
    ];
}

fclose($datei);

function filter($liste, $suchbegriff)
{
    $ergebnis = [];
    foreach ($liste as $listenEintrag) {
        if (false !== stripos($listenEintrag['tags'], $suchbegriff)) {
            $ergebnis[] = $listenEintrag;
        }
    }

    return $ergebnis;
}

if (count($_POST) > 0) {
    $suchBegriff = $_POST['suchbegriff'];
    $fotosGefiltert = filter($fotos, $suchBegriff);
} else {
    $suchBegriff = '';
    $fotosGefiltert = $fotos;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fotogalerie</title>
    <style>
        .miniatur {
            float: left;
        }

        .miniatur img {
            margin-right: 10px;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>
<h1>Fotogalerie</h1>
<p><?= count($fotosGefiltert); ?> von <?= count($fotos); ?> Fotos</p>
<?php foreach ($fotosGefiltert as $foto) : ?>
<div>
    <div class="miniatur" style="float: left;">
        <a href="foto.php?dateiname=<?= htmlspecialchars($foto['dateiname']); ?>">
            <img src="miniaturen/<?= htmlspecialchars($foto['dateiname']); ?>" alt="<?= htmlspecialchars($foto['dateiname']); ?>">
        </a>
    </div>
    Dateiname: <?= htmlspecialchars($foto['dateiname']); ?><br>
    Tags: <?= htmlspecialchars($foto['tags']); ?>
    <div class="clearfix"></div>
</div>
<?php endforeach; ?>
<div>
    <form action="index.php" method="post">
        <label for="suchbegriff">Suchbegriff:</label>
        <input type="text" id="suchbegriff" name="suchbegriff" value="<?= htmlspecialchars($suchBegriff); ?>"/>
        <button type="submit">Anwenden</button>
    </form>
</div>
</body>
</html>
