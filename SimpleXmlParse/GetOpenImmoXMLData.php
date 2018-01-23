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
    //Const TESTFILEPATH = __DIR__ . "\\xml\\export.xml";

    //Directory witch the files of the edireal zip like this:-> jjmmdd_export.zip
    const ZIP_DIR = ".\\edi_real\\";

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
    const IMAGESIZE_ATTACHMENT = 200;
    const PREISE = "preise";
    const KAUFPREIS = "kaufpreis";
    const FLAECHEN = "flaechen";
    const WOHNFLAECHE = "wohnflaeche";
    const EURO = "EUR";
    const VERMARKTUNGSART = "vermarktungsart";
    const AUSTRIA = "AUT";

    //FILE NAMES
    const FILE_NAME_HOME_SITE = "index.php";
    const FILE_NAME_DETAIL_SITE = "detail.php";

    //FORMATS
    const FORMAT_JPG = "jpg";
    const FORMAT_PNG = "png";

    //Various Strings
    const OUTPUT_SQUARE_METERS_UNIT = "m<sup>2</sup>";
    const OUTPUT_COMMA_MINUS = " ,-";
    const OUTPUT_ADDRESS = "Adresse";
    const OUTPUT_SPACE = " ";
    const OUTPUT_SELLING_PRICE = "Kaufpreis";
    const OUTPUT_LIVING_SPACE = "Wohnfläche";
    const OUTPUT_DESCRIPTION = "Beschreibung";
    const OUTPUT_COLON = ":";

    //OUTPUT Constants for HTML
    const OUTPUT_IMG_TAG_START = "<img src=\"";
    const OUTPUT_IMG_TAG_END = "/>";
    const OUTPUT_IMG_WIDTH = "\" WIDTH=";
    const OUTPUT_BR_TAG = "<br/>";
    const OUTPUT_A_TAG_START = "<a href=";
    const OUTPUT_A_TAG_END = "</a>";
    const OUTPUT_IMG_ALT = "alt=";
    const OUTPUT_P_TAG_START = "<p>";
    const OUTPUT_P_TAG_END = "</p>";
    const OUTPUT_H1_TAG_START = "<h1>";
    const OUTPUT_H1_TAG_END = "</h1>";
    const OUTPUT_H3_TAG_START = "<h3>";
    const OUTPUT_H3_TAG_END = "</h3>";


    //members
    private $xml_object; //holds the xml file as an SimpleXmlObject
    private $param_array; //holds the get param of the current site
    public $properties;
    //COMMUNITIES OF GRAZ AND GU
    private $communities = array(

        "8010" =>
        //TODO create communities

    );

    /**
     * GetOpenImmoXMLData constructor.
     * @param string $file_path
     * @param array $get_array
     */
    public function __construct(string $file_path, array $get_array)
    {
        $this->setXmlObject($file_path);

        if ($get_array) {
            $this->param_array = $get_array;
        } else {
            $this->param_array = false;
        }
    }

    /**
     * @return mixed
     */
    public function getPropertiesObject()
    {
        $anbieter = self::ANBIETER;
        $immobilien = self::IMMOBILIE;

        return $this->xml_object->$anbieter->$immobilien;
    }

    /**
     * @return mixed
     */

    public function getAgentObject()
    {
        $anbieter = self::ANBIETER;
        return $this->xml_object->$anbieter;
    }

    /**
     * @return bool
     */

    public function getGetArray()
    {
        if (empty($this->param_array)) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * @param $file_path
     */

    private function setXmlObject($file_path)
    {
        //checks if file is in directory
        if (file_exists($file_path)) {
            $this->xml_object = simplexml_load_file($file_path);
        } else {

            exit('Datei konnte nicht geöffnet werden!');
        }
    }

    /**
     * Outputs all data
     */

    public function getAllProperties()
    {
        $anbieter = self::ANBIETER;
        $immobilien = self::IMMOBILIE;
        $anhaenge = self::ANHAENGE;
        $anhang = self::ANHANG;
        $objektkategorie = self::OBJEKTKATEGORIE;
        $objektart = self::OBJEKTART;


        //indexes for links
        $immo_index = 0;
        $agent_index = 0;

        //output loop
        //children() means anbieter
        foreach ($this->xml_object->$anbieter as $makler) {
            foreach ($makler->$immobilien as $immobilie) {

                //type of property
                echo self::OUTPUT_H1_TAG_START . $immobilie->$objektkategorie->$objektart->children()[0]->attributes() . self::OUTPUT_H1_TAG_END;

                //cover image
                echo $this->getAllPropertyCoverImage($immobilie);

                //attachment images
                $this->getAllPropertyAttachmentImages($immobilie);

                //address
                echo self::OUTPUT_H3_TAG_START . self::OUTPUT_ADDRESS . self::OUTPUT_COLON . self::OUTPUT_H3_TAG_END;
                echo $this->getAllPropertyAddressFormatted($immobilie);

                //price
                echo self::OUTPUT_SELLING_PRICE . self::OUTPUT_COLON . self::OUTPUT_BR_TAG
                    . $this->getAllPropertyPrice($immobilie);
                //squaremeters
                echo self::OUTPUT_LIVING_SPACE . self::OUTPUT_COLON . self::OUTPUT_BR_TAG
                    . $this->getAllPropertySquaremeters($immobilie);

                //description of the property
                echo "<h3>" . self::OUTPUT_DESCRIPTION . self::OUTPUT_COLON . "</h3>"
                    . $this->getAllPropertyDescription($immobilie);

                //url creator
                $this->createUrl($agent_index, $immo_index);

                /*                if (__FILE__ != __DIR__ . "\\detail.php") {
                                    $this->createUrlToDetailSite($agent_index, $immo_index);
                                } else {
                                    $this->createBackUrl();
                                }*/

                $immo_index++;
            }
            $immo_index = 0;
            $agent_index++;
        }
    }


    public function getAllPropertyAttachmentImages(SimpleXMLElement $property_object)
    {
        //more images of the property
        foreach ($property_object->anhaenge->anhang as $images) {

            //url_check if image does not exist
            if (self::checkUrl($images->daten->pfad) && $images->format == self::FORMAT_JPG || $images->format == self::FORMAT_PNG) {
                echo "<img src=\"" . $images->daten->pfad . "\" WIDTH=" . self::IMAGESIZE_ATTACHMENT . ">";
            }
        }
    }

    /**
     * @param SimpleXMLElement $property_object
     * @return string
     */

    public function getAllPropertyDescription(SimpleXMLElement $property_object)
    {
        return str_replace("\n", "<br>", $property_object->freitexte->objektbeschreibung) . self::OUTPUT_BR_TAG . self::OUTPUT_BR_TAG;
    }

    /**
     * @param SimpleXMLElement $property_object
     * @return string
     */
    public function getAllPropertySquaremeters(SimpleXMLElement $property_object)
    {
        return $property_object->flaechen->wohnflaeche . self::OUTPUT_SPACE . self::OUTPUT_SQUARE_METERS_UNIT . self::OUTPUT_BR_TAG . self::OUTPUT_BR_TAG;
    }


    /**
     * @param SimpleXMLElement $property_object
     * @return string
     */
    public function getAllPropertyPrice(SimpleXMLElement $property_object)
    {
        return self::EURO . " " . $property_object->preise->kaufpreis . self::OUTPUT_SPACE . self::OUTPUT_COMMA_MINUS . self::OUTPUT_BR_TAG . self::OUTPUT_BR_TAG;
    }


    /**
     * @param SimpleXMLElement $property_object
     * @return string
     */

    public function getAllPropertyAddressFormatted(SimpleXMLElement $property_object)
    {
        return $property_object->geo->ort . self::OUTPUT_BR_TAG
            . $property_object->geo->plz . self::OUTPUT_SPACE
            . $property_object->geo->bezirk . self::OUTPUT_BR_TAG
            . $property_object->geo->bundesland . self::OUTPUT_BR_TAG
            . $property_object->geo->land[self::ISO_LAND]
            . self::OUTPUT_BR_TAG
            . self::OUTPUT_BR_TAG;
    }

    public function getAllPropertyState(SimpleXMLElement $property_object)
    {
        return $property_object->geo->bundesland;
    }

    public function getAllPropertyPlace(SimpleXMLElement $property_object)
    {

        return $property_object->geo->ort;

    }

    public function getAllPropertyPostCode(SimpleXMLElement $property_object)
    {
        return $property_object->geo->plz;
    }

    public function getAllPropertyDistrict(SimpleXMLElement $property_object)
    {
        return $property_object->geo->bezirk;
    }

    public function getAllPropertyLand(SimpleXMLElement $property_object)
    {
        return $property_object->geo->land[self::ISO_LAND];
    }


    public function getAllPropertyCoverImage(SimpleXMLElement $property_object)
    {
        return self::OUTPUT_IMG_TAG_START .
            $property_object->anhaenge->anhang[0]->daten->pfad .
            self::OUTPUT_IMG_WIDTH .
            self::IMAGESIZE_COVER .
            self::OUTPUT_IMG_TAG_END .
            self::OUTPUT_BR_TAG .
            self::OUTPUT_BR_TAG;
    }


    /**
     * @param $agent_index
     * @param $immo_index
     */

    public function getPropertyWithIndex($agent_index, $immo_index)
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
        $plz = self::PLZ;

        //type of property
        echo "<h2>" . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$objektkategorie->$objektart->children()[0]->attributes() . "</h2>";
        self::debugToConsole($this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$objektkategorie->$objektart->children()[0]->attributes());

        //cover image
        echo "<img src=\"" . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->anhaenge->anhang[0]->daten->pfad . "\" WIDTH=" . self::IMAGESIZE_COVER . "><br><br>";

        //more images of the property
        foreach ($this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$anhaenge->$anhang as $images) {

            //url_check if image does not exist
            if (self::checkUrl($images->daten->pfad) && $images->format == "jpg" || $images->format == "png") {
                echo "<img src=\"" . $images->daten->pfad . "\" WIDTH=" . self::IMAGESIZE_ATTACHMENT . ">";
            }
        }

        //address
        echo "<h3>Adresse:</h3>";
        echo $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$geo->$ort . "<br>"
            . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$geo->$plz . " "
            . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$geo->$bezirk . "<br>"
            . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$geo->$bundesland . "<br>"
            . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$geo->land[self::ISO_LAND] . "<br><br>";

        //price
        echo "Kaufpreis: <br>" . self::EURO . " " . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$preise->$kaufpreis . " ,-" . "<br><br>";
        //squaremeters
        echo "Wohnfläche: <br>" . $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$flaechen->$wohnflaeche . " m<sup>2</sup>" . "<br><br>";

        //description of the property
        echo "<h3>Beschreibung:</h3>" . str_replace("\n", "<br>", $this->xml_object->anbieter[$agent_index]->$immobilien[$immo_index]->$freitexte->$objektbeschreibung) . "<br><br>";

        $this->createUrl($agent_index, $immo_index);

    }


    /**
     * @param $agent_index
     * @param $immo_index
     */
    private function createUrl($agent_index, $immo_index)
    {

        $file_name = basename($_SERVER['REQUEST_URI']);//create a file name
        $pos_dot = strpos($file_name, '?');//get the position of the parameter
        $file_without_parameter = substr($file_name, 0, $pos_dot);//clear the url of parameter


        //Checks the file name and create the right url
        if ($file_without_parameter != self::FILE_NAME_DETAIL_SITE) {
            //go to detail site
            $this->createUrlToDetailSite($agent_index, $immo_index);
        } else {
            //back to home url
            $this->createBackUrl();
        }
    }

    private static function createBackUrl()
    {
        echo "<a href=\"" . "./" . self::FILE_NAME_HOME_SITE . "\">Zurück</a>";
    }

    /**
     * @return int
     */
