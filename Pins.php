<?php
include_once "repif_db.php";
if (isset($_POST["HostName"], $_POST["PinNo"], $_POST["Input"], $_POST["Designation"])) {
    $sqlInsert = $connection->prepare("INSERT INTO Pin (HostName, PinNo, Input, Designation) values (?,?,?,?)");
    $sqlInsert->bind_param("siis", $_POST["HostName"], $_POST["PinNo"], $_POST["Input"], $_POST["Designation"]);
    $resultOfExecute = $sqlInsert->execute();
    if (!$resultOfExecute) {
        print "Adding a new pin, failed!";
    } else {
        header("refresh: 0");
        die();
    }
}

if (isset($_POST["pinToDelete"])) {
    $sqlDelete = $connection->prepare("Delete from Pin where PinNo = ?");
    if (!$sqlDelete)
        die("Error in sql delete statement");
    $sqlDelete->bind_param("i", $_POST["pinToDelete"]);
    $sqlDelete->execute();
    $sqlDelete->close();
    header("refresh: 0");
    die();
}

if (isset($_POST["hostnameEdit"], $_POST["pinnoEdit"], $_POST["inputEdit"], $_POST["designationEdit"])) {
    $sqlUpdate = $connection->prepare("UPDATE Pin SET HostName=?, PinNo=?, Input=?, Designation=? WHERE PinNo = ?");
    if (!$sqlUpdate) {
        die("Pins couldnt be updated");
    }
    $sqlUpdate->bind_param("siisi", $_POST["hostnameEdit"], $_POST["pinnoEdit"], $_POST["inputEdit"], $_POST["designationEdit"], $_POST["pinnoEdit"]);
    $sqlUpdate->execute();

    header("refresh: 0");
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Pins Configuration</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <script src='main.js'></script>
</head>

<body>

    <h1 style="text-align:center">Pins - Technician Configuration Pages</h1>

    <?php


include_once("technav.php");
    $result = $connection->query("SELECT * FROM Pin");

    if (isset($_POST["pinToEdit"])) {
        $sqlEditPins = $_POST["pinToEdit"];
        $sqlSelect = $connection->prepare("SELECT * FROM Pin WHERE PinNo = ?");
        $sqlSelect->bind_param("i", $sqlEditPins);
        $sqlSelect->execute();
        $result = $sqlSelect->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    ?>
        <form method="POST">
            <div>
                <label>HostName</label>
                <input type="text" name="hostnameEdit" value="<?= $data[0]["HostName"] ?>">
                <input type="hidden" name="hostnameSearch" value="<?= $data[0]["HostName"] ?>">
            </div>
            <div>
                <label>PinNo</label>
                <input type="text" name="pinnoEdit" value="<?= $data[0]["PinNo"] ?>">
                <input type="hidden" name="pinnoSearch" value="<?= $data[0]["PinNo"] ?>">
            </div>
            <div>
                <label>Input</label>
                <input type="text" name="inputEdit" value="<?= $data[0]["Input"] ?>">
                <input type="hidden" name="inputSearch" value="<?= $data[0]["Input"] ?>">
            </div>
            <div>
                <label>Designation</label>
                <input type="text" name="designationEdit" value="<?= $data[0]["Designation"] ?>">
                <input type="hidden" name="designationSearch" value="<?= $data[0]["Designation"] ?>">
            </div>
            <button type="submit">Submit</button>
        </form>
        <?php
        die();
    }
    

    if ($result) {
        while ($row = $result->fetch_assoc()) {
        ?>
            <table class="table table-hover table-success">
                <tr>
                    <th>HostName</th>
                    <th>PinNo</th>
                    <th>Input</th>
                    <th>Designation</th>
                    <th>Buttons</th>
                </tr>
                <tr>
                    <td><?= $row["HostName"] ?></td>
                    <td><?= $row["PinNo"] ?></td>
                    <td><?= $row["Input"] ?></td>
                    <td><?= $row["Designation"] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="pinToDelete" value="<?= $row["PinNo"] ?>">
                            <input type="submit" value="Remove" class="btn btn-outline-dark">
                        </form>
                        <form method="POST">
                            <input type="hidden" name="pinToEdit" value="<?= $row["PinNo"] ?>">
                            <input type="submit" value="Edit" class="btn btn-outline-dark">
                        </form>
                    </td>
                </tr>
        <?php
        }
    } else {
        print "Something went wrong with selecting data";
    }
        ?>

            </table>
            <form method="POST">
                Add a New Pin: <input name="HostName" placeholder="SB_nbr">
                <input name="PinNo" placeholder="nbr">
                <input name="Input" placeholder="1 or 0">
                <input name="Designation" placeholder="GPIOnbr">
                <input type="submit" value="Add">
            </form>


</body>

</html>