<?php
/**
 * Created by PhpStorm.
 * User: cpetritsch
 * Date: 19.01.2018
 * Time: 15:48
 */
require_once("GetOpenImmoXMLData.php");

$file_path = __DIR__ . "\\xml\\export.xml";

//echo "Hallo " . htmlspecialchars($_GET["ai"] . " und " . htmlspecialchars($_GET["ii"]));


$xml_object = new GetOpenImmoXMLData($file_path, $_GET);

if ($xml_object->getGetArray()){

    $xml_object->getPropertyWithIndex((int)$_GET["ai"],(int)$_GET["ii"]);
    //$xml_object->getPropertyWithIndex(3, 55);

} else {

    echo "<p>" . "Fehler beim Abrufen des Objekts" . "</p>";

}
