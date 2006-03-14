{:show_header}
{:show_toolbar}
{table_box}
  <tr>
    <th colspan="5">Finance Reconciliation Report</th>
    <th class="right">{month_links}</th>
  </tr>
  <tr>
    <th colspan="6">TF Balances</th>
  </tr>
  <tr>
    <td>TF</td>
    <td align="right">Opening balance</td>
    <td align="right">Expenses</td>
    <td align="right">Salaries</td>
    <td align="right">Invoices</td>
    <td align="right">Closing Balance</td>
  </tr>
  {:show_tf_balances templates/reconciliationTFR.tpl}
  <tr>
    <td><strong>TOTAL:</td>
    <td align="right">{total_opening_balance}</td>
    <td align="right">{total_expense}</td>
    <td align="right">{total_salary}</td>
    <td align="right">{total_invoice}</td>
    <td align="right">{total_closing_balance}</td>
  </tr>
</table>

{table_box}
  <tr>
    <th colspan="5">Expenses</th>
  </tr>
  <tr>
    <td>#</td>
    <td>Date</td>
    <td>Product</td>
    <td>TF</td>
    <td align="right">Amount</td>
  </tr>
  {:show_transaction_list expense}
  <tr>
    <td colspan="4"><strong>TOTAL:</td>
    <td align="right">{total_amount}</td>
  </tr>
</table>

{table_box}
  <tr>
    <th colspan="5">Salaries</th>
  </tr>
  <tr>
    <td>#</td>
    <td>Date</td>
    <td>Product</td>
    <td>TF</td>
    <td align="right">Amount</td>
  </tr>
  {:show_transaction_list salary}
  <tr>
    <td colspan="4"><strong>TOTAL:</td>
    <td align="right">{total_amount}</td>
  </tr>
</table>

{table_box} 
  <tr>
    <th colspan="5">Invoices</th>
  </tr>
  <tr>
    <td>#</td>
    <td>Date</td>
    <td>Product</td>
    <td>TF</td>
    <td align="right">Amount</td>
  </tr>
  {:show_transaction_list invoice}
  <tr>
    <td colspan="4"><strong>TOTAL:</td>
    <td align="right">{total_amount}</td>
  </tr>
</table>

<div align="center">Report generated by Alloc.  {current_date}</div>
{:show_footer}
