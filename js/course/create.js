require(["core/first", "jquery", "jqueryui", "core/ajax"], function (
  core,
  $,
  bootstrap,
  ajax
) {
  let courseRepoName = "";
  let courseRepoNameList = $("#id_name option")
    .toArray()
    .map((option) => option["value"])
    .filter((val) => val !== "");
  const toggleMode = (is_export_mode) => {
    if (is_export_mode) {
      $("#id_branch").attr("disabled", "disabled");
      $("#id_commit").attr("disabled", "disabled");
      $("#id_objective").removeAttr("disabled", "disabled");
      $("#id_level").removeAttr("disabled", "disabled");
      $("#id_domain").removeAttr("disabled", "disabled");
      $("#id_sub_domain").removeAttr("disabled", "disabled");
      $("#id_skill").removeAttr("disabled", "disabled");
      $("#id_ILOs_0_level").removeAttr("disabled", "disabled");
      $("#id_ILOs_0_verb").removeAttr("disabled", "disabled");
      $("#id_ILOs_0_statement").removeAttr("disabled", "disabled");
      $("#id_ILOs_0_credit").removeAttr("disabled", "disabled");
      $("#id_ILO_delete_button_0").removeAttr("disabled", "disabled");
      $("#id_ILO_add_fields").removeAttr("disabled", "disabled");
      $("input[name = is_imported]").val("no");
      return;
    }
    $("input[name = is_imported]").val("yes");
    $("#id_branch").removeAttr("disabled", "disabled");
    $("#id_commit").removeAttr("disabled", "disabled");
    $("#id_objective").attr("disabled", "disabled");
    $("#id_level").attr("disabled", "disabled");
    $("#id_domain").attr("disabled", "disabled");
    $("#id_sub_domain").attr("disabled", "disabled");
    $("#id_skill").attr("disabled", "disabled");
    $("#id_ILOs_0_level").attr("disabled", "disabled");
    $("#id_ILOs_0_verb").attr("disabled", "disabled");
    $("#id_ILOs_0_statement").attr("disabled", "disabled");
    $("#id_ILOs_0_credit").attr("disabled", "disabled");
    $("#id_ILO_delete_button_0").attr("disabled", "disabled");
    $("#id_ILO_add_fields").attr("disabled", "disabled");
  };
  $(document).ready(function () {
    toggleMode(true);
    console.log(4);
    $("#id_name").change(function () {
      let newCourseRepoName = $("#id_name").val();
      if (courseRepoName === newCourseRepoName || newCourseRepoName == "")
        return;
      courseRepoName = newCourseRepoName;
      if (!courseRepoNameList.includes(courseRepoName)) {
        $("input[name = repo_name]").val("");
        toggleMode(true);
        return;
      }
      toggleMode(false);
      $("input[name = repo_name]").val(courseRepoName);
      console.log(courseRepoName);
      ajax
        .call([
          {
            methodname: "external_calls_helpers_get_branches_for_course_repo",
            args: {
              course_repo: courseRepoName,
            },
          },
        ])[0]
        .done(function (response) {
          $("#id_branch").html("");

          var data = JSON.parse(response);

          $("<option/>")
            .val("none")
            .html("Select Branch")
            .appendTo("#id_branch");

          for (var i = 0; i < data.length; i++) {
            var vals = data[i]["ref"].split("/");
            var val = vals[vals.length - 1];
            $("<option/>").val(val).html(val).appendTo("#id_branch");
          }

          return;
        })
        .fail(function (err) {
          console.log(err);
          return;
        });
    });

    $("#id_branch").change(function () {
      $("input[name = branch_hid]").val($("#id_branch").val());

      var selected_course = $("#id_name").val();
      var selected_branch = $("#id_branch").val();

      ajax
        .call([
          {
            methodname: "external_calls_helpers_get_commits_for_branch",
            args: {
              selected_course: selected_course,
              selected_branch: selected_branch,
            },
          },
        ])[0]
        .done(function (response) {
          console.log(response);
          // clear out old values
          $("#id_commit").html("");

          var data = JSON.parse(response);

          $("<option/>")
            .val("none")
            .html("Select Version")
            .appendTo("#id_commit");

          for (var i = 0; i < data.length; i++) {
            $("<option/>")
              .val(data[i]["sha"])
              .html(data[i]["commit"]["message"])
              .appendTo("#id_commit");
          }

          return;
        })
        .fail(function (err) {
          console.log(err);
          return;
        });
    });

    $("#id_commit").change(function () {
      $("input[name = commit_hid]").val($("#id_commit").val());
      var selectedCourse = $("#id_name").val();
      var selected_commit = $("#id_commit").val();

      ajax
        .call([
          {
            methodname:
              "external_calls_helpers_get_course_metadata_from_commit",
            args: {
              selected_commit: selected_commit,
              selected_course: selectedCourse,
            },
          },
        ])[0]
        .done(function (response) {
          console.log(response);
          var data = JSON.parse(response);

          $("#input[name = id_code]").val(data["code"]);
          $("#id_name").val(data["name"]);
          $("#id_objective").val(data["objective"]);
          $("#id_domain").val(data["domain"]);
          $("#id_sub_domain").val(data["sub_domain"]);
          $("#id_skill").val(data["skill"]);
          $("input[name = ILOs_string]").val(data["ILOs"]);
        })
        .fail(function (err) {
          console.log(err);
          return;
        });
    });

    $("#id_domain").change(function () {
      ajax
        .call([
          {
            methodname: "external_calls_helpers_get_sub_domains_from_domain",
            args: {
              domain: $("#id_domain").val(),
            },
          },
        ])[0]
        .done(function (response) {
          $("#id_sub_domain").html("");
          var data = JSON.parse(response);
          $("<option/>")
            .val("")
            .html("Select Sub Domain")
            .appendTo("#id_sub_domain");
          for (var i = 0; i < data.length; i++) {
            var val = data[i];
            $("<option/>").val(data[i]).html(val).appendTo("#id_sub_domain");
          }
        });
    });

    $("#id_sub_domain").change(function () {
      ajax
        .call([
          {
            methodname: "external_calls_helpers_get_skills_from_sub_domain",
            args: {
              domain: $("#id_domain").val(),
              sub_domain: $("#id_sub_domain").val(),
            },
          },
        ])[0]
        .done(function (response) {
          console.log(response);

          $("#id_skill").html("");
          var data = JSON.parse(response);
          $("<option/>").val("").html("Select Skill").appendTo("#id_skill");
          for (var i = 0; i < data.length; i++) {
            var val = data[i];
            $("<option/>").val(data[i]).html(val).appendTo("#id_skill");
          }
        });
    });
  });
});
