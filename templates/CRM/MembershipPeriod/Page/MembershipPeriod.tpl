{*
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
*}
<div id="membershipPeriod" class="view-content">
    <div class="form-item">
     {if $membershipperiodsCount > 0 }
       <table>
       <tr class="columnheader"><th>{ts}Start Date{/ts}</th><th>{ts}End Date{/ts}</th><th>{ts}Membership{/ts}</th><th>{ts}Contribution{/ts}</th></tr>
       {foreach from=$membershipperiods item=row}
         <tr class="{cycle values="odd-row,even-row"}">
            <td>{$row.start_date|crmDate}</td>
            <td>{$row.end_date|crmDate}</td>
            <td>{$row.membership_type}</td>
            <td> 
              <a href="{crmURL p='civicrm/contact/view/contribution'
              q="reset=1&id=`$row.contribution_id`&cid=`$contactId`&action=view&context=contribution&selectedChild=contribute"}">{$row.contribution_id}</a>
            </td>
         </tr>
       {/foreach}
       </table>
     {else}
     <div class="messages status no-popup">
      <div class="icon inform-icon"></div> &nbsp;
      {ts}None found.{/ts}
     </div>
     {/if}
    </div>
 </p>
</div>
{literal}
  <script type="text/javascript">
  CRM.$(function($) {
    $('#membershipPeriod .instance_data').on('crmLoad', function(e, data) {
      CRM.tabHeader.updateCount('#tab_log', data.totalRows);
    });
    CRM.reloadMembershipPeriodTab = function(url) {
      if (url) {
        $('#membershipPeriod .instance_data').crmSnippet({url: url});
      }
      $('#membershipPeriod .instance_data').crmSnippet('refresh');
    };
    CRM.reloadMembershipPeriodTab({/literal}"{$instanceUrl}"{literal});
  });

  </script>
{/literal}