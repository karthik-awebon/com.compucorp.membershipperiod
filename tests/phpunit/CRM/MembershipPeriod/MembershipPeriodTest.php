<?php

use CRM_MembershipPeriod_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * These test cases are fore MembershipPeriod Extension
 *
 *
 * @group headless
 */
class CRM_MembershipPeriod_MembershipPeriodTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testPageOutput() {
    ob_start();
    $p = new CRM_MembershipPeriod_Page_MembershipPeriod();
    $p->run();
    $content = ob_get_contents();
    ob_end_clean();
    $this->assertRegExp(';id="membershipPeriod";', $content);
  }

  public function testMembershipAdd() {
  	$test_data = [];
  	$test_data['membership_type_id'] = 2;
  	$test_data['contact_id'] = 1;
  	$test_data['total_amount'] = '50.00'; 
  	$test_data['join_date'] = '20171221000000';
  	$test_data['start_date'] = '20171221000000';
  	$test_data['end_date'] = '20181221000000';	
  	$test_id = [];
  	$dao_object = CRM_Member_BAO_Membership::create($test_data,$test_id);
	$this->assertInstanceOf('CRM_Member_DAO_Membership', $dao_object);
  }
  public function testMembershipAddWithContribution() {
  	$test_data = [];
  	$test_data['membership_type_id'] = 2;
  	$test_data['contact_id'] = 1;
  	$test_data['join_date'] = '20171221000000';
  	$test_data['start_date'] = '20171221000000';
  	$test_data['end_date'] = '20181221000000';    
  	$test_data['total_amount'] = '50.00';
  	$test_data['financial_type_id'] = 2;
  	$test_data['payment_instrument_id'] = 4;
  	$test_data['contribution_status_id'] = 1;
  	$test_data['receive_date'] = '20171221191700';
  	$test_data['contribution_source'] = 'Student Membership: Offline signup (by jkarthionline@gmail.com)' ;	
  	$test_data['action'] = 1;
  	$test_data['processPriceSet'] = 1;
  	$test_id = [];
  	$dao_object = CRM_Member_BAO_Membership::create($test_data,$test_id);
	$this->assertInstanceOf('CRM_Member_DAO_Membership', $dao_object);
  }

  public function testMembershipRenewal() {
  	$test_data = [];
  	$test_data['join_date'] = '20171214';
  	$test_data['start_date'] = '20171214';
  	$test_data['end_date'] = '20211213';
  	$test_data['membership_type_id'] = 2;
  	$test_data['log_start_date'] = '20191214';
  	$test_data['membership_activity_status'] = 'Completed';
  	$test_id = [];
  	$test_id['membership'] = 1;
  	$test_id['userId'] = 1;
  	$dao_object = CRM_Member_BAO_Membership::create($test_data,$test_id);
	$this->assertInstanceOf('CRM_Member_DAO_Membership', $dao_object);
  }

	function hook_civicrm_pre($op, $objectName, $id, &$params) {
	    if($op === 'edit' && $objectName === 'Membership'){ // While Renewing the Membership the end date of the Membership is the start date of Membership Period. So saving the end date in session and using on postSave hook of Membership.
	      $session = CRM_Core_Session::singleton();
	      $session->set('membershipperiod_membership_period_saved', NULL);
	      $session->set('membershipperiod_id', NULL);      
	      $session->set('membershipperiod_end_date', CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $id, 'end_date'));
	    } 
	}

	function hook_civicrm_postSave_civicrm_membership($dao) {
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

	function hook_civicrm_postSave_civicrm_membership_payment($dao) {
	  $session = CRM_Core_Session::singleton();
	  if($session->get('membershipperiod_membership_period_saved') === 1){ 
	        $membershipPeriod = new CRM_MembershipPeriod_DAO_MembershipPeriod();
	        $membershipPeriod->contribution_id = $dao->contribution_id;
	        $membershipPeriod->id = $session->get('membershipperiod_id');
	        $membershipPeriod->save();
	        $membershipPeriod->free();
	  }
	  $session->set('membershipperiod_membership_period_saved', NULL);
	  $session->set('membershipperiod_id', NULL);
	}	 
}
