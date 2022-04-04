var clipboard = new ClipboardJS(".clipboard-button");

function dismiss_notice(element) {
  jQuery(element)
    .parent()
    .slideUp("normal", function () {
      jQuery(this).remove();
    });
  return false;
}

jQuery(document).ready(function ($) {
  function clear_notices() {
    jQuery(".notice-dismiss").each(function (i, e) {
      dismiss_notice(e);
    });
  }

  if (test_status == "success") {
    $("#wbw_test_yes").show();
    $("#wbw_test_no").hide();
  } else {
    $("#wbw_test_no").show();
    $("#wbw_test_yes").hide();
  }

  $("form").on("submit", function () {
    $("#wbw_new_meeting_template_default").attr("disabled", "disabled");
    $("#wbw_existing_meeting_template_default").attr("disabled", "disabled");
    $("#wbw_other_meeting_template_default").attr("disabled", "disabled");
    $("#wbw_close_meeting_template_default").attr("disabled", "disabled");
  });

  $("#wbw_bmlt_test_status").val(test_status);

  $("#wbw_test_bmlt_server").on("click", function (event) {
    var parameters = {};
    parameters["wbw_bmlt_server_address"] = $("#wbw_bmlt_server_address").val();
    parameters["wbw_bmlt_username"] = $("#wbw_bmlt_username").val();
    parameters["wbw_bmlt_password"] = $("#wbw_bmlt_password").val();

    $.ajax({
      url: wbw_admin_bmltserver_rest_url,
      type: "POST",
      dataType: "json",
      contentType: "application/json",
      data: JSON.stringify(parameters),
      beforeSend: function (xhr) {
        clear_notices();
        xhr.setRequestHeader("X-WP-Nonce", $("#_wprestnonce").val());
      },
    })
      .done(function (response) {
        var msg = "";
        $("#wbw_bmlt_test_status").val("success");
        $("#wbw_test_yes").show();
        $("#wbw_test_no").hide();
        if (response.message == "")
          msg =
            '<div class="notice notice-success is-dismissible"><p><strong>SUCCESS: </strong><button type="button" class="notice-dismiss" onclick="javascript: return dismiss_notice(this);"></button></div>';
        else
          msg =
            '<div class="notice notice-success is-dismissible"><p><strong>SUCCESS: </strong>' +
            response.message +
            '.</p><button type="button" class="notice-dismiss" onclick="javascript: return dismiss_notice(this);"></button></div>';
        $(".wp-header-end").after(msg);
      })
      .fail(function (xhr) {
        $("#wbw_bmlt_test_status").val("failure");
        $("#wbw_test_no").show();
        $("#wbw_test_yes").hide();

        $(".wp-header-end").after(
          '<div class="notice notice-error is-dismissible"><p><strong>ERROR: </strong>' +
            xhr.responseJSON.message +
            '.</p><button type="button" class="notice-dismiss" onclick="javascript: return dismiss_notice(this);"></button></div>'
        );
      });
  });

});
