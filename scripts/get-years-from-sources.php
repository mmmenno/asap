<?

// should contain your db credentials
include("settings.php");

$sql = "CREATE TABLE IF NOT EXISTS `asap_years` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `street_uri` varchar(255) NOT NULL,
          `earliest_bag_building_year` int(11) NOT NULL,
          `raadsbesluit_year` int(11) NOT NULL,
          `uitleg_year` int(11) NOT NULL,
          `not_yet_on_map_year` int(11) NOT NULL,
          `first_map_year` int(11) NOT NULL,
          `last_map_year` int(11) NOT NULL,
          `first_gone_from_map_year` int(11) NOT NULL,
          `first_transportakte_year` int(11) NOT NULL,
          `last_transportakte_year` int(11) NOT NULL,
          PRIMARY KEY (id),
          INDEX `street_uri` (`street_uri`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$result = $mysqli->query($sql);




// MAP PRESENCE YEARS

echo "\n\ninsert map presence years";
$i=0;
$cols = array();
if (($handle = fopen("../sources/present-on-maps.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $i++;
        if($i==1){
            foreach ($data as $k => $v) {
                preg_match("/[0-9]{4}/",$v,$found);
                if($found){
                    $cols[$k] = $found[0];
                }
            }
        }

        $yearspresent = array();
        $yearsabsent = array();
        foreach ($data as $k => $v) {
            if(array_key_exists($k, $cols)){
                if($v=="yes"){
                    $yearspresent[] = $cols[$k];
                }
                if($v=="no"){
                    $yearsabsent[] = $cols[$k];
                }
            }
        }

        if(count($yearspresent)){
            $first_map_year = min($yearspresent);
            $last_map_year = max($yearspresent);
          
            if($check = streetUriPresent($data[0])){
                $sql = "update asap_years 
                        set first_map_year = " . $first_map_year . ",
                        last_map_year = " . $last_map_year . " 
                        where street_uri = '" . $data[0] . "'";
                $result = $mysqli->query($sql);
                echo " .";
                //echo $sql . "\n";
            }
        }



        if(count($yearsabsent) && count($yearspresent)){
            
            asort($yearsabsent,SORT_NUMERIC);
            $not_yet_on_map_year = 0;
            foreach ($yearsabsent as $k => $v) {
                if($v < $first_map_year){
                    $not_yet_on_map_year = $v;
                }
            }
            
            arsort($yearsabsent,SORT_NUMERIC);
            $first_gone_from_map_year = 0;
            foreach ($yearsabsent as $k => $v) {
                if($v > $last_map_year){
                    $first_gone_from_map_year = $v;
                }
            }
          
            if($check = streetUriPresent($data[0])){
                $sql = "update asap_years 
                        set not_yet_on_map_year = " . $not_yet_on_map_year . ",
                        first_gone_from_map_year = " . $first_gone_from_map_year . "
                        where street_uri = '" . $data[0] . "'";
                $result = $mysqli->query($sql);
                echo " .";
                //echo $sql . "\n";
            }
        }
    }
    fclose($handle);
}


// UITLEGGEN

$json = file_get_contents("../sources/stadsuitbreidingen.geojson");
$data = json_decode($json,true);

$uitlegCompletionYears = array();
foreach ($data['features'] as $feature) {
    $uitlegCompletionYears[$feature['properties']['id']] = $feature['properties']['gereed_in'];
}

echo "\n\ninsert uitleg completion years";
if (($handle = fopen("../sources/straten-stadsuitbreidingen.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($check = streetUriPresent($data[0])){
            $result = $mysqli->query(   "update asap_years 
                                        set uitleg_year = " . $uitlegCompletionYears[$data[1]] . " 
                                        where street_uri = '" . $data[0] . "'");
            echo " .";
        }
    }
    fclose($handle);
}


// RAADSBESLUITEN

echo "\n\ninsert raadsbesluit years";
if (($handle = fopen("../sources/raadsbesluiten.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($check = streetUriPresent($data[0])){
            $result = $mysqli->query(   "update asap_years 
                                        set raadsbesluit_year = " . $data[3] . " 
                                        where street_uri = '" . $data[0] . "'");
            echo " .";
        }
    }
    fclose($handle);
}


// BAG BUILDING YEARS

echo "\n\ninsert BAG buildingyears";
if (($handle = fopen("../sources/BAG-oldest-building-of-street.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($check = streetUriPresent($data[0])){
            $result = $mysqli->query(   "update asap_years 
                                        set earliest_bag_building_year = " . $data[3] . " 
                                        where street_uri = '" . $data[0] . "'");
            echo " .";
        }
    }
    fclose($handle);
}





function streetUriPresent($uri){
    global $mysqli;

    $result = $mysqli->query("select * from asap_years where street_uri = '" . $uri . "'");
    if($result->num_rows){
        return true;
    }

    $result = $mysqli->query("insert into asap_years (street_uri) values ('" . $uri . "')");
    return true;
}




?>