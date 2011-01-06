{page::header()}
{page::toolbar()}
<table class="box">
  <tr>
    <th class="header">Reminders
      <span>
        <a class='magic toggleFilter' href=''>Show Filter</a>
        <a href="{$url_alloc_reminderAdd}">Add Reminder</a>
      </span>
    </th>
  </tr>
  <tr>
    <td align="center">{show_reminder_filter("../reminder/templates/reminderFilter.tpl")}</td>
  </tr>  
  <tr>
    <td>
      <table class="list sortable">
        <tr>
          <th>Recipient</th>
          <th>Date / Time</th>
          <th>Subject</th>
          <th>Repeat</th>
        </tr>
        {show_reminders("../reminder/templates/reminderR.tpl")}
      </table>
    </td>
  </tr>
</table>
{page::footer()}
