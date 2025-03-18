<?php

session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once("database/dbForms.php");  // Include database functions

// Fetch published forms
$publishedForms = getPublishedForms();

?>

<!DOCTYPE html>
<html>
    <head>
        <?php require('universal.inc'); ?>
        <title>Stafford Junction | Forms</title>
    </head>
    <body>
        <?php require('header.php'); ?>
        <h1>Forms</h1>
        <?php 
            if (isset($_GET['formSubmitSuccess'])) {
                echo '<div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Form Successfully Submitted!</div>';
            }
   
            // Display success message for form deletion
            if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
                echo '<div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">Form Successfully Deleted!</div>';
            }
    
            // Display error message for form deletion
            if (isset($_GET['status']) && $_GET['status'] === 'error') {
                echo '<div class="happy-toast" style="margin-right: 30rem; margin-left: 30rem; text-align: center;">An error occurred while deleting the form. Please try again.</div>';
            }
        ?>


        <main class='dashboard'>
            <div id="dashboard">
                <!-- Holiday Meal Bag Form -->
                <?php if (in_array("Holiday Meal Bag", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=holidayMealBagForm.php' 
                : 'holidayMealBagForm.php'; ?>">
                <img src="images/holdiayMealBagIcon.svg">
                <span>Holiday Meal Bag Form</span>
                </div>
                <?php endif; ?>

                <!-- School Supplies Form -->
                <?php if (in_array("School Supplies Form", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=SchoolSuppliesForm.php' 
                : 'schoolSuppliesForm.php'; ?>">
                <img src="images/school-supplies-svgrepo-com.svg">
                <span>School Supplies Form</span>
                </div>
                <?php endif; ?>
                
                <!-- Spring Break Form -->
                <?php if (in_array("Spring Break", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=springBreakForm.php' 
                : 'springBreakForm.php'; ?>">
                <img src="images/tent-svgrepo-com.svg">
                <span>Spring Break Form</span>
                </div>
                <?php endif; ?>

                <!-- Angel Gifts Wish List Form -->
                <?php if (in_array("Angel Gifts Wish List", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=angelGiftForm.php' 
                : 'angelGiftForm.php'; ?>">
                <img src="images/angel.svg">
                <span>Angel Gifts Wish Form</span>
                </div>
                <?php endif; ?>

                <!-- Child Care Waiver Form -->
                <?php if (in_array("Child Care Waiver", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=childCareWaiverForm.php' 
                : 'childCareWaiverForm.php'; ?>">
                <img src="images/signature.svg">
                <span>Child Care Waiver Form</span>
                </div>
                <?php endif; ?>

                <!-- Field Trip Waiver Form -->
                <?php if (in_array("Field Trip Waiver", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=fieldTripWaiver.php' 
                : 'fieldTripWaiver.php'; ?>">
                <img src="images/location.svg">
                <span>Field Trip Waiver Form</span>
                </div>
                <?php endif; ?>

                <!-- Program Interest Form -->
                <?php if (in_array("Program Interest", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=programInterestForm.php' 
                : 'programInterestForm.php'; ?>">
                <img src="images/interest.svg">
                <span>Program Interest Form</span>
                </div>
                <?php endif; ?>

                <!-- Program Review Form -->
                <?php if (in_array("Program Review", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=programReviewForm.php' 
                : 'programReviewForm.php'; ?>">
                <img src="images/create-report.svg">
                <span>Program Review Form</span>
                </div>
                <?php endif; ?>

                <!-- Brain Builders Registration Form -->
                <?php if (in_array("Brain Builders Student Registration", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=brainBuildersRegistrationForm.php' 
                : 'brainBuildersRegistrationForm.php'; ?>">
                <img src="images/brainBuilders.svg">
                <span>Brain Builders Student Registration Form</span>
                </div>
                <?php endif; ?>

                <!-- Brain Builders Holiday Party Form -->
                <?php if (in_array("Brain Builders Holiday Party", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=holidayPartyForm.php' 
                : 'holidayPartyForm.php'; ?>">
                <img src="images/party-flyer-svgrepo-com.svg">
                <span>Brain Builders Holiday Party Form</span>
                </div>
                <?php endif; ?>

                <!-- Summer Junction Registration Form -->
                <?php if (in_array("Summer Junction Registration", $publishedForms)): ?>
                <div class="dashboard-item" data-link="<?= ($_SESSION['access_level'] > 1) 
                ? 'selectFamily.php?redirect=summerJunctionRegistrationForm.php' 
                : 'summerJunctionRegistrationForm.php'; ?>">
                <img src="images/summerJunction.svg">
                <span>Summer Junction Registration Form</span>
                </div>
                <?php endif; ?>

                <!--need to also do for actual activity form-->

            </div>

            <!-- Return to dashboard logic -->
            <?php if($_SESSION['access_level'] == 1): ?> 
                <a class="button cancel" href="familyAccountDashboard.php" style="margin-top: 3rem;">Return to Dashboard</a>
            <?php endif ?>
            <?php if($_SESSION['access_level'] > 1): ?>
                <a class="button cancel" href="index.php" style="margin-top: 3rem;">Return to Dashboard</a>
            <?php endif ?>
            <?php if ($_SESSION['access_level'] > 1): ?>
                 <a class="button" href="manageFormPublications.php" style="margin-top: 1rem;">Manage Form Publications</a>
            <?php endif; ?>
        </main>
    </body>
</html>
