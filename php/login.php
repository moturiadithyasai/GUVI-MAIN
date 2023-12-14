<?php 

$servername = "sql12.freesqldatabase.com";
$username = "sql12666788";
$password = "sbaeHC6ig1";
$database = "sql12666788";
// port 3306;


$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$redis = new Redis();
$redis->connect('redis-18192.c281.us-east-1-2.ec2.cloud.redislabs.com', 18192);
$redis->auth('MERqihE0z2ZwtVVNW1ePQKhIlHrhDSkf');




$email = $_POST["email"];
$password = $_POST["password"];

$sql = "SELECT * FROM guvi_users WHERE email = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $email); 

    $stmt->execute();
    $result = $stmt->get_result();

if($result == FALSE){
    $response = array(
        "status" => "error",
        "message" => "User not found"
    );
    echo json_encode($response);
}
elseif (mysqli_num_rows($result) == 0) {
    $response = array(
        "status" => "error",
        "message" => "User not found"
    );
    echo json_encode($response);
} else {
    $row = mysqli_fetch_assoc($result);

    if(password_verify($password, $row['password'])){
        $session_id = uniqid();
        $redis->set("session:$session_id", $email);
        $redis->expire("session:$session_id", 10*60);
       

        $payload = array(
            "email" => $row['email'],
        );
      
        $response = array(
            "status" => "success",
            "message" => "Login successful",
            'session_id' => $session_id
        );
        echo json_encode($response);
    } else {
        $response = array(
            "status" => "error",
            "message" => "Incorrect password"
        );
        echo json_encode($response);
    }
}
}

?>