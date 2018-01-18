<?php
/**
 * User: cpetritsch
 * Date: 18.01.2018
 * Time: 09:47
 */

/**
 * Class GetOpenImmoXMLData
 * @param array $getarray url parameter from $_GET
 */
class GetOpenImmoXMLData
{
    //Constants for attributes and elements in openimmo xml file
    Const TESTFILEPATH = __DIR__ . "\\xml\\export.xml";
    Const ANBIETER = "anbieter";
    Const ANBIETERNR = "anbieternr";
    Const OPENIMMO_ANID = "openimmo_anid";
    Const LOCATION = "location";
    Const OBJEKTKATEGORIE = "objektkategorie";
    Const FIRMA = "firma";
    Const IMMOBILIE = "immobilie";
    Const ANHAENGE = "anhaenge";
    Const ANHANG = "anhang";
    Const FREITEXTE = "freitexte";
    Const DATEN = "daten";
    Const PFAD = "pfad";
    Const GEO = "geo";
    const ORT = "ort";
    const PLZ = "plz";
    const BEZIRK = "bezirk";
    const BUNDESLAND = "bundesland";
    const LAND = "land";
    const ISO_LAND = 'iso_land';
    const OBJEKTBESCHREIBUNG = "objektbeschreibung";
    const IMAGESIZE_COVER = 500;
    const OBJEKTART = "objektart";
    const HAUSTYP = "haustyp";
    const IMAGESIZE_ATTACHMENT = "200";
    const PREISE = "preise";
    const KAUFPREIS = "kaufpreis";
    const FLAECHEN = "flaechen";
    const WOHNFLAECHE = "wohnflaeche";
    const EURO = "EUR";
    const VERMARKTUNGSART = "vermarktungsart";


    private $xmlobject;
    private $getarray;
    public $properties;

    public function __construct($getarray)
    {


        $this->setXmlObject(self::TESTFILEPATH);

        if (isset($getarray)) {
            $this->getarray = $getarray;
        } else {
            $this->getarray = false;
        }

    }

    public function getPropertiesObject()
    {
        $anbieter = self::ANBIETER;
        $immobilien = self::IMMOBILIE;

        return $this->xmlobject->$anbieter->$immobilien;
    }

    public function getGetArray()
    {
        if (!empty($this->getarray["object"]) && !empty($this->getarray["place"]) && !empty($this->getarray["rent"])) {
            return $this->getarray;
        } else {
            return false;
        }

    }


    private function setXmlObject($filepath)
    {
        //checks if file is in directory
        if (file_exists($filepath)) {
            $this->xmlobject = simplexml_load_file($filepath);
        } else {

            exit('Datei konnte nicht geöffnet werden!');
        }
    }

    public function getAllProperties()
    {
        $anbieter = self::ANBIETER;
        $immobilien = self::IMMOBILIE;
        $geo = self::GEO;
        $anhaenge = self::ANHAENGE;
        $anhang = self::ANHANG;
        $ort = self::ORT;
        $daten = self::DATEN;
        $plz = self::PLZ;
        $bezirk = self::BEZIRK;
        $bundesland = self::BUNDESLAND;
        $freitexte = self::FREITEXTE;
        $objektbeschreibung = self::OBJEKTBESCHREIBUNG;
        $objektkategorie = self::OBJEKTKATEGORIE;
        $objektart = self::OBJEKTART;
        $preise = self::PREISE;
        $kaufpreis = self::KAUFPREIS;
        $flaechen = self::FLAECHEN;
        $wohnflaeche = self::WOHNFLAECHE;


        //output loop
        foreach ($this->xmlobject->$anbieter->$immobilien as $immobilie) {

            //type of property
            echo "<h2>" . $immobilie->$objektkategorie->$objektart->haus[self::HAUSTYP] . "</h2>";

            //cover image
            echo "<img src=\"" . $immobilie->anhaenge->anhang[0]->daten->pfad . "\" WIDTH=" . self::IMAGESIZE_COVER . "><br><br>";

            //more images of the property
            foreach ($immobilie->$anhaenge->$anhang as $images) {

                //url_check if image does not exist
                if (self::checkUrl($images->daten->pfad) && $images->format == "jpg" || $images->format == "png") {
                    echo "<img src=\"" . $images->daten->pfad . "\" WIDTH=" . self::IMAGESIZE_ATTACHMENT . ">";
                }
            }

            //address
            echo "<h3>Adresse:</h3>";
            echo $immobilie->$geo->$ort . "<br>"
                . $immobilie->$geo->$plz . " "
                . $immobilie->$geo->$bezirk . "<br>"
                . $immobilie->$geo->$bundesland . "<br>"
                . $immobilie->$geo->land[self::ISO_LAND] . "<br><br>";

            //price
            echo "Kaufpreis: <br>" . self::EURO . " " . $immobilie->$preise->$kaufpreis . " ,-" . "<br><br>";
            //squaremeters
            echo "Wohnfläche: <br>" . $immobilie->$flaechen->$wohnflaeche . " m<sup>2</sup>" . "<br><br>";

            //description of the property
            echo "<h3>Beschreibung:</h3>" . str_replace("\n", "<br>", $immobilie->$freitexte->$objektbeschreibung) . "<br><br>";

        }
    }