//Check how often the word immobilie appears in the file
    public function getNumberOfAllProperties()
    {
        $numbers = 0;

        foreach ($this->xml_object->anbieter as $makler) {
            foreach ($makler->children() as $child) {
                //
                if ($child->getName() == self::IMMOBILIE) {
                    $numbers++;
                }
                //echo $child->getName();
            }
        }
        return $numbers;
    }

    /**
     * @return int
     */
    public function getNumberOfAllAgents()
    {
        $numbers = 0;

        foreach ($this->xml_object as $makler) {

            if ($makler->getName() == self::ANBIETER) {
                $numbers++;
            }
        }
        return $numbers;

    }

    /**
     * Checks if a url exist
     * @param $url
     * @return bool|false|int
     */

    private static function checkUrl($url)
    {
        $headers = @get_headers($url);
        //print_r($headers);
        return is_array($headers) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $headers[0]) : false;
    }

    /**
     * Outputs data with filter
     */

    public function filterProperties()
    {
        $objektkategorie = self::OBJEKTKATEGORIE;
        $geo = self::GEO;
        $ort = self::ORT;
        $plz = self::PLZ;
        $immobilien = self::IMMOBILIE;

        if ($this->getGetArray()) {

            $get = $this->param_array;
            $this->debugToConsole($get);
            $object = htmlspecialchars($get["object"]);
            $place = htmlspecialchars($get["place"]);
            $rent = htmlspecialchars($get["rent"]);

            $this->debugToConsole($object);
            $this->debugToConsole($place);
            $this->debugToConsole($rent);

            //number of the property
            $filtered_properties[$this->getNumberOfAllProperties()] = false;
            $immo_index = 0;
            $agent_index = 0;

            //checks the get values with the xml entries
            foreach ($this->getAgentObject() as $agent) {
                foreach ($agent->$immobilien as $use_as) {

                    $this->debugToConsole($use_as->$objektkategorie->nutzungsart[self::filterCheckObject($object)]);

                    if ($use_as->$objektkategorie->nutzungsart[self::filterCheckObject($object)] == "1") {
                        $this->debugToConsole("Loop 1");

                        $this->debugToConsole("Ort: " . $use_as->$geo->$ort);
                        $this::debugToConsole("PLZ: " . $use_as->$geo->$plz);

                        if (self::checkRegion($use_as->$geo->$plz, $use_as->$geo->land[self::ISO_LAND]) == $place) {
                            $this->debugToConsole("Loop 2");
                            $this->debugToConsole($use_as->$objektkategorie->vermarktungsart[self::filterCheckRent($rent)]);

                            if ($use_as->$objektkategorie->vermarktungsart[self::filterCheckRent($rent)] == "1") {
                                $this->debugToConsole("Loop 3");
                                //save the number of the property in the filtered_array
                                $filtered_properties[$immo_index] = true;

                                //output of the filtered object
                                $this->getPropertyWithIndex($agent_index, $immo_index);

                                $this->debugToConsole($filtered_properties);
                            } else {
                                $filtered_properties[$immo_index] = false;
                            }
                        } else {
                            $filtered_properties[$immo_index] = false;
                        }
                    } else {
                        $filtered_properties[$immo_index] = false;
                    }
                    $immo_index++;
                }
                $immo_index = 0;
                $agent_index++;
            }
        }
    }

    /**
     * @param $agent_index
     * @param $immo_index
     */

    private static function createUrlToDetailSite($agent_index, $immo_index)
    {

        echo "<a href=\"detail.php?ai=" . htmlspecialchars($agent_index) . "&" . "ii=" . htmlspecialchars($immo_index) . "\">Mehr</a>";

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

    /**
     * @param $rent
     * @return string
     */

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

    /**
     *
     */

    public static function unzipXML()
    {

        //saves a sorted selection of the files in the ZIP_DIR directory
        $zip_files = scandir(self::ZIP_DIR);
        $scanned_dir = array_slice($zip_files, 2);
        // Bitte .zip Datei in den gleichen Ordner wie Skript hochladen


        var_dump($scanned_dir);

        foreach ($scanned_dir as $zip_file) {


            echo fileatime($zip_file) . "<br>";

        }
    }


    /*
    $file = 'file.zip'; //Dateiname entsprechend ändern



    $path = pathinfo(realpath($file), PATHINFO_DIRNAME);

    $zip = new ZipArchive;
    $res = $zip->open($file);
    if ($res === TRUE) {
        $zip->extractTo($path);
        $zip->close();
        echo "Glückwunsch! $file wurde erfolgreich nach $path exportiert.";
    } else {
        echo "Die Datei $file konnte nicht gefunden/geöffnet werden.";
    }
*/


    /**
     * @param $data
     */

    private static function debugToConsole($data)
    {
        $output = $data;
        if (is_array($output)) {
            $output = implode(',', $output);
        }

        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }

    /**
     * @param $plz
     * @param $region
     * @return string
     */

    private static function checkRegion($plz, $region)
    {

        if ($region == self::AUSTRIA) {

            //Checks the first number in post code for defining the region
            $temp_plz = substr($plz, 0, 1);
            self::debugToConsole($temp_plz);

            switch ($temp_plz) {

                case "1":
                    return "Wien";
                    break;
                case "2":
                    return "Niederösterreich";
                    break;
                case "3":
                    return "Sankt Pölten";
                    break;
                case "4":
                    return "Linz";
                    break;
                case "5":
                    return "Salzburg";
                    break;
                case "6":
                    return "Bregenz";
                    break;
                case "7":
                    return "Burgenland";
                    break;
                case "8":
                    return self::checkCommunities($plz);
                    break;
                case "9":
                    return "Klagenfurt";
                    break;

                default:
                    return "";
            }
        } else {
            return "Ausland";
        }
    }


    private static function checkCommunities($postcode)
    {

        for ($i = 0;)


            return "Graz";

    }
}