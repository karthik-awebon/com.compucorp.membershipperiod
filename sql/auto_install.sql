-- /*******************************************************
-- *
-- * civicrm_membership_period
-- *
-- * Membership Period data.
-- *
-- *******************************************************/
CREATE TABLE IF NOT EXISTS `civicrm_membership_period` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL COMMENT 'Membership Period start date',
  `end_date` date NOT NULL COMMENT 'Membership Period end date',
  `membership_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to Membership',
  `contribution_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Conditional foreign key to civicrm_contribution id.',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_civicrm_membership_period_contribution_recur_id` FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_civicrm_membership_period_membership_id` FOREIGN KEY (`membership_id`) REFERENCES `civicrm_membership` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;