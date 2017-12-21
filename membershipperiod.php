<?php

require_once 'membershipperiod.civix.php';
use CRM_MembershipPeriod_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipperiod_civicrm_config(&$config) {
  _membershipperiod_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipperiod_civicrm_xmlMenu(&$files) {
  _membershipperiod_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipperiod_civicrm_install() {
  _membershipperiod_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function membershipperiod_civicrm_postInstall() {
  _membershipperiod_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipperiod_civicrm_uninstall() {
  _membershipperiod_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipperiod_civicrm_enable() {
  _membershipperiod_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipperiod_civicrm_disable() {
  _membershipperiod_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipperiod_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipperiod_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipperiod_civicrm_managed(&$entities) {
  _membershipperiod_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipperiod_civicrm_caseTypes(&$caseTypes) {
  _membershipperiod_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function membershipperiod_civicrm_angularModules(&$angularModules) {
  _membershipperiod_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipperiod_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipperiod_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_pre
 * It stores the end_date of the Membership in session. Which is useful in Calulating the Start date of the Next Membership 
 * Period
 */
function membershipperiod_civicrm_pre($op, $objectName, $id, &$params) {
    if($op === 'edit' && $objectName === 'Membership'){ // While Renewing the Membership the end date of the Membership is the start date of Membership Period. So saving the end date in session and using on postSave hook of Membership.
      $session = CRM_Core_Session::singleton();
      $session->set('membershipperiod_membership_period_saved', NULL);
      $session->set('membershipperiod_id', NULL);      
      $session->set('membershipperiod_end_date', CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $id, 'end_date'));
    } 
}

/**
 * Implementation of hook_civicrm_post for Membership
 * When a Membership is created or updated. A Membership Period will be created with corresponding start date and end date.
 */
function membershipperiod_civicrm_postSave_civicrm_membership($dao) {
    $session = CRM_Core_Session::singleton();
    $membership_period_input = array(
      'end_date' => $dao->end_date,
      'membership_id' => $dao->id,
    );
    if($session->get('membershipperiod_end_date') == NULL){
      $membership_period_input["start_date"] = $dao->start_date;
    }else{
      $date = explode('-', $session->get('membershipperiod_end_date'));
      $period_start_date = date('Y-m-d', mktime(0, 0, 0,
        (double) $date[1],
        (double) ($date[2] + 1),
        (double) $date[0]
      ));
      $membership_period_input["start_date"] = $period_start_date;
    }
  $membership_period = CRM_MembershipPeriod_BAO_MembershipPeriod::create($membership_period_input, CRM_Core_DAO::$_nullArray);
  $session->set('membershipperiod_end_date', NULL);
  $session->set('membershipperiod_membership_period_saved', 1);
  $session->set('membershipperiod_id', $membership_period->id);
}

/**
 * Implementation of hook_civicrm_post for Membership Payment
 * It updates the corresponding contribution id of a Membership Period when an Membership Payment is created for a Membership.
 */

function membershipperiod_civicrm_postSave_civicrm_membership_payment($dao) {
  $session = CRM_Core_Session::singleton();
  if($session->get('membershipperiod_membership_period_saved') === 1){ // If there is any contribution record for Membership updating it in Membership Period Record.
        $membershipPeriod = new CRM_MembershipPeriod_DAO_MembershipPeriod();
        $membershipPeriod->contribution_id = $dao->contribution_id;
        $membershipPeriod->id = $session->get('membershipperiod_id');
        $membershipPeriod->save();
        $membershipPeriod->free();
  }
  $session->set('membershipperiod_membership_period_saved', NULL);
  $session->set('membershipperiod_id', NULL);
}

/**
 * Implementation of hook_civicrm_entityTypes
 */
function membershipperiod_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_MembershipPeriod_DAO_MembershipPeriod'] = array(
    'name' => 'MembershipPeriod',
    'class' => 'CRM_MembershipPeriod_DAO_MembershipPeriod',
    'table' => 'civicrm_membership_period'
  );
}

/**
 * Implementation of hook_civicrm_tabset
 * This hook adds the Membership Peroid Tab to Contact Screen
 */
function membershipperiod_civicrm_tabset($tabsetName, &$tabs, $context) {
  $contact_id = $context['contact_id'];
  if ($tabsetName == 'civicrm/contact/view') {
      $tab['membershipperiod'] = array(
      'title' => ts('Membership Period'),
        'url' => CRM_Utils_System::url('civicrm/membershipperiod','reset=1&cid='.$contact_id),
      );
    $tabs = array_merge($tabs,$tab);
  }
}

