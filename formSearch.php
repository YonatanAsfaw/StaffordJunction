<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
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
    $families = find_all_families();

    require_once("database/dbForms.php");
    // SEARCHABLE_FORMS
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Stafford Junction | View Form Submissions</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>View Form Submissions</h1>
        <?php require_once('header.php');
        if (isset($_GET['formUpdateSuccess'])) {
            echo '<div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Form Successfully Updated!</div>';
        }
        ?>
        <div class="formSearch">
            <p>Search for a specific form, a specific family, or both</p>
            <form id="formSearch" method="get" action="formSearchResult.php">
                <label for="searchByForm">
                    <input type="checkbox" autocomplete="off" id="searchByForm" name="searchByForm" value="searchByForm"> Form Name
                </label>
                <select id="formName" name="formName" disabled>
                    <option value="">Select a form</option>
                    <?php
                        foreach(SEARCHABLE_FORMS as $form){
                            echo '<option value="'.$form.'">'.$form.'</option>';
                        }
                    ?>
                </select>

                <label for="searchByFamily">
                    <input type="checkbox" autocomplete="off" id="searchByFamily" name="searchByFamily" value="searchByFamily"> Family Account
                </label>
                <select id="familyAccount" name="familyAccount" disabled>
                    <option value="">Select a family</option>
                    <?php
                        foreach($families as $fam){
                            $name = $fam->getFirstName() . " " . $fam->getLastName();
                            echo '<option value="'.$fam->getId().'">'.$name.'</option>';
                        }
                    ?>
                </select>

                <!-- Child Name Search Field (Hidden by Default) -->
                <div id="childNameWrapper" style="display: none;">
                    <label for="childName">Child Name</label>
                    <input type="text" id="childName" name="childName" placeholder="Enter child's name">
                </div>

                <input type="submit" value="Search">
                <a class="button cancel" href="index.php">Return to Dashboard</a>
                <script>
    document.getElementById("formSearch").addEventListener("submit", function (e) {
        const searchByForm = document.getElementById("searchByForm").checked;
        const searchByFamily = document.getElementById("searchByFamily").checked;

        if (!searchByForm && searchByFamily) {
            alert("You must select a form name if you're searching by family.");
            e.preventDefault(); // Stop the form from submitting
        }
    });
</script>

            </form>

            <script>
                document.getElementById("searchByForm").addEventListener("change", (e) => {
                    const selectBox = document.getElementById("formName");
                    selectBox.disabled = !e.currentTarget.checked;
                    if (!e.currentTarget.checked) {
                        selectBox.selectedIndex = 0;
                        document.getElementById("childNameWrapper").style.display = "none";
                    }
                });

                document.getElementById("formName").addEventListener("change", (e) => {
                    const childNameWrapper = document.getElementById("childNameWrapper");
                    if (e.target.value === "Child Care Waiver") {
                        childNameWrapper.style.display = "block";
                    } else {
                        childNameWrapper.style.display = "none";
                    }
                });

                document.getElementById("searchByFamily").addEventListener("change", (e) => {
                    const selectBox = document.getElementById("familyAccount");
                    selectBox.disabled = !e.currentTarget.checked;
                    if (!e.currentTarget.checked) selectBox.selectedIndex = 0;
                });
            </script>
        </div>
    </body>
</html>
