<?php

use Respect\Validation\Validator as v;

class User extends DBHandler
{
    private $permissions;

    function __construct($permissions)
    {
        parent::__construct();
        $this->permissions = $permissions;
    }

    function __destruct()
    {
    }

    public function test()
    {
        $this->log->info("test fx ran...");
        $response["rc"] = 100;
        $response["message"] = "Success";
        return $response;
    }

    public function getAllUsers()
    {
        $this->log->info("checking " . __METHOD__);
        $response = [
            "rc" => -15,
            "message" => "Users' Details Not Found",
        ];

        try {
            $query = "SELECT * FROM users;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting users";
                $this->log->error("Error getting all users");
                http_response_code(403);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "No results received";
                $this->log->debug("No results received");
                http_response_code(403);
                return $response;
            }

            $response["data"] = [];

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
        }

        $response["rc"] = 50;
        $response["message"] = "Success";
        $this->log->info("Got all users.");
        http_response_code(200);
        return $response;
    }

    public function getUsersDetails($requestID)
    {
        $this->log->info("checking " . __METHOD__);
        $response = [
            "rc" => -16,
            "message" => "User Details Not Found for id $requestID",
        ];

        if (!v::numericVal()->positive()->validate($requestID)) {
            $response["rc"] = "no count";
            $response["message"] = "Invalid ID. Expected INT value.";
            http_response_code(403);
            return $response;
        }

        try {
            $query = "SELECT * FROM users WHERE user_id = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $requestID);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error(" database connection error");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting user with id $requestID";
                $this->log->error("Query execution error for getting user with id");
                http_response_code(403);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading user record of provided ID: $requestID";
                $this->log->debug("Error: no results received for id $requestID");
                return $response;
            }

            $response["data"] = [];

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
        }

        $response["rc"] = 52;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Got user with provided id.");
        http_response_code(200);
        return $response;
    }

    public function getAllCustomers()
    {
        $response = [
            "rc" => -18,
            "message" => "Customer Details Not Found",
        ];

        try {
            $query = "SELECT * FROM users WHERE role_id = 2;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error(" database connection error");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting customers";
                $this->log->error("Query execution error for getting all customers");
                http_response_code(500);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading record of customer";
                $this->log->debug("Error");
                return $response;
            }

            $response["data"] = [];

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
        }

        $response["rc"] = 55;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Retrieved all customers");
        http_response_code(200);
        return $response;
    }

    public function checkValidMethod($parentResource, $subResource, $method)
    {
        $result = false;
        if (is_numeric($subResource) || $subResource == "") {
            $subResource = "/";
        }

        $methodsArray = $this->permissions["1"][$parentResource][$subResource];

        for ($i = 0; $i < count($methodsArray); $i++) {
            if ($methodsArray[$i] == $method) {
                $this->log->debug(" request using method: $method");
                $result = true;
                return $result;
            }
        }
        http_response_code(403);
        $this->log->debug("No permissions : $parentResource, subresource: $subResource, method: $method");

        return $result;
    }

    public function generateRandomString($length = 24)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#%*()+=[]{};:,.<>?';
        $randomString = '';
        $characterCount = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $characterCount - 1)];
        }

        $this->log->info("Random string generated for new user key");

        return $randomString;
    }

    public function GET($requestParameters)
    {
        $response = [
            "rc" => -11,
            "message" => "Invalid Request",
        ];

        $this->log->info("Processing GET fx request");

        $subResource = isset($requestParameters[2]) ? $requestParameters[2] : -1;
        $parentResource = $requestParameters[1];

        switch ($subResource) {
            case 'employees':
                $this->log->info("Subrequest received --" . $subResource . '--');
                $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
                if ($validRequest) {
                    $response = $this->getAllEmployees();
                } else {
                    $response["rc"] = -12;
                    $this->log->error("No access to $subResource using method --GET--");
                }
                break;
            case 'customers':
                $this->log->info("Subrequest received --" . $subResource . '--');
                $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION);
                if ($validRequest) {
                    $response = $this->getAllCustomers();
                } else {
                    $response["rc"] = -13;
                    $this->log->error("No access to $subResource using method --GET--");
                }
                break;
            default:
                $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION);
                if ($validRequest) {
                    $response = $subResource < 1 ? $this->getAllUsers() : $this->getUsersDetails($subResource);
                } else {
                    $response["rc"] = -14;
                    $this->log->error("No access to $subResource using method --GET--");
                }
                // If no ID was provided then return all users: NO MATTER IF THE OTHER PARAMETERS ARE spelled incorrectly
                break;
        }

        return $response;
    }

    public function getAllEmployees()
    {
        $response = [
            "rc" => -17,
            "message" => "Employee Details Not Found",
        ];

        try {
            $query = "SELECT * FROM users WHERE role_id = 1;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting employees";
                $this->log->error("Query execution error for getting all employees");
                http_response_code(500);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading record of employee";
                $this->log->debug("Error: No results received");
                return $response;
            }

            $response["data"] = [];

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
        }

        $response["rc"] = 54;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Retrieved all employees");
        http_response_code(200);
        return $response;
    }

    public function addUserApiKey($newlyInsertedUserID, $role_id)
    {
        $randomString = $this->generateRandomString();
        $key = "awt_" . $randomString . "_" . crc32($randomString . API_SECRET);
        $this->log->info("Assembled new user key");

        try {
            $query = "INSERT INTO `user_keys` (`key_id`, `user_id`, `key`, `expired`, `created_at`, `status`)
                      VALUES (NULL, ?, ?, 0, current_timestamp(), 1);";

            $stmt = $this->sqlDB->prepare($query);
            $stmt->bind_param("is", $newlyInsertedUserID, $key);
            $stmt->execute();
            $newlyInsertedKeyID = "";

            if ($this->sqlDB !== null) {
                $newlyInsertedKeyID = $this->sqlDB->insert_id;
                $this->log->info("New user key inserted");
            }

            if ($role_id == 1) { // Is an employee so give them all permissions
                $query = "INSERT INTO `key_permissions` (`id`, `key_id`, `permission_id`, `method_id`, `created_at`, `status`)
                          VALUES
                          -- User with role 1 can perform GET requests for all collections
                          (NULL, $newlyInsertedKeyID, 1, 1, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 2, 1, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 3, 1, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 4, 1, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 1, 2, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 1, 3, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 1, 4, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 4, 2, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 4, 3, current_timestamp(), 1),
                          (NULL, $newlyInsertedKeyID, 4, 4, current_timestamp(), 1);";
                $stmt = $this->sqlDB->prepare($query);
                $stmt->execute();
                $this->log->info("Permissions added to key for user with role 1 (employee)");
            } elseif ($role_id == 2) { // Is a customer, so give them limited permissions
                $query = "INSERT INTO key_permissions (id, key_id, permission_id, method_id, created_at, status)
                          VALUES (NULL, $newlyInsertedKeyID, 4, 1, current_timestamp(), 1);";
                $stmt = $this->sqlDB->prepare($query);
                $stmt->execute();
                $this->log->info("Permissions added to key for user with role 2 (customer)");
            }
            $stmt->close();
        } catch (PDOException $e) {
            $this->log->error("Error: " . $e->getMessage());
        }
    }

    public function POST($requestParameters, $postData)
    {
        $response = [
            "rc" => -23,
            "message" => "Invalid Request",
        ];

        $this->log->info("Processing POST fx request");

        $parentResource = $requestParameters[1];
        $subResource = $requestParameters[2];

        $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION);
        if (!$validRequest) {
            $response["rc"] = -24;
            $this->log->error("No access to resource $parentResource using method --POST--");
            return $response;
        }

        $role_id = array_key_exists('role_id', $postData) ? $postData["role_id"] : 2; // Make the new user a customer by default
        $firstname = array_key_exists('firstname', $postData) ? $postData["firstname"] : "";
        $lastname = array_key_exists('lastname', $postData) ? $postData["lastname"] : "";
        $username = array_key_exists('username', $postData) ? $postData["username"] : "";
        $email = array_key_exists('email', $postData) ? $postData["email"] : "";
        $address = array_key_exists('address', $postData) ? $postData["address"] : NULL;
        $phone = array_key_exists('phone', $postData) ? $postData["phone"] : NULL;
        $age = array_key_exists('age', $postData) ? $postData["age"] : NULL;
        $password = array_key_exists('password', $postData) ? $postData["password"] : "password";
        $status = 1;

        try {
            $query = "INSERT INTO users (`role_id`, `firstname`, `lastname`, `username`, `email`, `address`, `phone`, `age`, `password`, `status`, `created_at`)
                      VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW());";

            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("issssssisi", $role_id, $firstname, $lastname, $username, $email, $address, $phone, $age, $password, $status);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("Lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error creating new user";
                $this->log->error("Query execution error for inserting a user.");
                http_response_code(500);
                return $response;
            }

            $newlyInsertedID = "";

            if ($this->sqlDB !== null) {
                $newlyInsertedID = $this->sqlDB->insert_id;
                $response["rc"] = 56;
                $response["message"] = "Successful user creation";
                $response["new_user_id"] = $newlyInsertedID;
            }
            $stmt->close();

            $this->addUserApiKey($newlyInsertedID, $role_id);
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
        }

        http_response_code(200);
        return $response;
    }

    public function getUpdatedDetails($requestID, $response)
    {
        $this->log->debug("Getting updated user details...");
        try {
            $query = "SELECT * FROM users WHERE user_id = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $requestID);
            } else {
                $response["error_getting_updated_user"] = "Sorry... Lost database connection";
                $this->log->error("Lost database connection. Could not get updated user details");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["error_getting_updated_user"] = "STMT Error. Couldn't get updated user's new details";
                $this->log->error("Query execution error for getting updated user details");
                http_response_code(403);
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["error_getting_updated_user"] = "No results found for the newly inserted user's details";
                $this->log->debug("No results received for the newly inserted user details");
                http_response_code(403);
                return $response;
            }

            $response["data"] = [];

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
        }

        return $response;
    }

    public function PUT($requestParameters, $postData)
    {
        $response = [
            "rc" => -25,
            "message" => "Invalid Request",
        ];

        $this->log->info("Processing PUT fx request");

        $requestID = $requestParameters[2];
        $parentResource = $requestParameters[1];

        $validRequest = $this->checkValidMethod($parentResource, $requestID, __FUNCTION);
        if (!$validRequest) {
            $response["rc"] = -26;
            $this->log->error("No access to resource $parentResource using method --PUT--");
            http_response_code(403);
            return $response;
        }

        $firstname = array_key_exists('firstname', $postData) ? $postData["firstname"] : null;
        $lastname = array_key_exists('lastname', $postData) ? $postData["lastname"] : null;
        $username = array_key_exists('username', $postData) ? $postData["username"] : null;
        $email = array_key_exists('email', $postData) ? $postData["email"] : null;
        $address = array_key_exists('address', $postData) ? $postData["address"] : null;
        $phone = array_key_exists('phone', $postData) ? $postData["phone"] : null;
        $age = array_key_exists('age', $postData) ? $postData["age"] : null;
        $password = array_key_exists('password', $postData) ? $postData["password"] : null;

        $updatedFields = 0;

        if ($firstname !== null) {
            $this->updateField($firstname, $requestID, 'firstname');
            $updatedFields++;
        }
        if ($lastname !== null) {
            $this->updateField($lastname, $requestID, 'lastname');
            $updatedFields++;
        }
        if ($username !== null) {
            $this->updateField($username, $requestID, 'username');
            $updatedFields++;
        }
        if ($email !== null) {
            $this->updateField($email, $requestID, 'email');
            $updatedFields++;
        }
        if ($address !== null) {
            $this->updateField($address, $requestID, 'address');
            $updatedFields++;
        }
        if ($phone !== null) {
            $this->updateField($phone, $requestID, 'phone');
            $updatedFields++;
        }
        if ($age !== null) {
            $this->updateField($age, $requestID, 'age');
            $updatedFields++;
        }
        if ($password !== null) {
            $this->updateField($password, $requestID, 'password');
            $updatedFields++;
        }

        if ($updatedFields > 0) {
            $this->log->info("The user with ID $requestID has been updated.");
            $response = $this->getUpdatedDetails($requestID, $response);
            http_response_code(200);
        } else {
            $this->log->info("No fields have been updated.");
            $response["rc"] = -27;
            $response["message"] = "No fields updated";
            http_response_code(403);
        }

        return $response;
    }

    public function updateField($field, $requestID, $fieldName)
    {
        try {
            $query = "UPDATE `users` SET `$fieldName` = ? WHERE `user_id` = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("si", $field, $requestID);
            } else {
                $this->log->error("Lost database connection. Field $fieldName not updated.");
                http_response_code(500);
                return;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $this->log->error("Query execution error for updating field $fieldName");
                http_response_code(500);
                return;
            }
            $stmt->close();
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
        }
    }

    public function DELETE($requestParameters)
    {
        $response = [
            "rc" => -28,
            "message" => "Invalid Request",
        ];

        $this->log->info("Processing DELETE fx request");

        $requestID = $requestParameters[2];
        $parentResource = $requestParameters[1];

        $validRequest = $this->checkValidMethod($parentResource, $requestID, __FUNCTION);
        if (!$validRequest) {
            $response["rc"] = -29;
            $this->log->error("No access to resource $parentResource using method --DELETE--");
            http_response_code(403);
            return $response;
        }

        $response = $this->getUsersDetails($requestID);
        if ($response["rc"] == 52) {
            try {
                $query = "DELETE FROM users WHERE user_id = ?;";
                if ($this->sqlDB !== null) {
                    $stmt = $this->sqlDB->prepare($query);
                    $stmt->bind_param("i", $requestID);
                } else {
                    $this->log->error("Lost database connection. User not deleted.");
                    http_response_code(500);
                    return $response;
                }

                if (!$stmt->execute()) {
                    $stmt = null;
                    $this->log->error("Query execution error for deleting user");
                    http_response_code(500);
                    return $response;
                }
                $stmt->close();
                $response["message"] = "User with ID $requestID has been deleted";
                $this->log->info("User with ID $requestID has been deleted.");
                http_response_code(200);
            } catch (Exception $e) {
                $this->log->error("Error: " . $e->getMessage());
            }
        }
        return $response;
    }
}

