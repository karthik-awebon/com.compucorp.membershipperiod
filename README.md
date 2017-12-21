Civi CRM Membership Period Extension
==

WHAT IT DOES
--
It creates a membershipship Period record when an Membership is created or updated. The Membership record will have only the start date and end date of a Membership, but it doesn't have the information of how many terms this membership renewed. The Membership Period maintains each term information and also it has contribution information linked to it.

TECHNICAL SOLUTION
--
The start date of a Membership Period is the start date of Membership when a new Membership Record is created and While renewing the membership it is the end date of the membership before it was updated to the database, so using 'hook_civicrm_pre' hook storing the end_date in CRM session and on 'hook_civicrm_postSave_civicrm_membership' using the session variable to calculate start date. Others data like end_date and membership id are in the Membership Dao object.

To update the contribution id of membership period a flag is stored on the session and it is validated on 'hook_civicrm_postSave_civicrm_membership_payment' based on the flag the contribution id is updated for the Membership Period record.

OTHER FEATURES
--
1) Create, Update, Delete, get API's for Membership Period Entity
2) Display the list of Membership Period for a contact on the new contact tab with the link to contribution.