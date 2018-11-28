<?

// should contain your db credentials
include("settings.php");

$run = true;

$sql = "SELECT * FROM `asap_years`";

$result = $mysqli->query($sql);

while($row = $result->fetch_assoc()){

    $smi = $sma = $umi = $uma = 0;
    $ss = $us = "";

    //echo $row['street_uri'] . "\n";
    $slash = strrpos($row['street_uri'],"/");
    $id = substr($row['street_uri'],$slash+1);


    // mostly outside the centre, between 1864-2006
    if($row['raadsbesluit_year']>0){
        $smi = $sma = $row['raadsbesluit_year'];
        $ss = "raadsbesluit";
    }


    // streets within an 'uitleg' that show on later maps
    if($smi == 0 && $row['uitleg_year']>0 && $row['uitleg_year']<$row['first_map_year']){
        $smi = $row['uitleg_year'];
        $sma = $row['first_map_year'];
        $ss = "op basis van stadsuitleg en eerste voorkomen op kaart " . $row['first_map_year'];
    }


    // streets within an 'uitleg' that do not show on later maps
    if($smi == 0 && $row['uitleg_year']>0 && $row['first_map_year'] == 0){
        $smi = $sma = $row['uitleg_year'];
        $ss = "op basis van stadsuitleg";
    }

    // on first map year only
    if($smi == 0 && $row['first_map_year'] > 0){
        $smi = $sma = $row['first_map_year'];
        $ss = "op basis van eerste voorkomen op kaart";
    }

    // still nothing? use BAG bouwjaar
    if($smi == 0 && $row['earliest_bag_building_year'] > 0){
        $smi = $sma = $row['earliest_bag_building_year'];
        $ss = "op basis oudste pand volgens BAG";
    }



    $s = "update adamnet_straten.streets set since_min = " . $smi . ", since_max = " . $sma . ", 
            since_source = '" . $ss . "'
            where id = " . $id . " and (since_min = 0 or since_min = " . $smi . ") 
            and  (since_max = 0 or since_max = " . $sma . ")";
    
    if($run){
        $do = $mysqli->query($s);
        echo " .";
    }else{
        echo $s . ";\n\n";
    }
    

    $s = "update adamnet_straten.streets set until_min = " . $umi . ", until_max = " . $uma . "
            until_source = '" . $us . "'
            where id = " . $id . " and (until_min = 0 or until_min = " . $umi . ") 
            and  (until_max = 0 or until_max = " . $uma . ")";
    //echo $s . ";\n";

}





?>