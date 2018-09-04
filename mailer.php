<?php
    // My modifications to mailer script from:
    // http://blog.teamtreehouse.com/create-ajax-contact-form
    // Added input sanitizing to prevent injection
    use \Mailjet\Resources;
    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
				$name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $cont_subject = trim($_POST["subject"]);
        $message = trim($_POST["message"]);

        // Check that data was sent to the mailer.
        if ( empty($name) OR empty($cont_subject) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Oops! Un problème est survenu lors de l'envoie, veuillez réessayer plus.";
            exit;
        }

        // Set the recipient email address.
        // FIXME: Update this to your desired email address.
        $recipient = "kadeyoadje@gmail.com";

        // Set the email subject.
        $subject = "New contact from $name";

        // Build the email content.
        $email_content = "Name: $name\n";
        $email_content .= "Email: $email\n\n";
        $email_content .= "Subject: $cont_subject\n\n";
        $email_content .= "Message:\n$message\n";

        // Build the email headers.
        $email_headers = "From: $name <$email>";

        // Send the email.
        if (mail($recipient, $subject, $email_content, $email_headers)) {
            // Set a 200 (okay) response code.
            // http_response_code(200);
            $apikey = '5605cc70b2429f8a3f12b4e6a8a202f9';
            $apisecret = 'c53a32dae6a6bfb622b0db24bdcdffd1';
            $mj = new \Mailjet\Client($apikey, $apisecret);
            $body = [
                'FromEmail' => "kadeyoadje@gmail.com",
                'FromName' => "Mailjet Pilot",
                'Subject' => "Your email flight plan!",
                'Text-part' => "Dear passenger, welcome to Mailjet! May the delivery force be with you!",
                'Html-part' => "<h3>Dear passenger, welcome to Mailjet!</h3><br/>May the delivery force be with you!",
                'Recipients' => [
                    [
                        'Email' => "kadeyo@gmail.com"
                    ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]);
            $response->success() && var_dump($response->getData());
            echo "Merci, Votre message a été envoyé avec succès.";
        } else {
            // Set a 500 (internal server error) response code.
            // http_response_code(500);
            echo "Oops! Veuillez reéssayer plus tard.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        // http_response_code(403);
        echo "Veuillez reéssayer plus tard.";
    }

?>
