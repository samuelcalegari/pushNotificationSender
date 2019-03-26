class pushNotificationSender {

    public function send($tokens, $title, $message, $platform = 'android') {

        if($platform == 'android') {
            
            $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

            $fields = array(
                'registration_ids' => $tokens,
                'priority' => 10,
                'notification' => array('title' => $title, 'body' => $message, 'sound' => 'Default'),
            );
            $headers = array(
                'Authorization:key=' . 'XXXXXXXXX',
                'Content-Type:application/json'
            );

            // Open connection
            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            
            // Execute post
            $result = curl_exec($ch);
            
            // Close connection
            curl_close($ch);
            return $result;
            
        } else {

            $result = true;
            $apnsServer = 'ssl://gateway.sandbox.push.apple.com:2195';
            $privateKeyPassword = 'XXXXXXXXX';
            $pushCertAndKeyPemFile = 'certificat.pem';

            $stream = stream_context_create();

            stream_context_set_option($stream,
                'ssl',
                'passphrase',
                $privateKeyPassword);

            stream_context_set_option($stream,
                'ssl',
                'local_cert',
                $pushCertAndKeyPemFile);

            $connectionTimeout = 20;
            $connectionType = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
            $connection = stream_socket_client($apnsServer,
                $errorNumber,
                $errorString,
                $connectionTimeout,
                $connectionType,
                $stream);

            if (!$connection){
               return false;
            }

            $messageBody['aps'] =array(
                'alert' => array(
                    'title' => $title,
                    'body' => $message,
                ),
                'sound' => 'default'
            );

            $payload = json_encode($messageBody);

            foreach($tokens as $token) {
                $notification = chr(0) .
                    pack('n', 32) .
                    pack('H*', $token) .
                    pack('n', strlen($payload)) .
                    $payload;

                $tmp = fwrite($connection, $notification, strlen($notification));
                $result = $result && $tmp;
            }

            fclose($connection);
            return $result;
        }
    }
}
