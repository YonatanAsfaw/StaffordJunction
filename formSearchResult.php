<?php
    // Template for new VMS pages. Base your new page on this one

    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    if (!isset($_SESSION['_id'])) {
        header('Location: login.php');
        die();
    }

    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];

    require_once("database/dbFamily.php");
    require_once("domain/Family.php");
    require_once 'database/dbForms.php';

    $families = find_all_families();

    // Columns we won't show in the table/exported CSV
    $excludedColumns = array("id", "family_id", "securityQuestion", "securityAnswer", "password", "child_id", "form_id");

    $hasSearched = isset($_GET['searchByForm']);
    $selectedFormName = $hasSearched ? $_GET['formName'] : "";

    $noResults = true;
    $searchingByForm = false;
    
    if(isset($_GET['searchByForm'])){

        $searchingByForm = true;
        $familyId = isset($_GET['familyAccount']) ? $_GET['familyAccount'] : null;
       
        $submissions = getFormSubmissions($_GET['formName'], $familyId);
        
        

        error_log("Final submissions in formSearchResult.php: " . json_encode($submissions));

        $noResults = empty($submissions);
        $columnNames = !$noResults ? array_keys($submissions[0]) : [];
    } elseif(isset($_GET['searchByFamily'])){
        $familyId = $_GET['familyAccount']? intval($_GET['familyAccount']) : null;
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
            <?php if(!$noResults && $searchingByForm): ?>
                <button class="button" id="downloadButton">Download Results (.csv)</button>
            <?php endif; ?>
            <span style="margin-left: 10px;">Viewing results for: 
            <?php 
                if ($searchingByForm) {
                    echo htmlspecialchars($_GET['formName'] ?? '', ENT_QUOTES, 'UTF-8') . ' Form';
                } else {
                    echo !empty($family) 
                        ? htmlspecialchars($family->getFirstName() . " " . $family->getLastName(), ENT_QUOTES, 'UTF-8') 
                        : "Unknown Family";
                }
            ?>
            </span>
        </form>

        <?php if(!$noResults): ?>
            <script>
                const resultsData = <?php echo json_encode($submissions); ?>;
                const columns = <?php echo json_encode($columnNames); ?>;
                const excludedColumns = <?php echo json_encode($excludedColumns); ?>;
                
                const csvHeaderRow = columns.filter(col => !excludedColumns.includes(col));
                const rows = [csvHeaderRow];

                resultsData.forEach(result => {
                    excludedColumns.forEach(col => delete result[col]);
                    rows.push(Object.values(result));
                });

                document.getElementById("downloadButton").addEventListener("click", (e) => {
                    let csvContent = "data:text/csv;charset=utf-8,";
                    rows.forEach(row => csvContent += row.join(",") + "\r\n");
                    window.open(encodeURI(csvContent));
                });
            </script>
        <?php endif; ?>

        <div class="form-search-results">
            <?php if(!$noResults): ?>
                <table class="general form-search-results-table">
                <?php if($searchingByForm): ?>
                    <thead>
                        <tr>
                            <?php
                                foreach($columnNames as $columnName){
                                    if(!in_array($columnName, $excludedColumns)){
                                        echo '<th>' . htmlspecialchars($columnName ?? '', ENT_QUOTES, 'UTF-8') . '</th>';
                                    }
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody class="standout">
                        <?php
                            foreach($submissions as $submission){
                                echo '<tr>';
                                foreach($submission as $columnName => $column){
                                    if(!in_array($columnName, $excludedColumns)){
                                        echo "<td>" . htmlspecialchars($column ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                                    }
                                }
                                echo '<td><a href="editForm.php?formName='.htmlspecialchars($_GET['formName'] ?? '', ENT_QUOTES, 'UTF-8').'&id='.htmlspecialchars($submission['id'] ?? '', ENT_QUOTES, 'UTF-8').'" class="button">Edit</a></td>';
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                <?php else: ?>
                    <thead>
                        <tr>
                            <th>Form Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="standout">
                        <?php
                            foreach($formNames as $formName){
                                echo '<tr>';
                                echo "<td>" . htmlspecialchars($formName ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                                echo "<td><a href='./formSearchResult.php?searchByForm=searchByForm&formName=" . urlencode($formName) . "&familyAccount=" . urlencode($familyId) . "'>View Submissions</a></td>"; 
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                <?php endif; ?>
                </table>
            <?php else: ?>
                <p style="text-align: center;">No results found.</p>
            <?php endif; ?>
        </div>
    </body>
</html>
