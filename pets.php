<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';

class PetManagementAPI
{
    // Users operations remain unchanged...

    // Pets operations
    function addPet($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO pets(Name, Species, Breed, Age, Photo, OwnerID, Sex, Weight) VALUES(:name, :species, :breed, :age, :photo, :ownerid, :sex, :weight)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":name", $json["name"]);
        $stmt->bindParam(":species", $json["species"]);
        $stmt->bindParam(":breed", $json["breed"]);
        $stmt->bindParam(":age", $json["age"]);
        $stmt->bindParam(":photo", $json["photo"]);
        $stmt->bindParam(":ownerid", $json["ownerid"]);
        $stmt->bindParam(":sex", $json["sex"]);
        $stmt->bindParam(":weight", $json["weight"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function updatePet($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "UPDATE pets SET Name = :name, Species = :species, Breed = :breed, Age = :age, Photo = :photo, OwnerID = :ownerid, Sex = :sex, Weight = :weight WHERE PetID = :petid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":name", $json["name"]);
        $stmt->bindParam(":species", $json["species"]);
        $stmt->bindParam(":breed", $json["breed"]);
        $stmt->bindParam(":age", $json["age"]);
        $stmt->bindParam(":photo", $json["photo"]);
        $stmt->bindParam(":ownerid", $json["ownerid"]);
        $stmt->bindParam(":sex", $json["sex"]);
        $stmt->bindParam(":weight", $json["weight"]);
        $stmt->bindParam(":petid", $json["petid"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function deletePet($json)
    {
        include 'db.php';
        $json = json_decode($json, true);
        $sql = "DELETE FROM pets WHERE PetID = :petid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":petid", $json["petid"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function getPetDetails()
    {
        include 'db.php';
        $sql = "SELECT PetID, Name, Species, Breed, Age, Photo, OwnerID, Sex, Weight FROM pets";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }
}

// Handling the HTTP request methods
$api = new PetManagementAPI();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] == 'add') {
            $data = file_get_contents("php://input");
            echo $api->addPet($data);
        } elseif (isset($_GET['action']) && $_GET['action'] == 'update') {
            $data = file_get_contents("php://input");
            echo $api->updatePet($data);
        }
        break;
    
    case 'DELETE':
        $data = file_get_contents("php://input");
        echo $api->deletePet($data);
        break;
    
    case 'GET':
        echo $api->getPetDetails();
        break;

    default:
        echo json_encode(["message" => "Method not supported"]);
        break;
}
?>
