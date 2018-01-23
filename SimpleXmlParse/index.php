<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Immobilien</title>
</head>

<body>

<?php
//$parameter for searching
$parameter = array(
    "OBJECT" => "object",
    "PLACE" => "place",
    "RENT" => "rent");

//WHAT
$search_for = array(
    "Büro",
    "Handel",
    "Industrie",
    "Grundstück",
    "Anlage");

//WHERE
$where = array(
    "Großraum Graz",
    "Steiermark",
    "Kärnten",
    "Wien",
    "Niederösterreich",
    "Oberösterreich",
    "Salzburg",
    "Burgenland",
    "Tirol",
    "Vorarlberg",
    "Ausland");

//FOR
$for = array(
    "Miete",
    "Kauf");


function createHtmlOptions(string $param, array $option)
{
    $array_count = sizeof($option);
    echo "<select name=\"" . $param . "\">", PHP_EOL;
    for ($i = 0; $i < $array_count; $i++) {
        echo "<option";
        if (isset($_GET[$param]) && $_GET[$param] == $option[$i]) {
            echo " selected>";
        } else {
            echo ">";
        }
        echo $option[$i] . "</option>", PHP_EOL;
    }
    echo "</select>", PHP_EOL;
}

?>

<form action="" method="get">
    <label>Suche
        <?php
        createHtmlOptions($parameter["OBJECT"], $search_for)
        ?>
    </label>

    <label>in
        <?php
        createHtmlOptions($parameter["PLACE"], $where);
        ?>
    </label>

    <label>für
        <?php
        createHtmlOptions($parameter["RENT"], $for);
        ?>
    </label>

    <input type="hidden" name="action" value="suchen">
    <input type="submit" value="Los">
</form>
<br>

<?php

$file_path = __DIR__ . "\\xml\\export.xml";
require_once("GetOpenImmoXMLData.php");

//Prototype of the openimmo export interface
//get the parameters of the search field

//GetOpenImmoXMLData::unzipXML();

$xml_object = new GetOpenImmoXMLData($file_path, $_GET);


if ($xml_object->getGetArray() == false) {
    //Outputs the number of properties
    echo "Es befinden sich " . $xml_object->getNumberOfAllProperties() . " Objekte von " . $xml_object->getNumberOfAllAgents() . " Maklern" . " auf unserer Seite" . "<br><br>";
    $xml_object->getAllProperties();
} else {
    //output the filtered properties
    $xml_object->filterProperties();
}

?>

</body>
</html>
