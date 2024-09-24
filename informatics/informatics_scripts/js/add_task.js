$(document).ready(function() {
    $("#add_task").click(function() {
        $("#add_task_block").css("display", "flex");
        $("#overlay").css("display", "block");
    });

    $(".add_task_close").click(function() {
        $("#add_task_block").css("display", "none");
        $("#overlay").css("display", "none");
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            $("#add_task_block").css("display", "none");
            $("#overlay").css("display", "none"); 
        }
    });
    

    $("#add_task_send").click(function(event) {
        event.preventDefault();
        let number = document.getElementById("add_task_number");
        let number_value = number.value;
        if (!number_value) {
            number.style.border = '1px solid #e20f0f';
            number.style.color = '#9d0000';
            return 0;
        }

        let answers = document.getElementById("add_task_answers");
        let answers_value = answers.value;
        if (!answers_value) {
            answers.style.border = '1px solid #e20f0f';
            answers.style.color = '#9d0000';
            return 0;
        }

        let condition = document.getElementById("add_task_cond");
        let condition_photo = condition.files[0];

        let diff = document.getElementById("add_task_difficulty");
        let diff_value = diff.value;
        if (!diff_value) {
            diff_value = 2;
        }

        let formData = new FormData();
        formData.append("photo", condition_photo);
        formData.append("number", number_value);
        formData.append("answers", answers_value);
        formData.append("diff", diff_value);

        var files_task = $("#add_task_files")[0].files;
        for (var i = 0; i < files_task.length; i++) {
            formData.append("add_task_files[]", files_task[i]);
        }

       var url = '../informatics/informatics_scripts/php/add_task.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,

            success: function(response) {
                switch (response) {
                    case "success":

                        $("#add_task_answers").val(""); 
                        $("#add_task_cond").val("");
                        $("#add_task_files").val(""); 
        
                        note({
                            content: "Задание успешно добавленио.",
                            type: "info",
                            time: 3
                        });
                        break;
                    case "error":

                        $("#add_task_answers").val(""); 
                        $("#add_task_cond").val("");
                        $("#add_task_files").val(""); 
        
                        note({
                            content: "Некорректный номер.",
                            type: "error",
                            time: 3
                        });
                        break;
                    default:
                        console.log(response)
                        break;
                }

            }
          });

    })


})