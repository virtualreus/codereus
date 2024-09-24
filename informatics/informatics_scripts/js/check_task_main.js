$(document).ready(function() {
    $("#main_task_block_ans_send").click(function(event) {
        event.preventDefault();
        let press_button = document.getElementById("main_task_block_ans_send");
        let user_answer =  document.getElementById("main_task_block_ans_input");
        let user_answer_value = user_answer.value

        var queryString = window.location.search;

        queryString = queryString.substring(1);
        var queryParams = queryString.split('&');
        var params = {};
        
        for (var i = 0; i < queryParams.length; i++) {
            var pair = queryParams[i].split('=');
            var key = decodeURIComponent(pair[0]);
            var value = decodeURIComponent(pair[1]);
            params[key] = value;
        }
        
        var task_number = params['number'];

        var task_id = press_button.getAttribute("data-number-id");
        var url = '../informatics/informatics_scripts/php/check_task_main.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                answer: user_answer_value,
                task: task_number,
                task_id: task_id,
            },
            success: function(response) {
                switch (response) {
                    case "correct":
                        var mainTaskElement = document.getElementById('main_task_block');
                        var answerField = document.getElementById("main_task_block_ans_input");
                        var refreshButton = document.getElementById("task_refresh");

                        mainTaskElement.style.border = '1px solid #b9d4b8';
                        mainTaskElement.style.boxShadow = 'rgba(6, 125, 0, 0.3) 4px 4px 80px 0px';
    
                        answerField.style.color = "#056305";
                        answerField.style.border = '1px solid #b9d4b8';

                        answerField.readOnly = true;

                        answerField.style.backgroundColor = "rgb(212 212 212)"; // Затемним фон
                        answerField.style.cursor = "not-allowed";
                        press_button.disabled = true;

                        refreshButton.style.transform = "scale(1.1)";
                        refreshButton.style.boxShadow = "4px 4px 98px 9px rgba(246, 158, 196, 90%)";

                        note({
                            content: "Совершенно правильный ответ!",
                            type: "success",
                            time: 5
                        });
                        setTimeout(function() { location.reload(); }, 7000);
                        break;
                    case "incorrect":
                        var mainTaskElement = document.getElementById('main_task_block');
                        var answerField = document.getElementById("main_task_block_ans_input");
                        mainTaskElement.style.border = '1px solid rgb(229 165 165)"';
                        mainTaskElement.style.boxShadow = 'rgb(146 0 0 / 37%) 4px 4px 75px 0px';
    
                        answerField.style.color = "rgb(99 5 5)";
                        answerField.style.border = '1px solid rgb(212 184 184)';
                        note({
                            content: "Неверный ответ. Попробуй еще!",
                            type: "error",
                            time: 5
                        });
                        break;

                    case "correct_A":
                        var mainTaskElement = document.getElementById('main_task_block');
                        var answerField = document.getElementById("main_task_block_ans_input");
                        mainTaskElement.style.border = '1px solid rgb(229 165 165)"';
                        mainTaskElement.style.boxShadow = 'rgb(146 101 0 / 37%) 4px 4px 75px 0px';
    
                        answerField.style.color = "rgb(145 103 21)";
                        answerField.style.border = '1px solid rgb(212 204 184)';
                        note({
                            content: "Ответ A - верно.<br> Ответ B - неверно :(",
                            type: "warn",
                            time: 5
                        });
                        break;
                    case "correct_B":
                        var mainTaskElement = document.getElementById('main_task_block');
                        var answerField = document.getElementById("main_task_block_ans_input");
                        mainTaskElement.style.border = '1px solid rgb(229 165 165)"';
                        mainTaskElement.style.boxShadow = 'rgb(146 101 0 / 37%) 4px 4px 75px 0px';
    
                        answerField.style.color = "rgb(145 103 21)";
                        answerField.style.border = '1px solid rgb(212 204 184)';
                        note({
                            content: "Ответ A - неверно :(.<br> Ответ B - верно!",
                            type: "warn",
                            time: 5
                        });
                        break;
                    case "empty":
                        note({
                            content: "Введите Ваш ответ.",
                            type: "info",
                            time: 5
                        });
                        break;
                    default:
                        console.log(response);
                        break
                    }



                    
            }
          });

    })
})