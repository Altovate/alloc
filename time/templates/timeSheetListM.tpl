{show_header()}
{show_toolbar()}
{$table_box}
  <tr>
    <th>Time Sheets</th>
    <th class="right"><a href="{$url_alloc_timeSheet}userID={$userID}">New Time Sheet</a></th>
  </tr>
  <tr>
    <td colspan="2" align="center">
      {show_filter()}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {show_timeSheet_list()}
    </td>
  </tr>
</table>
{show_footer()}
