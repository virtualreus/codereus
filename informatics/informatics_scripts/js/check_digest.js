function checkAns(button_node, varId, taskIndex) {
    let button_props = button_node.parentNode
    let answer_given = button_props.childNodes[1].value

    let task_block = button_props.parentNode

    if (!answer_given) {
        
        note({
            content: `Введите ответ.`,
            type: "warn",
            time: 3
        });
        return 0;
    }


    var url = '../informatics/informatics_scripts/php/check_digest.php';
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            var_id: varId,
            task_index: taskIndex,
            answer_given: answer_given,
        },
        success: function(response) {
            var mainTaskElement = task_block
            var answerField = answer_given = button_props.childNodes[1]
            switch (response) {
                case "0":
                    mainTaskElement.style.border = '1px solid rgb(229 165 165)"';
                    mainTaskElement.style.boxShadow = 'rgb(146 0 0 / 37%) 4px 4px 75px 0px';

                    answerField.style.color = "rgb(99 5 5)";
                    answerField.style.border = '1px solid rgb(212 184 184)';

                    note({
                        content: `Ответ неверный. Попробуй еще раз`,
                        type: "error",
                        time: 3
                    });
                    break;
                case "1":

                    mainTaskElement.style.border = '1px solid #b9d4b8';
                    mainTaskElement.style.boxShadow = 'rgba(6, 125, 0, 0.3) 4px 4px 80px 0px';

                    answerField.style.color = "#056305";
                    answerField.style.border = '1px solid #b9d4b8';

                    answerField.readOnly = true;

                    answerField.style.backgroundColor = "rgb(212 212 212)"; // Затемним фон
                    button_node.remove()


                    note({
                        content: `Верный ответ!`,
                        type: "success",
                        time: 3
                    });
                    break;
                case "2":
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
                case "3":
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
            }

        }
    });

}