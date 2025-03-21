<?php
session_cache_expire(30);
session_start();
ini_set("display_errors",1);
error_reporting(E_ALL);

if (!isset($_SESSION['_id'])) {
    header('Location: login.php');
    die();
}

require_once("database/dbForms.php");
require_once("database/dbChildCareWaiverForm.php");
require_once("database/dbFamily.php");
require_once("domain/Family.php");

$families = find_all_families();
$excludedColumns = array("", "family_id", "securityQuestion", "securityAnswer", "password", "child_id", "form_id");

$hasSearched = isset($_GET['searchByForm']) || isset($_GET['searchByFamily']);
$selectedFormName = $hasSearched ? ($_GET['formName'] ?? '') : '';
$familyId = $_GET['familyAccount'] ?? null;
$noResults = true;
$searchingByForm = false;
$submissions = [];
$columnNames = [];

// Check if searching by form
if (isset($_GET['searchByForm'])) {
    $searchingByForm = true;
    $childName = isset($_GET['childName']) ? trim($_GET['childName']) : '';

    if ($selectedFormName === "Child Care Waiver" && !empty($childName)) {
        // Search for Child Care Waiver forms using Child Name
        $submissions = getChildCareWaiverByChildName($childName);
    } else {
        // Default form search (by form name & optional family ID)
        sleep(1); // Small delay to allow database update to process
        clearstatcache(); // Clears any cached results
        $submissions = getFormSubmissions($selectedFormName, $familyId);
    }

    error_log("Fetching form: " . $selectedFormName . " | Family ID: " . $familyId);
    error_log("Database Query Result: " . json_encode($submissions));

    $noResults = empty($submissions);
    $columnNames = !$noResults ? array_keys($submissions[0]) : [];
} elseif (isset($_GET['searchByFamily'])) {
    $familyId = intval($_GET['familyAccount'] ?? 0);
    error_log("DEBUG: Using Family ID: " . ($familyId ?: "NULL"));
    $family = retrieve_family_by_id($familyId);
    $formNames = getFormsByFamily($familyId);
    $noResults = empty($formNames);
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>Stafford Junction | View Form Submissions</title>
</head>
<body>
    <?php require_once('header.php') ?>
    <form class="form-search-result-subheader" method="post">
        <a class="button cancel" href="formSearch.php">Back to Search</a>
        <?php if (!$noResults && $searchingByForm): ?>
            <button class="button" id="downloadButton">Download Results (.csv)</button>
        <?php endif; ?>
        <span style="margin-left: 10px;">Viewing results for: 
        <?php 
            echo htmlspecialchars($selectedFormName ?? '', ENT_QUOTES, 'UTF-8');
        ?>
        </span>
    </form>

    <div class="form-search-results">
        <?php if (!$noResults): ?>
            <table class="general form-search-results-table">
                <thead>
                    <tr>
                        <?php
                            foreach ($columnNames as $columnName) {
                                if (!in_array($columnName, $excludedColumns)) {
                                    echo '<th>' . htmlspecialchars($columnName ?? '', ENT_QUOTES, 'UTF-8') . '</th>';
                                }
                            }
                        ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <?php foreach ($submission as $columnName => $column): ?>
                                <?php if (!in_array($columnName, $excludedColumns)): ?>
                                    <td><?php echo htmlspecialchars($column ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td><a href="editForm.php?formName=<?php echo urlencode($selectedFormName); ?>&id=<?php echo htmlspecialchars($submission['id'], ENT_QUOTES, 'UTF-8'); ?>&familyAccount=<?php echo htmlspecialchars($familyId, ENT_QUOTES, 'UTF-8'); ?>" class="button">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">No results found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
