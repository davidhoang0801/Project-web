<?php
    session_start();
    include_once "config.php";
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        //Kiểm tra email là đúng hoặc sai
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){ //Nếu email đúng
            //Kiểm tra email tồn tại trong cơ sở dữ liệu hoặc không?
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
            if(mysqli_num_rows($sql) > 0){ //Nếu email đã tồn tại
                echo "$email - email đã tồn tại!";
            }else{
                //Kiểm tra user tải lên tệ tin hoặc chưa
                if(isset($_FILES['image'])){ //Nếu tệp tin đã tải lên
                    $img_name = $_FILES['image']['name']; //Lấy tên hình ảnh do người dùng tải lên
                    $img_type = $_FILES['image']['type'];
                    $tmp_name = $_FILES['image']['tmp_name']; //Tên tạm thời này dùng để lưu/di chuyển tệp trong thư mục
                    
                    $img_explode = explode('.',$img_name);
                    $img_ext = end($img_explode); //Ở đây nhận được tệp tin ảnh do người dùng tải lên
    
                    $extensions = ["jpeg", "png", "jpg"];
                    if(in_array($img_ext, $extensions) === true){ //Nếu ảnh tải lên khớp với các phần tử trong mảng...
                        $types = ["image/jpeg", "image/jpg", "image/png"];
                        if(in_array($img_type, $types) === true){
                            $time = time();
                            //Di chuyển ảnh người dùng tải lên vào thư mục
                            $new_img_name = $time.$img_name;
                            if(move_uploaded_file($tmp_name,"images/".$new_img_name)){ //Nếu người dùng tải ảnh di chuyển đến thư mục thành công!
                                $ran_id = rand(time(), 100000000); //Khởi tạo id ngẫu nhiên cho người dùng
                                $status = "Đang hoạt động";
                                $encrypt_pass = md5($password);
                                //Chèn tất cả dữ liệu người dùng vào trong bảng
                                $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}')");
                                if($insert_query){ //Nếu những dữ liệu này được chèn 
                                    $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                    if(mysqli_num_rows($select_sql2) > 0){
                                        $result = mysqli_fetch_assoc($select_sql2);
                                        $_SESSION['unique_id'] = $result['unique_id'];
                                        echo "success";
                                    }else{
                                        echo "Địa chỉ email này không tồn tại!";
                                    }
                                }else{
                                    echo "Đã xảy ra lỗi. Vui lòng thử lại!";
                                }
                            }
                        }else{
                            echo "Vui lòng tải lên một tệp hình ảnh!";
                        }
                    }else{
                        echo "Vui lòng tải lên một tệp hình ảnh!";
                    }
                }
            }
        }else{
            echo "$email đây không phải là email!";
        }
    }else{
        echo "Tất cả các trường là bắt buộc!";
    }
?>