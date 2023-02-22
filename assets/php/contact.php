<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../phpmailer/Exception.php';
    require '../phpmailer/PHPMailer.php';
    require '../phpmailer/SMTP.php';

    $SenderEmail = "#";
    $password = "#";
    $smtp = [
        'Host'=>'#',
        'Port'=>'587',
        'Encrypt'=>'tls'
    ];

    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
        $phone = strip_tags(trim($_POST["phone"]));
        $subject = strip_tags(trim($_POST["subject"]));
		$name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim(nl2br($_POST["message"]));

        // Check that data was sent to the mailer.
        if ( empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Please complete the form and try again.";
            exit;
        }

        $email_content = "Subject: $subject\n";
        $email_content .= "Name: $name\n";
        $email_content .= "Email: $email\n\n";
        $email_content .= "Phone: $phone\n\n";
        $email_content .= "Message:\n$message\n";


        $mail = new PHPMailer;
        $mail->isSMTP(); 
        $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
        $mail->Host = $smtp['Host'];
        $mail->Port = $smtp['Port'];
        $mail->SMTPSecure = $smtp['Encrypt'];
        $mail->SMTPAuth = true;
        $mail->Username = $SenderEmail;
        $mail->Password = $password;
        $mail->setFrom($SenderEmail, "FHAMTK");
        $mail->addAddress($SenderEmail, "FHAMTK");
        $mail->Subject = "New contact request from $name";
        $mail->Body = $email_content;

 
        if(!$mail->send()){
            echo "Mailer Error: " . $mail->ErrorInfo;
        }else{
            header("location: https://fhamtk.com");
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }

?>

