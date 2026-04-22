<?php
include "db.php";

$result = mysqli_query($conn, "SELECT * FROM consultations WHERE status='completed'");
?>

<h2>Consultation History</h2>

<table border="1">
<tr>
    <th>ID</th>
    <th>Doctor</th>
    <th>Patient</th>
    <th>Start Time</th>
    <th>End Time</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['doctor_id']; ?></td>
    <td><?php echo $row['patient_id']; ?></td>
    <td><?php echo $row['start_time']; ?></td>
    <td><?php echo $row['end_time']; ?></td>
</tr>
<?php } ?>

</table>