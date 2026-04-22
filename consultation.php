<?php
include "db.php";

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM consultations WHERE id='$id'");
$row = mysqli_fetch_assoc($result);
?>

<h2>Consultation</h2>

<p>Status: <?php echo $row['status']; ?></p>

<?php if($row['status'] == 'accepted'){ ?>
    <a href="start_consultation.php?id=<?php echo $id; ?>">
        <button>Start Consultation</button>
    </a>
<?php } ?>

<?php if($row['status'] == 'ongoing'){ ?>
    <a href="end_consultation.php?id=<?php echo $id; ?>">
        <button>End Consultation</button>
    </a>
<?php } ?>

<div style="width:400px;height:250px;background:#ccc;margin-top:20px;">
    <p style="text-align:center;padding-top:100px;">Video Area</p>
</div>
<iframe 
  src="https://meet.jit.si/consultation_<?php echo $id; ?>" 
  width="600" 
  height="400" 
  allow="camera; microphone">
</iframe>