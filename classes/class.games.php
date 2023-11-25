<?php

use Respect\Validation\Validator as v;




class Shipments extends db_handlers
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
    //done
    public function getAllgames()
    {
        $response["rc"] = -21;
        $response["message"] = "Games' Details Not Found";

        try {
            $query = "SELECT * FROM games;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting games";
                $this->log->error("query execution error for getting all games");
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $response["rc"] = -6;
                $response["message"] = "Error reading records for games";
                $this->log->debug("error: no results retreived");
                $stmt = null;
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 51;
        $response["message"] = "Success";
        $this->log->info("Got all Games.");

        http_response_code(200);
        return $response;
    }


    public function getGamesDetails($requestID)
    {
        $response["rc"] = -22;
        $response["message"] = "Games Details Not Found for id $requestID";

        if (!v::numericVal()->positive()->validate($requestID)) {
            $response["rc"] = "needs to check";
            $response["message"] = "Invalid ID. Expected INT value.";
            return $response;
        }

        try {
            $query = "SELECT * FROM games WHERE game_id = ?;";
            if ($this->sqlDB !== null) {
                $stmt = $this->sqlDB->prepare($query);
                $stmt->bind_param("i", $requestID);
            } else {
                $response["rc"] = -4;
                $response["message"] = "No database connection";
                $this->log->error("lost database connection");
                http_response_code(500);
                return $response;
            }

            if (!$stmt->execute()) {
                $stmt = null;
                $response["rc"] = -5;
                $response["message"] = "Error getting game with id $requestID";
                $this->log->error("query execution error for getting games with id");
                return $response;
            }

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = null;
                $response["rc"] = -6;
                $response["message"] = "Error reading game record of provided ID";
                $this->log->debug("error: no results received");
                return $response;
            }

            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $response["rc"] = 53;
        $response["message"] = "Success";
        $this->log->info("Successful request execution. Got games with provided id.");
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

        for ($i = 0; $i < sizeof($methodsArray); $i++) {
            if ($methodsArray[$i] == $method) {
                $this->log->debug("granted access for request using method: $method");
                $result = true;
                return $result;
            }
        }

        $this->log->debug("No permissions to access parent: $parentResource, subresource: $subResource, method: $method");

        return $result;

        public function GET($requestParameters)
       
        {
            $response["rc"] = -19;
            $response["message"] = "Invalid Request";
    
            $this->log->info("processing GET fx request");
    
            $subResource = isset($requestParameters[2]) ? $requestParameters[2] : -1;
            $parentResource = $requestParameters[1];
    
            $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
            if ($validRequest) {
                $response = $subResource < 1 ? $this->getAllgames() : $this->getgamesDetails($subResource);
            } else {
                $response["rc"] = -20;
                $response["message"] = "No permission to access the resource using method GET";
                $this->log->error("no access to $subResource using method --GET--");
            }
    
            return $response;
        }
    
      
    
        public function POST($requestParameters, $postData)
        {
            $response["rc"] = 0;
            $response["message"] = "hold tight for update";
            return $response;
        }
    
      
    
        public function getUpdatedDetails($requestID, $response)
        {
            try {
                $query = "SELECT * FROM games WHERE game_id = ?;";
                if ($this->sqlDB !== null) {
                    $stmt = $this->sqlDB->prepare($query);
                    $stmt->bind_param("i", $requestID);
                } else {
                    $response["error in updating games"] = "database connection error";
                    $this->log->error("lost database connection");
                    http_response_code(500);
                    return $response;
                }
    
                if (!$stmt->execute()) {
                    $stmt = null;
                    $response["error"] = "";
                    $response["error_gettinggames"] = "games new details failed";
                    $this->log->error("query execution error for getting update game details");
                    return $response;
                }
    
                $result = $stmt->get_result();
    
                if ($result->num_rows === 0) {
                    $stmt = null;
                    $response["error_getting_updated_games"] = "update not Found for games with id $requestID";
                    $this->log->error("could not find udpated games details using ID");
                    return $response;
                }
    
                while ($row = $result->fetch_assoc()) {
                    $response["newdata"][] = $row;
                }
    
                $stmt->close();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
    
            $response["rc"] = 51;
            $response["message"] = "Success";
            $this->log->info("Retrieved updated details");
            http_response_code(200);
            return $response;
        }
    
        public function PUT($requestParameters, $putData)
        {
            $response["rc"] = -29;
            $response["message"] = "Invalid Request";
    
            $request = isset($requestParameters[2]) ? $requestParameters[2] : -1;
            $this->log->info("processing PUT fx request for id $request");
    
            $subResource = $request;
            $parentResource = $requestParameters[1];
    
            $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
            if (!$validRequest) {
                $response["rc"] = -30;
                $this->log->error("no access to resource $parentResource using method --PUT--");
                return $response;
            }
    
            
            $response = $this->getgamesDetails($request);
            if ($response["message"] != "Success") {
                $this->log->error("Could not find games with the id provided");
                return $response;
            }
    
            try {
    
                $allowedKeys = ["game_id", "brand_id", "name", "description" , "price", "status"];
                $query = "UPDATE games SET ";
                $bindParams = [];
    
                foreach ($allowedKeys as $key) {
                   
                    if (isset($putData[$key])) {
                        $query .= "$key = ?, ";
                        $bindParams[] = $putData[$key];
                    }
                }
    
             
                $query = rtrim($query, ", ");
    
                
                $query .= " WHERE game_id = ?"; 
                $bindParams[] = $request; 
    
               
                if ($this->sqlDB !== null) {
                    $stmt = $this->sqlDB->prepare($query);
                } else {
                    $response["rc"] = -4;
                    $response["message"] = "No database connection";
                    $this->log->error("lost database connection");
                    http_response_code(500);
                    return $response;
                }
    
              
                $types = str_repeat('s', count($bindParams));
    
              
                $stmt->bind_param($types, ...$bindParams);
    
                if (!$stmt->execute()) {
                    $stmt = null;
                    $response["rc"] = -5;
                    $response["message"] = "Error updating games";
                    $this->log->error("query execution error for updating the list of games");
                    http_response_code(500);
                } else {
                    $this->log->info("Successful request games updated");
                    $response = $this->getUpdatedDetails($request, $response);
                }
                $stmt->close();
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
            }
    
            $response["rc"] = 58;
            $response["message"] = "Success. games Updated";
            return $response;
        }
    
        //-----------------------------------------------
    
        public function DELETE($requestParameters)
        {
            $response["rc"] = -33;
            $response["message"] = "Invalid Request";
    
            $request = isset($requestParameters[2]) ? $requestParameters[2] : -1;
            $this->log->info("processing DELETE fx request");
    
            $subResource = $request;
            $parentResource = $requestParameters[1];
    
            $validRequest = $this->checkValidMethod($parentResource, $subResource, __FUNCTION__);
            if (!$validRequest) {
                $response["rc"] = -34;
                $this->log->error("no access to resource $parentResource using method --DELETE--");
                return $response;
            }
    
            
            $response = $this->getGameDetails($request);
            if ($response["message"] != "Success") {
                $this->log->info(" games will be delete");
                return $response;
            }
    
            try {
                $query = "DELETE FROM games WHERE game_id = ?";
                if ($this->sqlDB !== null) {
                    $stmt = $this->sqlDB->prepare($query);
                    $stmt->bind_param("i", $request);
    
                    if ($stmt->execute()) {
                        $stmt->close();
                        $response["rc"] = 60;
                        $response["message"] = "Success. games deleted with id $request";
                        $this->log->info("games deleted. request complete");
                        http_response_code(200);
                    } else {
                        $stmt->close();
                        $response["rc"] = -1;
                        $response["message"] = "Error deleting Games with id $request";
                        $this->log->error("query execution error for deleting a Game with given ID");
                        http_response_code(500);
                        return $response;
                    }
                } else {
                    $response["rc"] = -4;
                    $response["message"] = "No database connection";
                    $this->log->error("lost database connection");
                    http_response_code(500);
                    return $response;
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
    
            return $response;
        }
    }
}







































