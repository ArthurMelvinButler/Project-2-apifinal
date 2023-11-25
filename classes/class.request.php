<?php
use Respect\Validation\Rules\Exists;



require_once "class.dbhandler.php";
require_once "class.users.php";
require_once "class.games.php";


class Request extends dbhandler
{
    public function __construct()
    {
        parent::__construct();
    }

    function __destruct()
    {
    }

    public function rateLimitCheck($server_data)
    {
       
        $total_user_calls = 0;
        $max_calls_limit = RL_MAX;
        $time_period = RL_SECS;

// It was difficult using Redis but i still tried and looked for external sources
        $client_key = isset($server_data["HTTP_API_KEY"]) ? $server_data["HTTP_API_KEY"] : NULL;

        if (!$client_key) {
            $this->log->error(__METHOD__ . " api key not provided in the request");
            return -1;
        }

        try {

            if (!$this->redisDB->exists($client_key)) {
                $this->redisDB->set($client_key, 1);
                $this->redisDB->expire($client_key, $time_period);
                $total_user_calls = 1;
            } else {
                $this->redisDB->INCR($client_key);
                $total_user_calls = $this->redisDB->get($client_key);
                if ($total_user_calls > $max_calls_limit) {
                    $this->log->info("User " . $client_key . " limit exceeded.");
                    return -2;
                }
            }
        } catch (RedisException $e) {
            $this->log->error("Error with rate limiting");
        }

        header("X-Rate-Limit-Limit: " . $max_calls_limit);
        header("X-Rate-Limit-Remaining: " . $max_calls_limit - $total_user_calls);
        header("X-Rate-Limit-Used: " . $total_user_calls);
        header("X-Rate-Limit-Reset: " . time() + $this->redisDB->ttl($client_key));

        return 1;
    }
       
       
    public function checkApiKey($server_data)
    {
        $response["key_id"] = -1;
        $response["permissions"] = array();

        $key = isset($server_data["HTTP_API_KEY"]) ? $server_data["HTTP_API_KEY"] : NULL; 
        if (!$key) {
            $this->log->error(__METHOD__ . " api key not provided in the request");
            return $response;
        }

        $key_parts = explode("_", $key);
            $this->log->error(__METHOD__ . " invalid api key format");
            return $response;
        }

        $checksum = crc32(($key_parts[1] . API_SECRET)); 
        if ($checksum != $key_parts[2]) {
            $this->log->error(__METHOD__ . " invalid api key checksum.");
          
            return $response;
        }

        
        $info = $this->apiKeyInfo($key); 
        if ($info["key_id"] == -1) {
            $this->log->error(__METHOD__ . " api key [$key] not found in db.");
            return $response;
        }

        if (empty($info["permissions"])) {
            $this->log->error(__METHOD__ . " api key has no permissions. Consult an employee or add permissions.");
          
            return $response;
        }

