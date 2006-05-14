{:show_header}
{:show_toolbar}
{table_box}
  <tr>
    <th>Personnel</th> 
    <th class="right" colspan="9">
      &nbsp;&nbsp;<a href={url_alloc_personSkillMatrix}>Full Skill Matrix</a>
      &nbsp;&nbsp;<a href={url_alloc_personGraphs}>Person Graphs</a></td>
      {personAddSkill_link}
      &nbsp;&nbsp;<a href="{url_alloc_person}">New Person</a>
    </th>
  </tr>
  <tr>
    <td colspan="9" align="center">
      <form action="{url_alloc_personList}" method="post">
      <table class="filter" align="center">
        <tr>
          <td><select name="skill_class">{skill_classes}</select></td>
          <td><select name="skill">{skills}</select></td>
          <td><select name="expertise">{employee_expertise}</select></td>
          <td><input type="checkbox" name="show_skills" {show_skills_checked}>Show Skills List</td>
          <td><input type="submit" value="Filter"></td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <!-- <th>Select</th> -->
    <td>Name</td>
    <td>Enabled</td>
    <td>Last Login</td>
    <td>Availability</td>
    <td>Actions</th>
    <td><nobr>On Leave</nobr></td>
    <td>Sum Prev Fort.</td>
    <td>Avg Per Fort.</td>
{optional:show_skills_list}
    <td>
      Senior
      <img src="../images/skill_senior.png" alt="S" align="absmiddle">
      <img src="../images/skill_advanced.png" alt="A" align="absmiddle">
      <img src="../images/skill_intermediate.png" alt="I" align="absmiddle">
      <img src="../images/skill_junior.png" alt="J" align="absmiddle">
      <img src="../images/skill_novice.png" alt="N" align="absmiddle"> Novice
    </td>
{/optional}

  </tr>
  {:show_people templates/personListR.tpl}
</table>
{:show_footer}
