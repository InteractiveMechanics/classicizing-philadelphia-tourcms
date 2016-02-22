<?php
    header("Content-type: text/xml");
    header("Access-Control-Allow-Origin: *");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    
    $turl   = 'http://staging.interactivemechanics.com/tours-cms/api/tours';
    $tjson  = file_get_contents($turl);
    $tours  = json_decode($tjson);

    if ($tours[1]){

    echo '<tourml:TourSet xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	    xmlns:tourml="http://tapintomuseums.org/TourML"
        xsi:schemaLocation="http://tapintomuseums.org/TourML TourML.xsd">';

    }

    foreach ($tours as $tour){
        $tid    = $tour->tid;
        $ttit   = $tour->title;
        $tdesc  = $tour->description;
        $tlat   = $tour->latitude;
        $tlong  = $tour->longitude;
        $zoom   = $tour->zoom;
        $tfile  = $tour->file_name;
        $tstops = $tour->stops;

        echo '<tourml:Tour 
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:tourml="http://tapintomuseums.org/TourML"
                tourml:id="tour_' . $tid . '">';
        
        // TourMetadata, static for now
        echo '<tourml:TourMetadata>';
            echo '<tourml:Title xml:lang="en">' . $ttit . '</tourml:Title>';
    		echo '<tourml:Description xml:lang="en">' . $tdesc;
    		echo '</tourml:Description>';
    		echo '<tourml:Author>Bryn Mawr College</tourml:Author>';
    		
            echo '<tourml:AppResource tourml:id="tour-geo" tourml:usage="geo" />';
            echo '<tourml:AppResource tourml:id="tour-image" tourml:usage="image" />';     
    
    		echo '<tourml:PropertySet>';
                echo '<tourml:Property tourml:name="initial_map_zoom">' . $zoom . '</tourml:Property>';
    			echo '<tourml:Property tourml:name="google-analytics"></tourml:Property>';
    		echo '</tourml:PropertySet>';
    	echo '</tourml:TourMetadata>';
    
        // TourAssets, static for now
        echo '<tourml:Asset tourml:id="tour-geo">';
            echo '<tourml:Content>';
                echo '<tourml:Data><![CDATA[{"type":"Point","coordinates":[' . $tlong . ',' . $tlat . ']}]]></tourml:Data>';
            echo '</tourml:Content>';
        echo '</tourml:Asset>';
        echo '<tourml:Asset tourml:id="tour-image">';
    		echo '<tourml:Source tourml:format="image/png" tourml:uri="http://staging.interactivemechanics.com/tours-cms/files/' . $tfile . '" />';
    	echo '</tourml:Asset>';
    

        // TourStops, loop through items returned from the database
        $tstops = substr($tstops, 1, -1);
        $tstops = explode(",", $tstops);
        foreach ($tstops as $s){
            $set    = substr($s, 1, -1);
            $url    = 'http://staging.interactivemechanics.com/tours-cms/api/stops/' . $set;
            $json   = file_get_contents($url);
            $stop   = json_decode($json);

            $sid         = $stop[0]->sid;
            $title       = $stop[0]->title;
            $desc        = $stop[0]->description;
            $long        = $stop[0]->longitude;
            $lat         = $stop[0]->latitude;
            $file_name   = $stop[0]->file_name;
            $file_type   = $stop[0]->file_type;
    
            $curl       = 'http://staging.interactivemechanics.com/tours-cms/api/stops/' . $sid . '/content';
            $cjson      = file_get_contents($curl);
            $content    = json_decode($cjson);
    
            // Build stop with metadata
            echo '<tourml:Stop tourml:id="stop_' . $sid . '" tourml:view="web_stop">';
    		    echo '<tourml:Title xml:lang="en">' . $title . '</tourml:Title>';
                echo '<tourml:Description xml:lang="en">' . $desc . '</tourml:Description>';
                echo '<tourml:AssetRef tourml:id="stop_' . $sid . '-image" tourml:usage="image" />';
                echo '<tourml:AssetRef tourml:id="stop_' . $sid . '-html" tourml:usage="web_content" />';
                
                if ($long && $lat){
                    echo '<tourml:AssetRef tourml:id="stop_' . $sid . '-geo" tourml:usage="geo" />';
                }
        		echo '<tourml:PropertySet><tourml:Property tourml:name="code">' . $sid . '</tourml:Property></tourml:PropertySet>';
            echo '</tourml:Stop>';
    
            // Build stop assets
            // -- Image
            echo '<tourml:Asset tourml:id="stop_' . $sid . '-image">';
        		echo '<tourml:Source tourml:format="' . $file_type . '" tourml:uri="http://staging.interactivemechanics.com/tours-cms/files/' . $file_name . '" />';
        	echo '</tourml:Asset>';
    
            // -- HTML
            echo '<tourml:Asset tourml:id="stop_' . $sid . '-html">';
                echo '<tourml:Content>';
                    echo '<tourml:Data><![CDATA[';
                        echo '<div data-role="tabs" id="tabs">';
                            echo '<div data-role="navbar">';
                                echo '<ul>';
                                echo '<li><a href="#overview" data-ajax="false" data-src="http://staging.interactivemechanics.com/tours-cms/files/' . $content[0]->file_name . '" class="ui-btn-active">Overview</a></li>';
                                echo '<li><a href="#building" data-ajax="false" data-src="http://staging.interactivemechanics.com/tours-cms/files/' . $content[1]->file_name . '">Building</a></li>';
                                echo '<li><a href="#models" data-ajax="false" data-src="http://staging.interactivemechanics.com/tours-cms/files/' . $content[2]->file_name . '">Models</a></li>';
                                echo '<li><a href="#architect" data-ajax="false" data-src="http://staging.interactivemechanics.com/tours-cms/files/' . $content[3]->file_name . '">Architect</a></li>';
                                echo '</ul>';
                            echo '</div>';
    
                            foreach ($content as $c){
                                echo '<div id="' . $c->type . '">';
                                echo $c->body;
                                echo '</div>';
                            }
                            
                        echo '</div>';
                    echo ']]></tourml:Data>';
                echo '</tourml:Content>';
        	echo '</tourml:Asset>';
    
            // -- Geolocation
            if ($long && $lat){
                echo '<tourml:Asset tourml:id="stop_' . $sid . '-geo">';
                    echo '<tourml:Content>';
                        echo '<tourml:Data><![CDATA[{"type":"Point","coordinates":[' . $long . ',' . $lat . ']}]]></tourml:Data>';
                    echo '</tourml:Content>';
                echo '</tourml:Asset>';
            }
    
        }
    
        echo '</tourml:Tour>';
    }

    if ($tours[1]) {
        echo '</tourml:TourSet>';
    }
