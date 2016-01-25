<?php
    header("Content-type: text/xml");
    header("Access-Control-Allow-Origin: *");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<tourml:Tour 
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:tourml="http://tapintomuseums.org/TourML"
            tourml:id="classicalcore">';
    
    // TourMetadata, static for now
    echo '<tourml:TourMetadata>';
        echo '<tourml:Title xml:lang="en">Philadelphia\'s Classical Core</tourml:Title>';
		echo '<tourml:Description xml:lang="en">Clustered within a few blocks of Independence Hall, five important buildings (and one that is no longer extant) reveal ways in which architects from the 1790s until the Civil War used Greek Revival idiom to house the institutions of a growing nation.';
		echo '</tourml:Description>';
		echo '<tourml:Author>Bryn Mawr College</tourml:Author>';
		
        echo '<tourml:AppResource tourml:id="tour-geo" tourml:usage="geo" />';
        echo '<tourml:AppResource tourml:id="tour-image" tourml:usage="image" />';     

		echo '<tourml:PropertySet>';
            echo '<tourml:Property tourml:name="initial_map_zoom">17</tourml:Property>';
			echo '<tourml:Property tourml:name="google-analytics"></tourml:Property>';
		echo '</tourml:PropertySet>';
	echo '</tourml:TourMetadata>';

    // TourAssets, static for now
    echo '<tourml:Asset tourml:id="tour-geo">';
        echo '<tourml:Content>';
            echo '<tourml:Data><![CDATA[{"type":"Point","coordinates":[-75.1452936,39.9476204]}]]></tourml:Data>';
        echo '</tourml:Content>';
    echo '</tourml:Asset>';
    echo '<tourml:Asset tourml:id="tour-image">';
		echo '<tourml:Source tourml:format="image/png" tourml:uri="/mobile/html/images/tour_intro.jpg" />';
	echo '</tourml:Asset>';

    // TourStops, loop through items returned from the database
    $url    = 'http://staging.interactivemechanics.com/tours-cms/api/stops';
    $json   = file_get_contents($url);
    $stops  = json_decode($json);

    foreach ($stops as $stop){
        $sid         = $stop->sid;
        $title       = $stop->title;
        $desc        = $stop->description;
        $long        = $stop->longitude;
        $lat         = $stop->latitude;
        $file_name   = $stop->file_name;
        $file_type   = $stop->file_type;

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