function showIssueReceiptModal(partner_id) {
    const url = `/partner/partners/${partner_id}/issue_receipt`;
    const container = $(".issue-receipt-modal");

    $.ajax({
        url,
        method: 'GET',
        success: function (response) {
            container.html(response);
            container.modal('show');
        }
    })
}

function showUnsettledReceipts(partner_id) {
    const url = `/partner/partners/${partner_id}}/unsettled`;
    const container = $(".unsettled-receipts-modal");

    $.ajax({
        url,
        method: 'GET',
        success: function (response) {
            container.html(response);
            container.modal('show');
        }
    })
}

function printReceipts(receipt_ids, paid=0) {
    $form = $("#form_bulk_print");
    $form.find("input[name='type']").val(paid == 1 ? 'payment' : 'receipt');
    $form.find("input[name='selected_ids']").val(receipt_ids.join(','));
    $form.trigger('submit');
}

$(document).ready(() => {
    // issue_receipt modal
    $(document).on('shown.bs.modal', '.issue-receipt-modal, .add-additional-payment-modal, .unsettled-receipts-modal', function (e) {
        $issue_months = $(e.target).find("#issue_months");
        const start_month = $issue_months.data('start-month');
        const end_month = $issue_months.data('end-month');
        initMonthPicker($issue_months, {
            startMonth: start_month,
            endMonth: end_month,
            drops: 'up'
        });

        $(e.target).find('input.input-icheck').iCheck({
            checkboxClass: 'icheckbox_square-blue',
        });
    });

    $(document).on('submit', '.add-additional-payment-modal form', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $form = $(e.target);
        const $modal = $(".add-additional-payment-modal");
        const url = "/partner/receipts/issue";
        const data = {
            partner_id: $form.find('input[name="partner_id"]').val(),
            issue_months: $form.find('input[name="issue_months"]').val(),
            paid: 1,
            additional_payment: 1,
        }

        $.ajax({
            method: 'POST',
            url,
            data,
            dataType: 'json',
            success: function (result) {
                if (result.success == 1) {
                    $modal.modal("hide");

                    if (result.msg?.length > 0) {
                        toastrSwal(result.msg[0].message, result.msg[0].type, function() {
                            if(result.new_receipt_ids?.length > 0) {
                                printReceipts(result.new_receipt_ids, 1)
                            }
                        });

                        $modal.trigger('receipts_updated');
                    }
                } else {
                    $form.find('button[type="submit"]').prop('disabled', false);
                    toastrSwal(result.msg, 'error');
                }
            }
        });
    });

    $(document).on('submit', '.bulk-issue-receipts-modal form, .issue-receipt-modal form', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $form = $(e.target);
        const $modal = $form.closest(".modal");
        const data = new FormData(e.target);
        const paid = $form.find("input[name='paid']")[0].checked;
        const issue_months = $form.find("input[name='issue_months']").val();
        var url = $form.attr('action');

        // To check whether receipt would be made for the future.
        if(paid == 0) {
            const months = issue_months.split('-');
            const to_month_str = months[1].trim();
            const parts = to_month_str.split('/');
            const m = parseInt(parts[0], 10) - 1;
            const y = parseInt(parts[1], 10);
            const to_month_date = new Date(y, m, 1);
            const today = new Date();
            if(today < to_month_date) {
                // toastrSwal("Receipt could not made for the future month", 'warning');
                toastrSwal(LANG.receipt_issued_future_month, 'warning');
                $form.find("button[type='submit']").prop('disabled', false);
                return;
            }
        }

        $.ajax({
            method: 'POST',
            url,
            data,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success == 1) {
                    $modal.modal("hide");
                    if (result.msg?.length > 0) {
                        toastrSwalArray(result.msg, function() {
                            if(result.new_receipt_ids?.length > 0) {
                                printReceipts(result.new_receipt_ids, paid);
                            }

                            $modal.trigger('receipts_updated');
                        });
                    }
                } else {
                    $form.find('button[type="submit"]').prop('disabled', false);
                    toastrSwal(result.msg, 'error');
                }
            }
        });

        return false;
    })

    $(document).on('click', '.unsettled-receipts-modal #btn_settle_unpaidReceipts', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $selected_unpaid_receipts = $("table.unpaid-receipts tbody tr input.sel_unpaid_receipt:checked");
        if ($selected_unpaid_receipts.length == 0) return;

        const selected_ids = [];
        for (let i = 0; i < $selected_unpaid_receipts?.length; i++) {
            selected_ids.push($($selected_unpaid_receipts[i]).data('receipt-id'))
        }

        const data = {
            selected_ids,
        };
        $.ajax({
            url: '/partner/receipts/bulk_settle',
            data,
            method: 'post',
            dataType: 'json',
            success: function (result) {
                if (result.success == true) {
                    toastrSwal(result.msg);

                    for(let i=0; i<$selected_unpaid_receipts?.length; i++) {
                        $tr = $($selected_unpaid_receipts[i]).closest("tr");
                        $tr.remove();
                    }

                    printReceipts(selected_ids, 1);
                } else {
                    toastrSwal(result.msg, 'error');
                }
            }
        })
    })

    $(document).on('click', '.unsettled-receipts-modal #btn_issue_receipts', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $modal = $(".unsettled-receipts-modal");
        const url = "/partner/receipts/issue";
        const paid = $modal.find('input[name="paid"]')[0].checked ? 1 : 0;
        const data = {
            partner_id: $("input[name='partner_id']").val(),
            ignore_leave: 1,
            issue_months: $modal.find('input[name="issue_months"]').val(),
            paid: paid,
        }

        $.ajax({
            method: 'POST',
            url,
            data,
            dataType: 'json',
            success: function (result) {
                if (result.success == 1) {
                    toastrSwalArray(result.msg, function() {
                        if(result.new_receipt_ids?.length > 0) {
                            printReceipts(result.new_receipt_ids, paid)
                        }
                    });
                } else {
                    
                    toastrSwal(result.msg, 'error');
                }
            }
        });
    })
})