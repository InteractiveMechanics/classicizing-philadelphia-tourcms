<?php
    date_default_timezone_set('America/New_York');
    require 'db.php';
    require '../vendor/autoload.php';

    $app = new \Slim\Slim();
    $app->config('debug', true);

    $app->get('/tours', getTours);    
    $app->get('/tours/:tid', getTourByID);
    $app->get('/tours/:tid/stops', getTourStops);
    $app->get('/stops', getStops);
    $app->get('/stops/:sid', getStop);
    $app->get('/stops/:sid/content', getStopContent);
    $app->get('/files/:fid', getFileById);

    $app->post('/stops', postStop);
    $app->post('/stops/:sid/content', postStopContent);
    $app->post('/files', postFile);

    $app->put('/stops/:sid', updateStop);
    $app->put('/stops/:sid/content/:cid', updateStopContent);

    $app->delete('/stops/:sid', deleteStop);

    $app->run();

    function getTours() {
        $sql = '
            SELECT
                tid,
                title,
                description,
                status,
                last_updated
            FROM 
                tour 
            ORDER BY tid DESC';
        try {
            $db     = getDB();
            $query  = $db->query($sql);
            $tours  = $query->fetchAll(PDO::FETCH_OBJ);
            $db     = null;

            echo json_encode($tours);
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }
    function getTourByID($tid) {
        $sql = '
            SELECT
                tid,
                title,
                description,
                status,
                last_updated
            FROM 
                tour 
            WHERE 
                tour.tid = ' . $tid;
        try {
            $db     = getDB();
            $query  = $db->query($sql);
            $tour   = $query->fetchAll(PDO::FETCH_OBJ);
            $db     = null;

            echo json_encode($tour);
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }
    function getTourStops($tid) {
        $sql = '
            SELECT
                tid,
                title,
                description,
                status,
                last_updated
            FROM 
                stop 
            NATURAL JOIN 
                tour_stop 
            WHERE 
                tour_stop.tid = ' . $tid;
        try {
            $db     = getDB();
            $query  = $db->query($sql);
            $stops  = $query->fetchAll(PDO::FETCH_OBJ);
            $db     = null;

            echo json_encode($stops);
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }
    function getStops() {
        $sql = '
            SELECT
                sid,
                title,
                description,
                status,
                last_updated,
                latitude,
                longitude,
                stop.fid,
                file.file_name,
                file.file_type
            FROM 
                stop 
            LEFT JOIN
                file
            ON
                stop.fid = file.fid
            ORDER BY sid DESC';
        try {
            $db     = getDB();
            $query  = $db->query($sql);
            $stops  = $query->fetchAll(PDO::FETCH_OBJ);
            $db     = null;

            echo json_encode($stops);
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }
    function getStop($sid) {
        $sql = '
            SELECT
                sid,
                title,
                description,
                status,
                last_updated,
                latitude,
                longitude,
                stop.fid,
                file.file_name,
                file.file_type
            FROM 
                stop 
            LEFT JOIN
                file
            ON
                stop.fid = file.fid
            WHERE sid = ' . $sid;
        try {
            $db     = getDB();
            $query  = $db->query($sql);
            $stop   = $query->fetchAll(PDO::FETCH_OBJ);
            $db     = null;

            echo json_encode($stop);
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }
    function getStopContent($sid) {
        $sql = '
            SELECT
                cid,
                sid,
                type,
                body,
                content.fid,
                file.file_name,
                file.file_type
            FROM 
                content 
            LEFT JOIN
                file
            ON
                content.fid = file.fid
            WHERE sid = ' . $sid;
        try {
            $db     = getDB();
            $query  = $db->query($sql);
            $stop   = $query->fetchAll(PDO::FETCH_OBJ);
            $db     = null;

            echo json_encode($stop);
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }
    function getFileById($fid) {
        $sql = '
            SELECT 
                file_name,
                file_type
            FROM 
                file 
            WHERE 
                fid = ' . $fid;
        try {
            $db     = getDB();
            $query  = $db->query($sql);
            $tour   = $query->fetchAll(PDO::FETCH_OBJ);
            $db     = null;
    
            echo json_encode($tour);
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }


    function postStop() {
        global $app;

        $req    = json_decode($app->request->getBody());
        $posts  = get_object_vars($req);
        $date   = date('Y-m-d h:i:s', time());
     
        $sql = "
            INSERT INTO 
                stop (`title`, `description`, `latitude`, `longitude`, `status`, `last_updated`, `fid`) 
            VALUES 
                (:title, :description, :latitude, :longitude, :status, :last_updated, :fid)";
        try {
            $db = getDB();
            $stmt = $db->prepare($sql);  
            $stmt->bindParam('title', html_entity_decode($posts['title']));
            $stmt->bindParam('description', html_entity_decode($posts['description']));
            $stmt->bindParam('latitude', $posts['latitude']);
            $stmt->bindParam('longitude', $posts['longitude']);
            $stmt->bindParam('status', $posts['status']);
            $stmt->bindParam('fid', $posts['fid']);
            $stmt->bindParam('last_updated', $date);
            $stmt->execute();

            $result = $db->lastInsertId();
            print_r($result);

            $db = null;
        } catch(PDOException $e) {
            echo json_encode($e->getMessage()); 
        }
    }
    function postStopContent($sid) {
        global $app;

        $req    = json_decode($app->request->getBody());
        $posts  = get_object_vars($req);
     
        $sql = "
            INSERT INTO 
                content (`type`, `body`, `sid`, `fid`) 
            VALUES 
                (:type, :body, :sid, :fid)";
        try {
            $db = getDB();
            $stmt = $db->prepare($sql);  
            $stmt->bindParam('type', $posts['type']);
            $stmt->bindParam('body', html_entity_decode($posts['body']));
            $stmt->bindParam('sid', $sid);
            $stmt->bindParam('fid', $posts['fid']);
            $stmt->execute();

            $result = $db->lastInsertId();
            print_r($result);

            $db = null;
        } catch(PDOException $e) {
            echo json_encode($e->getMessage()); 
        }
    }
    function postFile() {
        global $app;

        $req    = json_decode($app->request->getBody());
        $vars   = get_object_vars($req);
     
        $sql = '
            INSERT INTO 
                file (`file_name`, `file_type`) 
            VALUES 
                (:file_name, :file_type)';
        try {
            $db = getDB();
            $stmt = $db->prepare($sql);  
            $stmt->bindParam('file_name', $vars['file_name']);
            $stmt->bindParam('file_type', $vars['file_type']);
            $stmt->execute();

            $result = $db->lastInsertId();
            print_r($result);            
            
            $db = null;
        } catch(PDOException $e) {
            echo json_encode($e->getMessage()); 
        }
    }


    function updateStop($sid) {
        global $app;

        $req    = json_decode($app->request->getBody());
        $posts  = get_object_vars($req);
        $date   = date('Y-m-d h:i:s', time());
     
        $sql = "
            UPDATE 
                stop 
            SET 
                title=:title, 
                description=:description, 
                latitude=:latitude, 
                longitude=:longitude, 
                status=:status, 
                fid=:fid,
                last_updated=:last_updated
            WHERE 
                sid=:sid";
        try {
            $db = getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('sid', $sid);
            $stmt->bindParam('title', html_entity_decode($posts['title']));
            $stmt->bindParam('description', html_entity_decode($posts['description']));
            $stmt->bindParam('latitude', $posts['latitude']);
            $stmt->bindParam('longitude', $posts['longitude']);
            $stmt->bindParam('status', $posts['status']);
            $stmt->bindParam('fid', $posts['fid']);
            $stmt->bindParam('last_updated', $date);
            $stmt->execute();

            $db = null;
        } catch(PDOException $e) {
            echo json_encode($e->getMessage()); 
        }
    }
    function updateStopContent($sid, $cid) {
        global $app;

        $req    = json_decode($app->request->getBody());
        $posts  = get_object_vars($req);
     
        $sql = "
            UPDATE 
                content 
            SET 
                body=:body, 
                fid=:fid,
            WHERE 
                cid=:cid";
        try {
            $db = getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('body', html_entity_decode($posts['body']));
            $stmt->bindParam('fid', $posts['fid']);
            $stmt->bindParam('cid', $cid);
            $stmt->execute();

            $db = null;
        } catch(PDOException $e) {
            echo json_encode($e->getMessage()); 
        }
    }


    function deleteStop($sid) {
        $sql = "
            DELETE FROM 
                stop 
            WHERE 
                sid=:sid";
        try {
            $db = getDB();
            $stmt = $db->prepare($sql);  
            $stmt->bindParam('sid', $sid);
            $stmt->execute();

            $db = null;
        } catch(PDOException $e) {
            echo json_encode($e->getMessage());
        }
    }
