{show_header()}
{show_toolbar()}

<form action="{$url_alloc_invoice}" method="post">
{$table_box}
  <tr>
    <th colspan="4">Invoice</th>
  </tr>  
  <tr>
    <td align="right" width="30%">Client: </td>
    <td class="nobr" class="nobr" width="20%">{$field_clientID}</td>
    <td align="right" class="nobr" width="10%">Total Incoming Funds:</td>
    <td>{$invoiceTotal}</td>
  </tr>
  <tr>
    <td align="right">Invoice Number: </td>
    <td>{$field_invoiceNum}</td>
    <td align="right">Total Amount Paid:</td>
    <td>{$invoiceTotalPaid}</td>
  </tr>
  <tr>
    <td align="right">Invoice Name: </td>
    <td>{$field_invoiceName}</td>
    <td align="right">Period: </td>
    <td>{$invoiceDateFrom} to {$invoiceDateTo}</td>
  </tr>
  <tr>
    <td colspan="4" align="center">
      <input type="hidden" name="invoiceItemID" value="{$invoiceItemID}">
      <input type="hidden" name="mode" value="{$mode}">
      <table width="100%" align="center">
        <tr>
          <td align="center">
            {$invoice_buttons}<br><br>{$invoice_status_label}
            <input type="hidden" name="invoiceID" value="{$invoiceID}">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>


{if $TPL["invoiceID"]}

  {show_new_invoiceItem("templates/invoiceItemForm.tpl")}

  {if count(invoice::get_invoiceItems($TPL["invoiceID"]))}
    {$table_box}
      <tr>
        <th>Invoice Items</th>
      </tr>
      <tr>
        <td>{show_invoiceItem_list()}</td>
      </tr>
    </table>
  {/}

{/}


</form>

{if defined("SHOW_INVOICE_ATTACHMENTS") && SHOW_INVOICE_ATTACHMENTS}
{show_attachments($TPL["invoiceID"])}
{/}

{show_footer()}
