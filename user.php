<?php 

class User {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }
    public function getUser($data) {
        $query='';
        if (is_array($data)) {
            $query = "select * from users where id = ".$data['id'];
        } else {
            $query = "select * from users where id = ".$data;
        }
        
        $result = mysqli_query($this->con, $query);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }
    public function checkLogin($data) {

        $password = !empty(trim($data->password)) ? trim($data->password) : null;
        $email = !empty(trim($data->email)) ? trim($data->email) : null;
        if (empty($email)) {
            $response = array(
                'login'=>"failed",
                'message'=> 'Email id required',
            );
            http_response_code(401); 
        } elseif (empty($password)){
            $response = array(
                'login'=>"failed",
                'message'=> 'Password is required',
            );
            http_response_code(401); 
        } else {
            $query = "select * from users where email = '".$email."'";
            $result = mysqli_query($this->con, $query);
            if (mysqli_num_rows($result) != 0 && !empty($password)) {
                $row = mysqli_fetch_assoc($result);
      
                if (password_verify($password,$row['password'])) {
                    $response = array(
                        'username'=> $row['name'],
                        'login'=>"success",
                        'id'=>$row['id'], 
                        'profile_pic'=>$row['profile_pic'],
                        'message'=> "Login successful"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                    );
                    http_response_code(200);  
                } else {
                    $response = array(
                        'username'=> $row['name'],
                        'login'=>"failed",
                        'message'=> "password didn't match",
                    ); 
                    http_response_code(401);  
                }
            } else {
                $response = array(
                    'username'=> $email,
                    'login'=>"failed",
                    'message'=> 'Email does not exists or Password did not provide',
                );
                http_response_code(401);  
            }
        }

        return $response; 
    }

    public function addUser($data) {
        $name = !empty($data->name) ? filter_var($data->name, FILTER_SANITIZE_STRING): '';
        $email = !empty($data->email) ? filter_var($data->email, FILTER_SANITIZE_EMAIL): '';
        $phone = !empty($data->phone) ? $data->phone : 0;
        $password = !empty($data->password) ? $data->password: '';
        $cpassword = !empty($data->cpassword) ? $data->cpassword: '';
        $error =[];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error["email"] = "Email is not a valid";
         }
      
         if (empty($name)) {
            $error["name"] = "Name is required";
         }
      
         if (empty($password)) {
            $error["password"] = "Password is required";
         }
      
         if (empty($cpassword)) {
            $error["cpassword"] = "Confirm password is required";
         } elseif ($password != $cpassword) {
            $error["cpassword"] = "Password and Confirm password did not match";
         }
      
         if (empty($phone)) {
            $error["phone"] = "Phone number required";
         } elseif (!preg_match("/^[6-9][0-9]{9}$/", $phone)) {
            $error["phone"] = "Phone number is not valid";
         }
      
         if (empty($error)) {
            $final_passowrd = password_hash($password, PASSWORD_DEFAULT);

            $name = mysqli_real_escape_string($this->con, $email);
            $email = mysqli_real_escape_string($this->con, $email);
            $phone = mysqli_real_escape_string($this->con, $email);
            $query = "insert into users (name,email,phone,password) value ('".$name."','".$email."','".$phone."','".$final_passowrd."')";
            $result = mysqli_query($this->con, $query);
            if (mysqli_affected_rows($this->con) != 0) {

                $id = mysqli_insert_id($this->con);
         
                $getUserResult = $this->getUser($id);
                $data = array(
                    'data'=> $getUserResult,
                    'sign_up'=>'Success', 
                    'message'=> "Sign up successful"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                );
                http_response_code(200); 
                return $data;
            }
         } else {
            http_response_code(401); 
            $data = array(
                'sign_up'=>"failed",
                'message'=> $error
            );
            return $data;
         }

    }

}