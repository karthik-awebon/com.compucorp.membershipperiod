<?php
use CRM_MembershipPeriod_ExtensionUtil as E;
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

 /**
 * Page for displaying list of Membership Periods
 */

class CRM_MembershipPeriod_Page_MembershipPeriod extends CRM_Core_Page {

  /**
   * Called when action is browse.
   *
   * @return null
   */
  public function browse() {
    $membership_period_entries = CRM_MembershipPeriod_BAO_MembershipPeriod::getContactMembershipPeriods($this->_contactId);

    $this->assign('membershipperiodsCount', count($membership_period_entries));
    $this->ajaxResponse['tabCount'] = count($membership_period_entries);
    $this->ajaxResponse += CRM_Contact_Form_Inline::renderFooter($this->_contactId, FALSE);
    $this->assign_by_ref('membershipperiods', $membership_period_entries);
  }

  public function preProcess() {
    if(CIVICRM_UF === 'UnitTests'){
      $this->_contactId = 153;
    }else{
      $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE,170);
    }
    $this->assign('contactId', $this->_contactId);

    // check logged in url permission
    CRM_Contact_Page_View::checkUserPermission($this);

    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, FALSE, 'browse');
    $this->assign('action', $this->_action);
  }

  public function run() {
    $this->preProcess();
    $this->browse();
    return parent::run();
  }

}
