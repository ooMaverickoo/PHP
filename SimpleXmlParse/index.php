
<form action="" method="get">
    Suche:
    <label>
        <select name="object">
            <option>Bürofläche</option>
            <option>Haus</option>
            <option>Wohnung</option>
        </select>
    </label>
    <label>in
        <select name="place">
            <option>Graz</option>
            <option>Wien</option>
            <option>Salzburg</option>
            <option>Eisenstadt</option>
            <option>Klagenfurt</option>
            <option>Sankt Pölten</option>
            <option>Linz</option>
            <option>Innsbruck</option>
            <option>Bregenz</option>
        </select>

    </label>

    <label>für
        <select name="rent">
            <option>Miete</option>
            <option>Kauf</option>
            <option>Mietkauf</option>
            <option>Pacht</option>
        </select>
    </label>

    <input type="hidden" name="action" value="suchen">
    <input type="submit" value="Los">
</form><br>

<?php

require_once("GetOpenImmoXMLData.php");

//Prototype of the openimmo export interface
//get the parameters of the search field
$get_parameter = $_GET;


$xml_object = new GetOpenImmoXMLData($get_parameter);


if ($xml_object->getGetArray() == false) {
    //Outputs the number of properties
    echo "Es befinden sich " . $xml_object->getNumberOfProperties() . " Objekte auf unserer Seite" . "<br><br>";
    $xml_object->getAllProperties();
} else {
    //output the filtered properties
    $xml_object->filterProperties();
}
?>