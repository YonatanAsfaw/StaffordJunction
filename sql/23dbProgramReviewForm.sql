--
-- Table structure for table `dbProgramReviewForm`
--

DROP TABLE IF EXISTS `dbProgramReviewForm`;

CREATE TABLE `dbProgramReviewForm`(
`id` INT AUTO_INCREMENT PRIMARY KEY,
`family_id` INT NOT NULL,
`event_name` VARCHAR(100),
`reviewText` VARCHAR(1000)
)