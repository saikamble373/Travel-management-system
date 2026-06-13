<?php
include('includes/config.php');
try {
    $sqlList = [
        "CREATE TABLE IF NOT EXISTS tblwishlist (
            id int(11) NOT NULL AUTO_INCREMENT,
            PackageId int(11) DEFAULT NULL,
            UserEmail varchar(100) DEFAULT NULL,
            PostingDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        
        "CREATE TABLE IF NOT EXISTS tblreviews (
            id int(11) NOT NULL AUTO_INCREMENT,
            PackageId int(11) NOT NULL,
            UserEmail varchar(100) NOT NULL,
            Rating float NOT NULL,
            Comment text DEFAULT NULL,
            PostingDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
    ];

    foreach ($sqlList as $sql) {
        $dbh->exec($sql);
    }
    echo "Tables 'tblwishlist' and 'tblreviews' created successfully.";
} catch(PDOException $e) {
    echo $e->getMessage();
}
?>
