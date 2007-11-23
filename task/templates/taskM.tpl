{show_header()}
{show_toolbar()}
<script type="text/javascript" language="javascript">

// Make the XML request thing, specify the callback function 
function updateStuffWithAjax() \{
  obj = document.getElementById("taskCommentForm").taskCommentTemplateID;
  id = obj.options[obj.selectedIndex].value;
  url = '{$url_alloc_updateTaskCommentTemplate}taskCommentTemplateID='+id+'&taskID={$task_taskID}'
  makeAjaxRequest(url,'updateTaskCommentTemplate',1)
\}

// Here's the callback function
function updateTaskCommentTemplate(number) \{
  if (http_request[number].readyState == 4) \{
    if (http_request[number].status == 200) \{
      document.getElementById("comment").value = http_request[number].responseText;
    \}
  \}
\}

</script>
<form action="{$url_alloc_task}" method="post">
<input type="hidden" name="taskID" value="{$task_taskID}">
{$table_box}
  <tr>
    <th class="nobr">{$task_taskType} {$taskSelfLink}</th>
    <th class="right nobr" colspan="4"><a href="{$url_alloc_task}taskID={$task_taskID}&view=detail">Edit</a>&nbsp;&nbsp;{$navigation_links}</th>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
  <tr>
    <td valign="top" colspan="3" rowspan="2" style="padding-left:15px;" width="50%">

      <table border="0" cellspacing="0" cellpadding="5" class="panel" width="100%">
        <tr>
          <td>{$project_projectName}{$hierarchy_links}{$priorityLabel}<br/>{$task_taskDescription}</td>
        </tr>
      </table>


    </td>
    <td valign="top" rowspan="2" width="50%">
    
      <table border="0" cellspacing="0" cellpadding="5" class="panel" width="100%">
        <tr> 
          <td>Created By</td>
          <td><b>{$task_createdBy}</b> {$task_dateCreated}</td> 
        </tr>
        <tr> 
          <td>Managed By</td>
          <td><b>{$manager_username}</b> </td> 
        </tr>
        <tr>
          <td>Assigned To</td> 
          <td><b>{$person_username}</b> {$task_dateAssigned}</td>
        </tr>
        {$task_closed_info}
        <tr>
          <td valign="top">{get_expand_link("interested_parties_dropdown","Interested Parties ","interested_parties_text")}</td>
          <td valign="top"><div id="interested_parties_text">{$taskCCList_text}</div>
            <div style="display:none;" id="interested_parties_dropdown" class="nobr">
              {$taskCCListOptions}{get_help("task_interested_parties")}
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr> 
        <tr>
          <td>Estimated Hours</td>
          <td><input type="text" name="timeEstimate" value="{$task_timeEstimate}" size="5">
            &nbsp;&nbsp;{$time_billed_link}
          </td>
        </tr>  
        <tr>
          <td>Percent</td>
          <td>
            {$percentComplete}
          </td>
        </tr>
        <tr>
          <td><nobr>Target Start/Completion</nobr></td>
          <td>
            <nobr>
              {get_calendar("dateTargetStart",$TPL["task_dateTargetStart"])}
              {get_calendar("dateTargetCompletion",$TPL["task_dateTargetCompletion"])}
            </nobr>
          </td>
        </tr>
        <tr>
          <td>Actual Start/Completion</td>
          <td>
            <nobr>
              {get_calendar("dateActualStart",$TPL["task_dateActualStart"])}
              {get_calendar("dateActualCompletion",$TPL["task_dateActualCompletion"])}
            </nobr>
          </td>
        </tr>
      </table>

    </td>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
  
  <tr>
    <td colspan="5" align="center">
      {$timeSheet_save}
      
      <input type="submit" name="save" value="&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;">
    <!--  <input type="submit" name="save_and_back" value="Save and Return to Project"> -->
    <!--  <input type="submit" name="save_and_summary" value="Save and Return to Task List"> -->
      <input type="submit" name="save_and_new" value="Save &amp; New">
      <input type="submit" name="delete" value="Delete" onClick="return confirm('Are you sure you want to delete this record?')">
      <input type='hidden' name='view' value='brief'>

    </td>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
</table>


{show_task_children("templates/taskChildrenM.tpl")}

{$table_box}
  <tr>
    <th>Reminders</th>
    <th class="right" colspan="3"><a href="{$url_alloc_reminderAdd}step=3&parentType=task&parentID={$task_taskID}&returnToParent=task">Add Reminder</a></th>
  </tr>
  <tr>
    <td>Recipient</td>
    <td>Date / Time</td>
    <td>Subject</td>
    <td>Repeat</td>
  </tr>
  {show_reminders("../reminder/templates/reminderR.tpl")}
</table>

</form>

{show_attachments()}

{show_taskComments()}

{show_footer()}
