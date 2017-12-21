<?php
use CRM_MembershipPeriods_ExtensionUtil as E;
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 * @package MembershipPeriod
 */

class CRM_MembershipPeriod_BAO_MembershipPeriod extends CRM_MembershipPeriod_DAO_MembershipPeriod {

  /**
   * class constructor
   */
  function __construct() {
    parent::__construct();
  }

  /**
   * Create a new MembershipPeriod based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_MembershipPeriod_DAO_MembershipPeriod|NULL
   *
   */
  public static function create($params) {
    $className = 'CRM_MembershipPeriod_DAO_MembershipPeriod';
    $entityName = 'MembershipPeriod';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    if (!$instance->find(TRUE)) {
      $instance->save();
    }
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);
    return $instance;
  }

  /**
   * Takes contactID as input and returs the list of membership periods related to the contactID through Membership Id 
   * and Membership Type Id
   * @param int $contactID ID of the Contact whose membership periods are needed.
   *
   * @return Array of objects which has membership period info on success, empty array otherwise, Each object in the array has Start Date, End Date, Contribution ID and Membership Type Name
   * @access public
   * @static
   */

  static function getContactMembershipPeriods($contactID) {

    $select = "SELECT civicrm_membership_period.id,civicrm_membership_period.start_date,civicrm_membership_period.end_date,civicrm_membership_period.contribution_id,civicrm_membership_type.name as name FROM civicrm_membership_period ";
    $where = "WHERE civicrm_membership.contact_id = {$contactID} AND civicrm_membership.is_test = 0 ";
    $select .= " INNER JOIN civicrm_membership ON civicrm_membership_period.membership_id = civicrm_membership.id ";
    $select .= " INNER JOIN civicrm_membership_type ON civicrm_membership.membership_type_id = civicrm_membership_type.id ";
    $query = $select . $where;

    $membership_period_entries = array();

    $dao =& CRM_Core_DAO::executeQuery($query, array());

    while ($dao->fetch()) {
      $membership_period_entries[] = array(
        'start_date' => $dao->start_date,
        'end_date' => $dao->end_date,
        'membership_type' => $dao->name,
        'contribution_id' => $dao->contribution_id,
      );
    }

    return $membership_period_entries;
  }

}
