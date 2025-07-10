<?php
require 'header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require '../config.php';

// Get filters from request
$format = isset($_GET['format']) ? $_GET['format'] : 'excel';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query based on the status filter and search
$query = "SELECT applications.*, users.full_name, users.email FROM applications
          JOIN users ON applications.user_id = users.id";

// Add WHERE clause based on filters
$whereConditions = [];
$params = [];

// Status filter
if ($statusFilter !== 'all') {
    $whereConditions[] = "applications.status = :status";
    $params[':status'] = $statusFilter;
}

// Search filter
if (!empty($searchQuery)) {
    $whereConditions[] = "(users.full_name LIKE :search OR users.email LIKE :search OR applications.ref_code LIKE :search OR applications.study_title LIKE :search)";
    $params[':search'] = "%$searchQuery%";
}

// Combine WHERE conditions if any
if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

// Add order by
$query .= " ORDER BY applications.submitted_at DESC";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$applications = $stmt->fetchAll();

// Function to convert status to a more readable format
function formatStatus($status) {
    if (empty($status)) return 'Incomplete';
    
    return ucfirst(str_replace('_', ' ', $status));
}

// Function to get a readable date
function formatDate($date) {
    if (empty($date)) return 'N/A';
    
    return date('Y-m-d H:i:s', strtotime($date));
}

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="research_applications_export_' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');

// Excel content
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Research Applications</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        td, th {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            padding: 3px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Ref Code</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Study Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Step Completed</th>
                <th>Submitted At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $app): ?>
            <tr>
                <td><?= htmlspecialchars($app['ref_code']) ?></td>
                <td><?= htmlspecialchars($app['full_name']) ?></td>
                <td><?= htmlspecialchars($app['email']) ?></td>
                <td><?= htmlspecialchars($app['study_title'] ?: 'Not specified') ?></td>
                <td><?= htmlspecialchars(ucfirst($app['research_category'] ?: 'Not specified')) ?></td>
                <td><?= formatStatus($app['status']) ?></td>
                <td class="text-center"><?= $app['step_completed'] ?: '0' ?> / 7</td>
                <td><?= formatDate($app['submitted_at']) ?></td>
                <td><?= formatDate($app['updated_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
