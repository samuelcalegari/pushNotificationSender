# pushNotificationSender
Send Notifications to Android and IOS with PHP

# usage :

$pushNotificationSender->send(array_of_tokens,tittle,message,platform);

# example :

$myPushNotificationSender = new pushNotificationSender();

if($myPushNotificationSender->send(array('XXXXXX'),'hello','i am here','ios'))

  echo 'message sent';
  
else

  echo 'Error : message not sent';