    //Check how often the word immobilie appears in the file
    public function getNumberOfProperties()
    {
        $numbers = 0;

        foreach ($this->xmlobject->anbieter->children() as $child) {
            //
            if ($child->getName() == self::IMMOBILIE) {
                $numbers++;
            }
            //echo $child->getName();
        }
        return $numbers;
    }

    //Checks if a url exist
    function checkUrl($url)
    {
        $headers = @get_headers($url);
        //print_r($headers);
        return is_array($headers) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $headers[0]) : false;
    }


    function filterProperties()
    {
        $objektkategorie = self::OBJEKTKATEGORIE;
        $geo = self::GEO;
        $ort = self::ORT;

        $get = $this->getGetArray();
        $this->debugToConsole($get);
        $object = htmlspecialchars($get["object"]);
        $place = htmlspecialchars($get["place"]);
        $rent = htmlspecialchars($get["rent"]);

        $this->debugToConsole($object);
        $this->debugToConsole($place);
        $this->debugToConsole($rent);

        //number of the property
        $filtered_properties[$this->getNumberOfProperties()] = false;
        $i = 0;

        //checks the get values with the xml entries
        foreach ($this->getPropertiesObject() as $use_as) {

            $this->debugToConsole($use_as->$objektkategorie->nutzungsart[self::filterCheckObject($object)]);

            if ($use_as->$objektkategorie->nutzungsart[self::filterCheckObject($object)] == "1") {
                $this->debugToConsole("Erste Schleife");

                $this->debugToConsole($use_as->$geo->$ort);

                if ($use_as->$geo->$ort == self::filterCheckPlace($place)) {
                    $this->debugToConsole("Zweite Schleife");
                    $this->debugToConsole($use_as->$objektkategorie->vermarktungsart[self::filterCheckRent($rent)]);

                    if ($use_as->$objektkategorie->vermarktungsart[self::filterCheckRent($rent)] == "1") {
                        $this->debugToConsole("Dritte Schleife");
                        //save the number of the property in the filtered_array
                        $filtered_properties[$i] = true;

                        //coverimage
                            echo "<img src=\"" . $use_as->anhaenge->anhang[0]->daten->pfad . "\" WIDTH=" . self::IMAGESIZE_COVER . "><br><br>";

                        $this->debugToConsole($filtered_properties);
                    } else {
                        $filtered_properties[$i] = false;
                    }
                } else {
                    $filtered_properties[$i] = false;
                }
            } else {
                $filtered_properties[$i] = false;
            }
            $i++;
        }



        return $filtered_properties;
    }

    /**
     * @param $object string of the $_GET["object"]
     * @return string outputs the label of the object
     */
    private static function filterCheckObject($object)
    {
        switch ($object) {
            case "Bürofläche":
                return "GEWERBE";
                break;

            case "Wohnung":
            case "Haus":
                return "WOHNEN";
                break;

            default:
                return "WOHNEN";

        }
    }

    private static function filterCheckPlace($place)
    {

        switch ($place) {
            case "Graz":
                return "Graz";
                break;
            case "Wien":
                return "Wien";
                break;
            case "Salzburg":
                return "Salzburg";
                break;
            case "Klagenfurt":
                return "Klagenfurt";
                break;
            case "Sankt Pölten":
                return "Sankt Pölten";
                break;
            case "Linz":
                return "Linz";
                break;
            case "Innsbruck":
                return "Innsbruck";
                break;
            case "Bregenz":
                return "Bregenz";
                break;

            default:
                return "Graz";

        }
    }

    private static function filterCheckRent($rent)
    {
        switch ($rent) {
            case "Miete":
            case "Mietkauf":
                return "MIETE_PACHT";
                break;
            case "Kauf":
                return "KAUF";
                break;
            default:
                return "MIETE_PACHT";
        }
    }

    function debugToConsole($data) {
        $output = $data;
        if ( is_array( $output ) )
            $output = implode( ',', $output);

        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }

}