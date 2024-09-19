
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: rgb(2,0,36);
            background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(27,27,126,0.865983893557423) 35%, rgba(0,212,255,1) 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group input[type="file"] {
            padding: 8px;
            font-size: 16px;
        }
        .input-group button {
            padding: 8px 20px;
            background-color: rgb(2,0,36);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .input-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload CSV File</h2>
        <form action="upload_csv.php" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <input type="file" name="csvFile" id="csvFile" required accept=".csv">
            </div>
            <div class="input-group">
                <button type="submit" value="upload">Upload CSV</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileSize = $_FILES['csv_file']['size'];
        $fileType = $_FILES['csv_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Check if the uploaded file is a CSV
        if ($fileExtension == 'csv') {
           
            $uploadFileDir = './uploaded_files/';
            $dest_path = $uploadFileDir . $fileName;

            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                

                
                header("Location: index.php");//supposed to put test cases on whether the upload was successful
                exit();
            } else {
                
                header("Location: index.php?");
                exit();
            }
        } else {
            
            header("Location: index.php");
            exit();
        }
    } else {
        
        header("Location: index.php");
        exit();
    }
}
?>

