<?php 

$redis = new Redis();
$redis->connect('redis-18192.c281.us-east-1-2.ec2.cloud.redislabs.com', 18192);
$redis->auth('MERqihE0z2ZwtVVNW1ePQKhIlHrhDSkf');

$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017/");
$database = "guvi";
$collection = "users";
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
   
    $redisId = $_POST["redisId"];
    $redis->del("session:$redisId");
    $response = array(
        "status" => "success",
        "message" => "Logout successful",
    );

    echo json_encode($response);
}

if (isset($_POST['action']) && $_POST['action'] === 'valid-session'){
  $redisId = $_POST["redisId"];
  if ($redis->get("session:$redisId")) {
    $sessionData = $redis->get("session:$redisId");
    $response = array(
        "status" => "success",
        "message" => "Session is valid",
    );
    echo json_encode($response);
  }
  else
  {
    $response = array(
        "status" => "error",
        "message" => "Session is invalid",
    );
    echo json_encode($response);
  }
}


if (isset($_POST['action']) && $_POST['action'] === 'get-data'){
  $redisId = $_POST["redisId"];
  $sessionData = $redis->get("session:$redisId");

  $email = $sessionData;  

  $filter = ['email' => $email];

  $options = [];

  $query = new MongoDB\Driver\Query($filter, $options);

  $cursor = $manager->executeQuery("$database.$collection", $query);

  foreach ($cursor as $document) {
    $data[] = $document;
  }

  if (!empty($data)) {
    $response = ['status' => 'success', 'data' => $data];
  }else{
    $response = ['status' => 'error', 'message' => 'No data found.'];
  }
  echo json_encode($response);
}

if (isset($_POST['action']) && $_POST['action'] === 'update-data'){
  $email = $_POST["email"];
  $data = $_POST["data"];

  $dob = $data['dob'];
  $contact = $data['contact'];
  $age = $data['age'];


  $filter = ['email' => $email];
  $update = ['$set' => ['age' => $age, 'contact' => $contact , 'dob' => $dob]];


  $options = ['multi' => false, 'upsert' => false];

 
  $bulk = new MongoDB\Driver\BulkWrite;
  $bulk->update($filter, $update, $options);
  $result = $manager->executeBulkWrite("$database.$collection", $bulk);

  if ($result->getModifiedCount() > 0) {
    $response = ['status' => 'success', 'message' => 'updated successfully'];
  } else {
    $response = ['status' => 'error', 'message' => 'update failed'];
  }

  echo json_encode($response);
}

?>