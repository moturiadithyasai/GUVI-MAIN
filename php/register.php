<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$servername = "sql12.freesqldatabase.com";
$username = "sql12666788";
$password = "sbaeHC6ig1";
$database = "sql12666788";
$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "CREATE TABLE IF NOT EXISTS guvi_users (
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    mongodbId VARCHAR(255) NOT NULL,
    PRIMARY KEY (email)
)";

$email = $_POST['email'];
$password = $_POST['password'];
$password = password_hash($password, PASSWORD_DEFAULT); //PASSWORD_DEFAULT is used for hashing password with defalut algorithm


if (!mysqli_query($conn, $sql)) 
{
    echo "Error creating table: " . mysqli_error($conn);
}
$sql = "SELECT * FROM guvi_users WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
if (mysqli_num_rows($result) > 0) {
    $response = array(
        "status" => "user_error",
        "message" => "User already exists"
    );
    echo json_encode($response);
    exit();
}
}

$uri = 'mongodb://localhost:27017/';
$manager = new MongoDB\Driver\Manager($uri);

$database = "guvi";
$collection = "users";

$bulk = new MongoDB\Driver\BulkWrite;

$document = [
    'email' => $email,
    'dob' => '',
    'age' => '',
    'contact'=>'',
];

$bulk = new MongoDB\Driver\BulkWrite;
$_id = $bulk->insert($document);
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$result = $manager->executeBulkWrite("$database.$collection", $bulk, $writeConcern);


$mongoId = (string)$_id;
$sql = "INSERT INTO guvi_users (email, password, mongodbId) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("sss", $email, $password, $mongoId);

    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $response = ['status' => 'success', 'message' => 'Registered successfully'];
    } else {
        $response = ['status' => 'error', 'message' => 'Registration failed'];
    }

    $stmt->close();
}
else {

    echo "Error preparing statement: " . $conn->error;
}

echo json_encode($response);

?>