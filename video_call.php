<?php
$room = "consultation_" . $_GET['id'];
?>

<iframe 
  src="https://meet.jit.si/<?php echo $room; ?>"
  width="100%" 
  height="500px"
  allow="camera; microphone; fullscreen">
</iframe>