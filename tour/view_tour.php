<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            width: 40%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: navy;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input {
            width: 300px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="submit"] {
            width: 150px;
            background-color: #0056b3;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #004288;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>View Tour</h2>
        <?php
        $host = "localhost";
        $user = "root";
        $password = "ducanh12@#";
        $database = "travelEasy";
        $con = mysqli_connect($host, $user, $password, $database);

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }

        if (isset($_GET['idTour'])) {
            $idTour = $_GET['idTour'];
        
            // Truy vấn để lấy thông tin tour cần chỉnh sửa
            // $query = mysqli_query($con, "SELECT idTour, name, startDay, endDay, cost FROM tblTour WHERE idTour = '$idTour'");
            $query = mysqli_query($con, "SELECT tblTour.idTour, tblTour.name AS tour_name, tblTour.startDay, tblTour.endDay, tblTour.cost, tblHotel.name, tblVehicle.name AS hotel_name, tblCity.name AS City, tblDistrict.name AS District, tblWard.name AS Ward, tblVehicle.name AS vehicle_name,  tblTourGuide.name AS tourGuide_name FROM tblTour 
            JOIN tblHotel ON tblTour.idHotel = tblHotel.idHotel
            JOIN tblAddress ON tblTour.idAddress= tblAddress.idAddress
            JOIN tblCity ON tblAddress.idCity = tblCity.idCity
            JOIN tblDistrict ON tblDistrict.idCity = tblCity.idCity
            JOIN tblWard ON tblWard.idDistrict = tblDistrict.idDistrict 
            JOIN tblVehicle ON tblVehicle.idVehicle = tblTour.idVehicle 
            JOIN tblTourGuide ON tblTourGuide.idTourGuide = tblTour.idTourGuide 
            WHERE idTour = '$idTour'");
            if ($row = mysqli_fetch_assoc($query)) {
                // Display tour information
                echo "<form action='process_edit.php' method='POST'>";
                echo "<input type='hidden' name='idTour' value='" . $row['idTour'] . "'>";
                echo "<input type='text' placeholder='Name' name='name' value='" . $row['tour_name'] . "' required readonly>" ;
                echo "<input type='date' placeholder='startDay' name='startDay' value='" . $row['startDay'] . "' required readonly>";
                echo "<input type='date' placeholder='endDay' name='endDay' value='" . $row['endDay'] . "' required readonly>";
                echo "<input type='text' placeholder='cost' name='cost' value='" . number_format($row['cost'], 0, ',', '.') . "' required readonly>";
                echo "<input type='text' placeholder='hotelName' name='hotelName' value='" . $row['hotel_name'] . "' required readonly>";
                echo "<input type='text' placeholder='City' name='City' value='" . $row['City'] . "' required readonly>";
                echo "<input type='text' placeholder='District' name='District' value='" . $row['District'] . "' required readonly>";
                echo "<input type='text' placeholder='Ward' name='Ward' value='" . $row['Ward'] . "' required readonly>";
                echo "<input type='text' placeholder='Vehicle' name='Vehicle' value='" . $row['vehicle_name'] . "' required readonly>";
                echo "<input type='text' placeholder='TourGuide' name='TourGuide' value='" . $row['tourGuide_name'] . "' required readonly>";
                echo "</form>";
            } else {
                // Display a message if no tour is found
                echo "Tour not found.";
            }
        }
        mysqli_close($con);
        ?>
    </div>
</body>
</html>
