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
            AND mrr.status = 'Pending'
      )
      AND NOT EXISTS (
          SELECT 1 FROM material_return_request mrr2
          WHERE mrr2.event_id = efh.event_form_id
            AND mrr2.user_id = $user_id
            AND mrr2.status = 'Approved'
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
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Return Unused Materials</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --bg: #f7f7f7;
            --white: #fff;
            --shadow: 0 2px 16px rgba(0,0,0,0.08);
            --radius: 12px;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--bg);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 480px;
            margin: 40px auto;
            background: var(--white);
            padding: 32px 24px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        @media (min-width: 600px) {
            .container {
                max-width: 700px;
                padding: 40px 48px;
            }
        }
        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 18px;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #222;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #d0d0d0;
            font-size: 1rem;
            margin-bottom: 12px;
            background: #fafbfc;
            transition: border 0.2s;
        }
        select:focus, input[type="number"]:focus {
            border-color: var(--primary);
            outline: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            background: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #e3e3e3;
            padding: 10px 8px;
            text-align: left;
        }
        th {
            background: #eaf4ff;
            color: var(--primary);
            font-weight: 700;
            font-size: 1rem;
        }
        td input[type="number"] {
            width: 70px;
            margin: 0;
        }
        button[type="submit"] {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 18px;
            box-shadow: 0 2px 8px #cce0ff33;
            transition: background 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        button[type="submit"]:hover {
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            box-shadow: 0 4px 16px #cce0ff55;
        }
        #materialsTableContainer {
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .container {
                padding: 18px 6vw;
            }
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                display: none;
            }
            tr {
                margin-bottom: 15px;
                background: #f7faff;
                border-radius: 8px;
                box-shadow: 0 1px 4px #e3e3e3;
            }
            td {
                border: none;
                position: relative;
                padding-left: 48%;
                min-height: 36px;
            }
            td:before {
                position: absolute;
                left: 12px;
                top: 10px;
                width: 45%;
                white-space: nowrap;
                font-weight: 600;
                color: var(--primary-dark);
            }
            td:nth-child(1):before { content: "Type"; }
            td:nth-child(2):before { content: "Name"; }
            td:nth-child(3):before { content: "Provided Qty"; }
            td:nth-child(4):before { content: "Return Qty"; }
        }
        /* Custom scrollbar for table */
        ::-webkit-scrollbar {
            width: 8px;
            background: #eaf4ff;
        }
        ::-webkit-scrollbar-thumb {
            background: #b3d1ff;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="userpage.php" style="
        display: inline-block;
        margin-bottom: 18px;
        background: #eaf4ff;
        color: var(--primary-dark);
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 1rem;
        font-weight: 500;
        text-decoration: none;
        box-shadow: 0 1px 4px #cce0ff33;
        transition: background 0.2s, color 0.2s;
    " onmouseover="this.style.background='#d0e7ff';this.style.color='#0056b3';" onmouseout="this.style.background='#eaf4ff';this.style.color='var(--primary-dark)';">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
    <h2><i class="fa-solid fa-rotate-left"></i> Return Unused Materials</h2>
    <form method="post" id="returnForm" autocomplete="off">
        <label for="event_id"><i class="fa-solid fa-calendar-days"></i> Select Event:</label>
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
        <button type="submit"><i class="fa-solid fa-paper-plane"></i> Submit Return Request</button>
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
