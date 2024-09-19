
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
    if (isset($_FILES["csvFile"])) {
        
        $database_file = 'test.db';

        try {
            // Connect to SQLite database or create if not exists
            $db = new SQLite3($database_file);
            
            $createTableSQL = "CREATE TABLE IF NOT EXISTS csv_import (
                                id INTEGER PRIMARY KEY,
                                name TEXT NOT NULL,
                                surname TEXT NOT NULL,
                                initials TEXT NOT NULL,
                                age INTEGER NOT NULL,
                                date_of_birth TEXT NOT NULL
                            )";
            
            if (!$db->exec($createTableSQL)) {
                die("Error creating table: " . $db->lastErrorMsg());
            }

            // Open uploaded CSV file
            $csvFile = fopen($_FILES["csvFile"]["tmp_name"], "r");
            
            
            $db->exec('BEGIN');

            $count = 0;
            $batchSize = 100; 
            $batchValues = array(); // Array to store batch values

            while (($data = fgetcsv($csvFile)) !== false) {
                if ($count > 0) { // Skip header row
                    $name = $db->escapeString(trim($data[1], "'\"")); // Strip single and double quotes
                    $surname = $db->escapeString(trim($data[2], "'\""));
                    $initials = $db->escapeString(trim($data[3], "'\""));
                    $age = trim($data[4], "'\"");
                    $dob = DateTime::createFromFormat("'d/m/Y'", $data[5]); 
                    

                    if ($dob !== false) {
                         $dobFormatted = $dob->format('Y-m-d'); // ISO 8601 format

                        
                        $batchValues[] = "('$name', '$surname', '$initials', $age, '$dobFormatted')";
                    } else {
                        echo "Invalid date format for record: " . implode(', ', $data) . "<br>";
                    }
                }
                $count++;

                // Check if batch size is reached or end of file is reached
                if (count($batchValues) == $batchSize || feof($csvFile)) {
                    $sql = "INSERT INTO csv_import (name, surname, initials, age, date_of_birth) VALUES " . implode(',', $batchValues);
                    if (!$db->exec($sql)) {
                        die("Error inserting records: " . $db->lastErrorMsg());
                    }
                    // Clear batch values array
                    $batchValues = array();
                }
            }

            // Commit transaction
            $db->exec('COMMIT');
            
            fclose($csvFile);
            $db->close();
            
            echo "Number of records inserted: " . ($count - 1); 
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="sucess.gif" alt="" width="250">
    </div>

   
</body>
</html>
