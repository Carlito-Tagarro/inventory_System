<?php 
include '../connection.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

$connection = CONNECTIVITY();

// Helper function to fetch requested materials
function fetch_requested_materials($connection, $mat_id) {
    $materials = [];
    if ($mat_id) {
        $stmt = mysqli_prepare($connection, "SELECT * FROM material_request_form WHERE request_mats = ?");
        mysqli_stmt_bind_param($stmt, "i", $mat_id);
        mysqli_stmt_execute($stmt);
        $mat_result = mysqli_stmt_get_result($stmt);
        while ($mat_row = mysqli_fetch_assoc($mat_result)) {
            $materials[] = [
                'Brochure' => [
                    'name' => $mat_row['name_brochures'],
                    'qty' => $mat_row['brochure_quantity']
                ],
                'Swag' => [
                    'name' => $mat_row['name_swag'],
                    'qty' => $mat_row['swag_quantity']
                ],
                'Marketing Material' => [
                    'name' => $mat_row['name_material'],
                    'qty' => $mat_row['material_quantity']
                ]
            ];
        }
        mysqli_stmt_close($stmt);
    }
    return $materials;
}

// Fetch event requests with sender email and requested materials
$query = "SELECT event_form.*, users.email AS sender_email 
          FROM event_form 
          LEFT JOIN users ON event_form.user_id = users.user_id";
$result = mysqli_query($connection, $query);

$event_requests = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['requested_materials'] = fetch_requested_materials($connection, intval($row['request_mats']));
    $event_requests[] = $row;
}

// Pagination for history
$history_per_page = 10;
$history_page = isset($_GET['history_page']) ? max(1, intval($_GET['history_page'])) : 1;
$history_offset = ($history_page - 1) * $history_per_page;

// Get total count for pagination
$total_history_query = "SELECT COUNT(*) as cnt FROM event_form_history";
$total_history_result = mysqli_query($connection, $total_history_query);
$total_history_row = mysqli_fetch_assoc($total_history_result);
$total_history_count = intval($total_history_row['cnt']);
$total_history_pages = ceil($total_history_count / $history_per_page);

// Fetch paginated history requests
$history_query = "SELECT h.event_form_id, h.event_name, h.event_title, h.event_date, 
    COALESCE(h.sender_email, u.email) AS sender_email, 
    h.date_time_ingress, h.date_time_egress, h.place, h.location, h.sponsorship_budg, 
    h.target_audience, h.number_audience, h.set_up, h.booth_size, h.booth_inclusion, 
    h.number_tables, h.number_chairs, h.speaking_slot, h.date_time, h.topic, 
    h.technical_team, h.trainer_needed, h.trainer_task, h.provide_materials, 
    h.created_at, h.user_id, h.request_mats, h.request_status, h.processed_at,
    h.event_duration,h.claiming_id,h.amount, h.duration, h.speaker_name, h.technical_task,
    h.requested_by
    FROM event_form_history h
    LEFT JOIN users u ON h.user_id = u.user_id
    ORDER BY h.processed_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($connection, $history_query);
mysqli_stmt_bind_param($stmt, "ii", $history_per_page, $history_offset);
mysqli_stmt_execute($stmt);
$history_result = mysqli_stmt_get_result($stmt);

$history_requests = [];
while ($row = mysqli_fetch_assoc($history_result)) {
    $row['requested_materials'] = fetch_requested_materials($connection, intval($row['request_mats']));
    $history_requests[] = $row;
}
mysqli_stmt_close($stmt);

DISCONNECTIVITY($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../images/images__1_-removebg-preview.png"> 
    <title>Requests</title>
    <link rel="stylesheet" href="../CSS/admin_request.css">
    
</head>
<body>
    <nav>
        <a href="admin.php"><img src="../images/images__1_-removebg-preview.png" alt="Company Logo"></a>
        <div class="nav-container">
            <a href="admin.php">Dashboard</a>
            <a href="brochure_inventory.php">Brochure Inventory</a>
            <a href="marketing_inventory.php">Materials Inventory</a>
            <a href="swags_inventory.php">Swags Inventory</a>
        </div>
    </nav>
      <h4>Note: Click <span style="color:#b91c1c;"> View Details </span>in the History Table to Download the PDF Format of the Request Form</h4>
    <div class="tables-flex">
        <div class="table-container">
            <h2>Event Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Sender Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($event_requests as $row): ?>
                    <?php if($row['request_status'] === 'Pending'): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event_name']); ?></td>
                        <td><?= htmlspecialchars($row['event_date']); ?></td>
                        <td><?= htmlspecialchars($row['sender_email']); ?></td>
                        <td id="status-<?= $row['event_form_id']; ?>">
                            <?= htmlspecialchars($row['request_status']); ?>
                        </td>
                        <td>
                            <button 
                                class="view-request"
                                data-request='<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                                data-materials='<?= json_encode($row['requested_materials'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                            >View Details</button>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="table-container">
            <h2>Processed Requests History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th>Processed At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($history_requests as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event_name']); ?></td>
                        <td><?= htmlspecialchars($row['event_date']); ?></td>
                        <td><?= htmlspecialchars($row['request_status']); ?></td>
                        <td><?= htmlspecialchars($row['processed_at']); ?></td>
                        <td>
                            <button 
                                class="view-request"
                                data-request='<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                                data-materials='<?= json_encode($row['requested_materials'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                            >View Details</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($total_history_pages > 1): ?>
            <div style="margin-top:24px; display:flex; flex-direction:column; align-items:center;">
                <div style="display:flex; gap:12px; align-items:center;">
                    <?php if ($history_page > 1): ?>
                        <a href="?history_page=<?= $history_page-1; ?>">
                            <button style="min-width:90px; font-size:16px; padding:10px 24px;">&#8592; Previous</button>
                        </a>
                    <?php endif; ?>
                    <span style="
                        background: #f0f4ff;
                        color: #2563eb;
                        border-radius: 6px;
                        padding: 8px 18px;
                        font-size: 16px;
                        font-weight: 600;
                        margin: 0 8px;
                        box-shadow: 0 1px 4px rgba(34,34,59,0.07);
                        letter-spacing: 0.5px;
                    ">
                        Page <?= $history_page; ?> of <?= $total_history_pages; ?>
                    </span>
                    <?php if ($history_page < $total_history_pages): ?>
                        <a href="?history_page=<?= $history_page+1; ?>">
                            <button style="min-width:90px; font-size:16px; padding:10px 24px;">Next &#8594;</button>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <div style="position: absolute; top: 18px; right: 24px;">
                <button class="close" onclick="closeModal()" style="
                    background: none;
                    border: none;
                    font-size: 28px;
                    color: #2563eb;
                    cursor: pointer;
                    font-weight: bold;
                    transition: color 0.2s;
                " title="Close">&times;</button>
            </div>
            <div style="width:100%;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <!-- <img src="images/AUDENTES LOGO.png" alt="Event" style="height:60px; width: 60px;"> -->
                </div>
                <div class="modal-divider"></div>
                <div style="display: flex; flex-wrap: wrap; gap: 32px;">
                    <div class="modal-left" id="modalLeft" style="flex:1 1 320px; min-width:260px;"></div>
                    <div class="modal-right" id="modalRight" style="flex:1 1 320px; min-width:260px;"></div>
                </div>
                <div class="modal-divider"></div>
                <div id="modalActions" style="margin-top: 18px; text-align: right;"></div>
            </div>
        </div>
    </div>
    <script src="../JavaScripts/admin_request.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>