        return $info;
    }   

    public function apiKeyInfo($key)
    {
        $response = [
            "key_id" => -1,
            "permissions" => null,
            "error" => null
        ];
    
        try {
            $query = "SELECT
                        uk.key_id,
                        COALESCE(pr.parent, -1) AS parent,
                        pr.resource,
                        GROUP_CONCAT(m.method) AS methods
                      FROM
                        user_keys AS uk
                      INNER JOIN
                        users AS u ON u.user_id = uk.user_id
                      INNER JOIN
                        key_permissions AS kp ON uk.key_id = kp.key_id AND kp.status = 1
                      INNER JOIN
                        permissions AS pr ON kp.permission_id = pr.permission_id AND pr.status = 1
                      INNER JOIN
                        methods AS m ON kp.method_id = m.method_id
                      WHERE
                        uk.key = ? AND uk.status = 1 AND u.status = 1
                      GROUP BY
                        uk.key_id, pr.parent, pr.resource
                      ORDER BY
                        uk.key_id, pr.parent, pr.resource";
    
            $stmt = $this->sqlDB->prepare($query);
            $stmt->bind_param("s", $key);
    
            if (!$stmt->execute()) {
                $this->log->error("SQL Error");
                $response["error"] = "Processing Request error";
            } else {
                $result = $stmt->get_result();
    
                if ($result->num_rows < 1) {
                    $this->log->error("No permissions");
                    $response["error"] = "Information not found for apiKey";
                } else {
                    $associativeArray = [];
                    $currentKeyID = null;
    
                    foreach ($result as $row) {
                        $response["key_id"] = $row['key_id'];
                        $keyID = 1;
                        $parent = $row['parent'];
                        $resource = $row['resource'];
                        $methods = explode(',', $row['methods']);
    
                        if ($keyID !== $currentKeyID) {
                            $associativeArray[$keyID] = [
                                "key_id" => $row['key_id'],
                            ];
                            $currentKeyID = $keyID;
                        }
    
                        if (!isset($associativeArray[$keyID][$parent])) {
                            $associativeArray[$keyID][$parent] = [];
                        }
    
                        if (!isset($associativeArray[$keyID][$parent][$resource])) {
                            $associativeArray[$keyID][$parent][$resource] = [];
                        }
    
                        $associativeArray[$keyID][$parent][$resource] = $methods;
                    }
    
                    $response["permissions"] = $associativeArray;
                }
            }
    
            $stmt->close();
        } catch (Exception $e) {
            $this->log->error("Error: " . $e->getMessage());
            $response["error"] = "An error occurred while processing the request";
        }
    
        return $response;
    }
    
        
   
    private function parsePutFormData($putData)
    {
        $formData = [];
        list($boundary, $data) = explode("\r\n", $putData, 2);

        // Split the parts into key-value pairs
        $parts = explode($boundary, $data);

        foreach ($parts as $part) {
            if (empty($part)) continue;

          
            if (preg_match('/Content-Disposition: form-data; name="([^"]*)"\s*\r\n\r\n(.*)\r\n/', $part, $matches)) {
                $name = $matches[1];
                $value = $matches[2];
                $formData[$name] = $value;
            }
        }

        return $formData;
    }

    public function process($data, $permissions)
    {
       
        $response["rc"] = -1;
        $response["message"] = "Invalid Request";
        $errorFound = 0;


        $clientRequest = $data["REQUEST_URI"]; 
        $clientRequestArray = explode("/", ltrim($clientRequest, "/"));  
        $requestMethod = isset($data["REQUEST_METHOD"]) ? $data["REQUEST_METHOD"] : "GET"; 
        $parentResource = isset($clientRequestArray[1]) ? $clientRequestArray[1] : -1;  

        $this->log->info("request received ------" . $requestMethod . '------');  

        $service = null;

        if (!isset($permissions["1"][$parentResource]))
         { 
            $this->log->debug("NO ACCESS for for parentResource: $parentResource");
            $response["rc"] = -2;
            $response["message"] = "No permissions to access parentResource --$parentResource--";
            $errorFound = 1;
        }else{
            $this->log->debug("granted parent resource --$parentResource--");
        }
        

        if ($errorFound == 0) {
          
            switch ($parentResource) {
                case 'users':
                    $service = new User($permissions);
                    break;
                case 'orders':
                    break;
                case 'products':
                    break;
                case 'shipments':
                    $service = new shipments($permissions);
                    break;
                default:
                    $this->log->info("unknown resource requested...");
                    break;
            }


            if ($service) {
           
                switch ($requestMethod) {
                    case 'GET':
                        $response = $service->GET($clientRequestArray);
                        break;
    
                    case 'POST':
                        $postData = $this->generate_assoc_array($_POST); 
    
                        $response = $service->POST($clientRequestArray, $postData);
                        break;
                    case 'PUT':
                        $rawPutData = file_get_contents("php://input");
                        $parsedData = $this->parsePutFormData($rawPutData);
                        $response = $service->PUT($clientRequestArray, $parsedData);
                        break;
                    case 'DELETE':
                        $response = $service->DELETE($clientRequestArray);
                        break;
                    default:
                        $response["rc"] = -3;
                        $response["message"] = "Unsupported Request";
                        $this->log->info("unsupported request");
                        break;
                }
            }

        }


        echo json_encode($response);
    }
}

