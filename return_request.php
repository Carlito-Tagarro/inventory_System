<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'trainer') {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$conn = CONNECTIVITY();

// Fetch events with provided materials by this user
$events = [];
$res = $conn->query("
    SELECT efh.event_form_id, efh.event_name, efh.event_date, efh.request_mats
    FROM event_form_history efh
    WHERE efh.user_id = $user_id
      AND efh.provide_materials = 'Yes'
      AND NOT EXISTS (
          SELECT 1 FROM material_return_request mrr
          WHERE mrr.event_id = efh.event_form_id
            AND mrr.user_id = $user_id
            AND mrr.status = 'Approved'
      )
    ORDER BY efh.event_date DESC
");
while ($row = $res->fetch_assoc()) {
    $events[] = $row;
}

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id']);
    $return_items = json_decode($_POST['return_items'], true);

    // Insert return request
    $stmt = $conn->prepare("INSERT INTO material_return_request (event_id, user_id, items_json, status, requested_at) VALUES (?, ?, ?, 'Pending', NOW())");
    $items_json = json_encode($return_items);
    $stmt->bind_param("iis", $event_id, $user_id, $items_json);
    $stmt->execute();
    $stmt->close();

    $_SESSION['return_submit_status'] = "success";
    // Redirect back to avoid blank page and resubmission
    header("Location: return_request.php");
    DISCONNECTIVITY($conn);
    exit;
}
DISCONNECTIVITY($conn);
?>
<html>
<head>
    <title>Return Unused Materials</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        h2 { text-align: center; }
        label { font-weight: bold; }
        select, input[type="number"] { padding: 6px; border-radius: 4px; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #e3e3e3; padding: 8px; }
        th { background: #f0f8ff; color: #007bff; }
        button { background: #007bff; color: #fff; border: none; border-radius: 4px; padding: 8px 24px; font-size: 15px; cursor: pointer; margin-top: 18px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h2>Return Unused Materials</h2>
    <form method="post" id="returnForm">
        <label for="event_id">Select Event:</label>
        <select name="event_id" id="event_id" required>
            <option value="">--Select Event--</option>
            <?php foreach ($events as $ev): ?>
                <option value="<?= $ev['event_form_id'] ?>" data-request-mats="<?= $ev['request_mats'] ?>">
                    <?= htmlspecialchars($ev['event_name']) ?> (<?= $ev['event_date'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <div id="materialsTableContainer"></div>
        <input type="hidden" name="return_items" id="return_items_input">
        <button type="submit">Submit Return Request</button>
    </form>
</div>
<script>
document.getElementById('event_id').addEventListener('change', function() {
    var eventId = this.value;
    var requestMats = this.selectedOptions[0].getAttribute('data-request-mats');
    var container = document.getElementById('materialsTableContainer');
    container.innerHTML = '';
    if (!eventId || !requestMats) return;

    // Fetch materials for this event via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'return_materials_fetch.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var items = JSON.parse(xhr.responseText);
            if (items.length === 0) {
                container.innerHTML = '<p>No materials found for this event.</p>';
                return;
            }
            var html = '<table><thead><tr><th>Type</th><th>Name</th><th>Provided Qty</th><th>Return Qty</th></tr></thead><tbody>';
            items.forEach(function(item, idx) {
                html += '<tr>' +
                    '<td>' + item.type + '</td>' +
                    '<td>' + item.name + '</td>' +
                    '<td>' + item.qty + '</td>' +
                    '<td><input type="number" min="0" max="' + item.qty + '" value="0" data-idx="' + idx + '" style="width:60px;" oninput="if(this.value > ' + item.qty + ') this.value=' + item.qty + '; if(this.value < 0) this.value=0;"></td>' +
                '</tr>';
            });
            html += '</tbody></table>';
            container.innerHTML = html;

            // Additional validation: prevent non-integer and exceeding provided qty
            var inputs = container.querySelectorAll('input[type="number"]');
            inputs.forEach(function(input, idx) {
                var max = parseInt(input.getAttribute('max'), 10);
                input.addEventListener('input', function() {
                    var val = parseInt(input.value, 10);
                    if (isNaN(val) || val < 0) {
                        input.value = 0;
                    } else if (val > max) {
                        input.value = max;
                    }
                });
            });

            // On submit, collect return quantities
            document.getElementById('returnForm').onsubmit = function(e) {
                var returnItems = [];
                var valid = true;
                var inputs = container.querySelectorAll('input[type="number"]');
                items.forEach(function(item, idx) {
                    var input = container.querySelector('input[data-idx="' + idx + '"]');
                    var val = parseInt(input.value, 10);
                    var max = parseInt(input.getAttribute('max'), 10);
                    if (val > max) {
                        input.value = max;
                        valid = false;
                    }
                    if (val > 0) {
                        returnItems.push({type: item.type, name: item.name, qty: val});
                    }
                });
                document.getElementById('return_items_input').value = JSON.stringify(returnItems);
                if (!valid) {
                    alert('Return quantity cannot exceed provided quantity.');
                    e.preventDefault();
                    return;
                }
                if (returnItems.length === 0) {
                    alert('Please specify at least one item to return.');
                    e.preventDefault();
                }
            };
        }
    };
    xhr.send('request_mats=' + encodeURIComponent(requestMats));
});

    // Show JS message after form submit (PHP sets JS variable)
    <?php if (isset($_SESSION['return_submit_status'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($_SESSION['return_submit_status'] === "success"): ?>
                alert("Return request submitted!");
                document.getElementById('returnForm').reset();
                document.getElementById('materialsTableContainer').innerHTML = '';
            <?php endif; ?>
        });
    <?php
        // Unset after showing
        unset($_SESSION['return_submit_status']);
    endif;
    ?>
</script>
</body>
</html>
