<?

// should contain your db credentials
include("settings.php");

$run = false;

$sql = "CREATE TABLE IF NOT EXISTS `asap_years` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `street_uri` varchar(255) NOT NULL,
          `earliest_bag_building_year` int(11) NOT NULL,
          `raadsbesluit_year` int(11) NOT NULL,
          `uitleg_year` int(11) NOT NULL,
          `first_map_year` int(11) NOT NULL,
          `first_gone_from_map_year` int(11) NOT NULL,
          `first_transportakte_year` int(11) NOT NULL,
          `last_transportakte_year` int(11) NOT NULL,
          PRIMARY KEY (id),
          INDEX `street_uri` (`street_uri`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$result = $mysqli->query($sql);


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

function streetUriPresent($uri){
    global $mysqli;

    $result = $mysqli->query("select * from asap_years where street_uri = '" . $uri . "'");
    if($result->num_rows){
        return true;
    }

    $result = $mysqli->query("insert into asap_years (street_uri) values ('" . $uri . "')");
    return true;
}

die;
while($row = $result->fetch_assoc()){
    
    $s2 = "select * from adamnet_hulptabellen.nwbwegvakken 
            where naam = '" . $mysqli->real_escape_string($row['preflabel']) . "'";
        
        
    $r2 = $mysqli->query($s2);

    if($r2->num_rows > 0){

        $line = array();
        if($r2->num_rows ==1){
            $row2 = $r2->fetch_assoc();
            $line = json_decode($row2['geom'],true);
        }else{
            $line['type'] = "MultiLineString";
            $line['coordinates'] = array();
            while($row2 = $r2->fetch_assoc()){
                $geom = json_decode($row2['geom'],true);
                $line['coordinates'][] = $geom['coordinates'];
            }
        }
        
        
        echo $c($row['sources'] . " " . $row['preflabel'] . " found")->green() . "\n";
        $upd = "insert into streetgeometries (street_identifier, geojson, sources) values (
                " . $row['id'] . ",
                '" . json_encode($line) . "',
                'nwb-25-9-18'
                )";

        if($run){
            if (!$mysqli->query($upd)) {
                printf("Error: %s\n", $mysqli->error);
            }
            echo " +";
        }else{
            echo $upd . "\n";
        }
    }else{
        echo $c($row['sources'] . " " . $row['preflabel'] . " not found")->red() . "\n";
        //echo $c(" -")->red();
    }

    


}


?>