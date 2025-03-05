--
-- Table structure for table `dbProgramReviewForm`
--

DROP TABLE IF EXISTS `dbProgramReviewForm`;

CREATE TABLE `dbProgramReviewForm`(
`id` INT AUTO_INCREMENT PRIMARY KEY,
`family_id` INT NOT NULL,
`reviewText` VARCHAR(1000)
)