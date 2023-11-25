<?php

//validation docs at https://respect-validation.readthedocs.io/en/latest/
use Respect\Validation\Validator as v; //use this wherever the library is used.

class Login extends DBHandler
{

    private $permissions;

    function __construct()
    {
        parent::__construct();
    }

    function __destruct()
    {
    }

    //done
    public function userLogin($email, $password)
    {
        $response["rc"] = -999;
        $response["message"] = "User Details Not Found with email '$email' and password";

        $this->log->info("Email is $email and password is $password");
        $user = "";
        $savedPassword = "";

        try {
            $query = "SELECT user_id AS userid, CONCAT(firstname, ' ', lastname) AS user, email, username, password FROM users WHERE email = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("s", $email);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -999;
                $response["message"] = "Error getting user with id $email";
                $this->log->error(__METHOD__ . "query execution error for finding user with email");
                http_response_code(403);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows < 1) {
                $stmt = null;
                $response["rc"] = -999;
                $response["message"] = "no user with provided email exists";
                $this->log->debug("error: no user with provided email exists");
                return $response;
            }

            $data = "";
            while ($row = $result->fetch_assoc()) {
                $data = $row;
                $user = $row["email"];
                $savedPassword = $row["password"];
            }

            if(!password_verify($password, $savedPassword)){
                $response["rc"] = -999;
                $response["message"] = "passwords do not match";
                $this->log->debug("error: no user with provided password exists");
                return $response;
            }else{
                $response["data"] = $data;
            }

            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error(__METHOD__ . "$e");
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 999;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. User Can log in");
        http_response_code(200);
        return $response;
    }

    
    public function GET($requestParameters, $params)
    {
        
        $data = json_decode($params, true);
        
        $this->log->debug(__METHOD__. " " . json_encode($data));

        $response["rc"] = -11;
        $response["message"] = "Invalid Request";

        $this->log->info("processing GET fx request");

        $parentResource = $requestParameters[1];

        
        if (isset($data["email"]) && isset($data["password"])) {
            $email = $data["email"];
            $password = $data["password"];
        } else {
            $this->log->error("Error in the structure of the received JSON: 'email' or 'password' key not found.");
            return $response;
        }
        

        switch ($parentResource) {
            case 'login':
                $response = $this->userLogin($email, $password);
                break;
        }

        return $response;
    }

}
