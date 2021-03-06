(function($){
     var methods = {
        stopQuiz : function( reason ) {
            var d = superContainer.data();
            if (d.quizEnded) {
                return true;
            }
            endTime = $.now();
            spentTime = Math.round((endTime - startTime) / 1000) ;
            spentTimeMin = Math.floor(spentTime/60);
            spentTimeSec = spentTime - (spentTimeMin*60);
            var questionIds = [];
            superContainer.find('ul.answers').each(function(index) {
                var qAnswer = [];
                r = 1;
                $(this).children('li').each(function() {
                    if ($(this).hasClass('selected')) {
                        qAnswer.push(r);
                    }
                    r++;
                });
                questionIds.push($(this).attr("id"));
                userAnswers.push(qAnswer);
            });
            var collate =[];
            for (r = 0; r < userAnswers.length; r++) {
                collate.push('{"questionNumber":"'+parseInt(questionIds[r])+'", "userAnswers":"'+userAnswers[r]+'"}');
            }
            res = '[' + collate.join(",") + ']';
            countdown.timeTo("stop");
            progressKeeper.hide();
            notice.hide();
            notice_multi.hide();
            var results = checkAnswers(),
            resultSet = '',
            questionResult = '',
            trueCount = 0,
            score;
            for (var i = 0, toLoopTill = results.length; i < toLoopTill; i++) {
                if (results[i] === true) {
                    trueCount++;
                }
               if (results[i] === true)
                    questionResult = "<div class='correct'><span>Верно</span></div>";
                 else if (results[i] === false)
                    questionResult = "<div class='wrong'><span>Неверно</span></div>";
                else 
                    questionResult = "<div class='unanswered'><span>Без ответа</span></div>";
                resultSet += '<div class="result-row"> Вопрос №' + (i + 1) + questionResult;
                resultSet += '<div class="resultsview-qhover">' + quizConfig.questions[i].question;
                resultSet += "<ul>";
                for (answersIteratorIndex = 0; answersIteratorIndex < quizConfig.questions[i].answers.length; answersIteratorIndex++) {
                    var correctAdd = '';
                    var selectedAdd = '';
                    if (quizConfig.showCorrectAnswers) {
                        if ($.inArray( answersIteratorIndex + 1 , quizConfig.questions[i].ca ) !== -1 ) {
                            correctAdd += 'right';
                        }
                    }
                    if ($.inArray( answersIteratorIndex + 1 , userAnswers[i] ) !== -1 ) {
                        selectedAdd += ' selected-point';
                    }
                    resultSet += '<li class="' + correctAdd + '"><span class="' + selectedAdd + '"></span>' + quizConfig.questions[i].answers[answersIteratorIndex] + '</li>';
                }
                resultSet += '</ul></div></div>';
            }
            resultSet += '<div class="jquizzy-clear"></div><div class="legend"> ';
            if (quizConfig.showCorrectAnswers) {
                resultSet += '<span class="right-point"> - Правильный ответ</span>, ';
            }
            resultSet += '<span class="selected-point"> - Выбор пользователя </span> </div>';                
            score = roundReloaded(trueCount / questionLength * 100, 2);
            if (quizConfig.sendResultsURL !== null) {
                console.log("Попытка отправки результатов теста");
                $.ajax({
                    type: 'POST',
                    url: quizConfig.sendResultsURL,
                    data: { 
                        task: 'save_result', 
                        cause: reason, 
                        ticket: $("#ticket").val(), 
                        dossier_id: $("#dossier_id").val(), 
                        trial: $("#trial").val(), 
                        answers: res,
                        percentage: score,
                        begined: startTime,
                        ended: endTime
                    }
                }).done(function( msg ) { 
                    message = '<div class="ui-state-highlight ui-corner-all" id="message">';
                    message += '<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>';
                    message += '<strong>' + msg + '</strong><br /></p></div>';
                    $(message).appendTo('.message');
                });
            }
            resultSet = '<h2 class="qTitle">' + reason + '<br/> Результат: ' + judgeSkills(score) + ', Вы набрали ' + score + '%, затрачено времени ' + spentTimeMin + ' мин.</h2> ' + resultSet + '<div class="jquizzy-clear"/>';
            superContainer.find('.result-keeper').html(resultSet).show(500);
            superContainer.find('.resultsview-qhover').hide();
            //if (quizConfig.showCorrectAnswers) {
                superContainer.find('.result-row').hover(function() {
                    $(this).find('.resultsview-qhover').show();
                    }, function() {
                    $(this).find('.resultsview-qhover').hide();
                    });
            //}
            superContainer.find('.slide-container').hide(function() {
                superContainer.find('.results-container').fadeIn(500); 
            });
            $('#' + quizConfig.closePageButtonId).toolbar('showButton');
            $('#' + quizConfig.cancelTestButtonId).toolbar('hideButton');
            superContainer.data('quizEnded', 1);
            return true;
        },
        saveResults : function() {
          // 
        }
    };
    
    $.fn.quiz = function(method) {
        var defaults = {
            questions: null,
            startText : 'Начало теста',
            endText: 'Тест завершен',
            splashImage: 'includes/style/images/play-icon.png',
            sendResultsURL: 'quiz_helper.php',
            timeToTest: 3600,
            hostip: '172.16.172.33', 
            //hostip: 'quiz.miac-io.ru',
            //hostip: 'attest.miac-io.ru',
            //hostip: '127.0.0.1:8080',
            showCorrectAnswers: true,
            closePageButtonId: 'close_quizpage',
            cancelTestButtonId: 'cancel_quiz',
            resultComments :  
            {
                perfect: 'Замечательно! (оценка - 5)',
                excellent: 'Оценка - 5',
                good: 'Хорошо! (оценка - 4)',
                average: 'Приемлемо! (оценка - 3)',
                bad: 'Неудовлетворительно! (оценка - 2)',
                poor: 'Оценка - 1',
                worst: 'Оценка - 1'
            }
        }, 
        method, settings = {};
        
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } 
        else if ( typeof method === 'object' || ! method ) {
            settings = method;
        } else {
            $.error( 'Метод с именем ' +  method + ' не существует для jQuery.quiz' );
        }
        
        quizConfig = $.extend(defaults, settings);  
        if (window.location.host != quizConfig.hostip) {
            console.log('Хост ' + window.location.host);
            $(this).html('<div class="intro-container slide-container"><h2 class="qTitle">Ошибка подготовки вопросов (1)</h2></div>');
            return;
        }
        if (quizConfig.questions === null) 
        {
            $(this).html('<div class="intro-container slide-container"><h2 class="qTitle">Ошибка подготовки вопросов (2)</h2></div>');
            return;
        }
        if (quizConfig.questions.length === 0) 
        {
            $(this).html('<div class="intro-container slide-container"><h2 class="qTitle">Нет вопросов по выбранной теме</h2></div>');
            return;
        }
        
        superContainer = this;
        answers = [];
        introFob = '<div class="intro-container slide-container"><div class="question-number">'+quizConfig.startText+'</div><a class="nav-start" href="#"><img src="'+quizConfig.splashImage+'" /></a></div>';
        exitFob =  '<div class="results-container slide-container"><div class="question-number">'+quizConfig.endText+'</div><div class="result-keeper"></div></div>';
        exitFob += '<div class="notice">Пожалуйста выберите вариант ответа</div><div class="notice_multi">Для данного вопроса нужно выбрать более одного правильного ответа</div>';
        exitFob += '<div class="progress-keeper" ><div class="progress"></div></div>';
        contentFob = '';
        startTime = '';
        endTime = '';
        countdown = $('#countdown');
        superContainer.addClass('main-quiz-holder');
        qType = function(qt) {
            if (qt == 1)
                return "Выбор";
            else if (qt == 2)
                return "Несколько ответов";
            else 
                return "Тип вопроса не определен";
        }; 
        if (window.location.host == quizConfig.hostip) {
            for (questionsIteratorIndex = 0; questionsIteratorIndex < quizConfig.questions.length; questionsIteratorIndex++) {
                contentFob += '<div class="slide-container">';
                contentFob += '<div class="question-type">Тип вопроса: "' + qType(quizConfig.questions[questionsIteratorIndex].qT) + '"</div>';
                contentFob += '<div class="question-number">' + (questionsIteratorIndex + 1) + '/' + quizConfig.questions.length + '</div>';
                contentFob += '<div class="question"></div>'
                contentFob += '<ul id="' + quizConfig.questions[questionsIteratorIndex].qId +  '" class="answers">';
                for (answersIteratorIndex = 0; answersIteratorIndex < quizConfig.questions[questionsIteratorIndex].answers.length; answersIteratorIndex++) {
                    contentFob += '<li>' + quizConfig.questions[questionsIteratorIndex].answers[answersIteratorIndex] + '</li>';
                }
                contentFob += '</ul>';
                contentFob += '<div class="nav-container">';
                if (questionsIteratorIndex !== 0) {
                    contentFob += '<div class="prev"><a class="nav-previous" href="#">Предыдущий</a></div>';
                }
                if (questionsIteratorIndex < quizConfig.questions.length - 1) {
                    contentFob += '<div class="next"><a class="nav-next" href="#">Следующий</a></div>';
                }
                else {
                    contentFob += '<div class="next final"><a class="nav-show-result" href="#">Завершение</a></div>';
                }
                contentFob += '</div></div>';
                answers.push(quizConfig.questions[questionsIteratorIndex].ca);
            }
            superContainer.html(introFob + contentFob + exitFob);
            var qid_collate = [];
            for (r = 0; r < quizConfig.questions.length; r++) {
                qid_collate.push( quizConfig.questions[r].qId  );
            }
            q_ids = '[' + qid_collate.join(",") + ']';
            $.ajax({
                type: 'POST',
                url: quizConfig.sendResultsURL,
                data: { 
                    task: 'get_question', 
                    ids: q_ids
                }
            }).done(function(ret) { 
                t = $.parseJSON(ret);
                r = 0;
                superContainer.find('.question').each(function() {
                        $(this).text(t[r]);
                        quizConfig.questions[r].question = t[r];
                        r++;
                    } 
                );
            });
            
            progress = superContainer.find('.progress');
            progressKeeper = superContainer.find('.progress-keeper');
            notice = superContainer.find('.notice');
            notice_multi = superContainer.find('.notice_multi');
            progressWidth = progressKeeper.width();
            userAnswers = [];
            questionLength = quizConfig.questions.length;
            slidesList = superContainer.find('.slide-container');
            progressKeeper.hide();
            notice.hide();
            notice_multi.hide();
            slidesList.hide().first().fadeIn(500);
        }
        
        countdown.timeTo(quizConfig.timeToTest, function() { methods.stopQuiz(' Время отведенное на ответы исчерпано. ') });

        checkAnswers = function() {
            var resultArr = [],
            flag = false;
            for (i = 0; i < answers.length; i++) {
                if (answers[i].toString() == userAnswers[i].toString()) {
                    flag = true;
                } else {
                    flag = false;
                }
                if (userAnswers[i].toString() == '') {
                    flag = null;
                }
                resultArr.push(flag);
            }
            return resultArr;
        };
        
 /*        function deobfuscate(s)
        {
            s = s.split('%').slice(1);
            c = '';
            for (i = 0; i < s.length; i++)
            {
                console.log("Номер символа " +  s[i]);
                c += String.fromCharCode(s[i]);
            }
            return c;
        }

        function deobfuscate(s)
        {
            s = s.split('%').slice(1);
            c = '';
            for (i = 0; i < s.length; i++)
            {
                c += s[i] + String.fromCharCode(s[i].substr(1)-s[i].charCodeAt());
            }
            return c;
        }
         */
         
        roundReloaded = function(num, dec) {
            var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
            return result;
        };
            
        judgeSkills = function(score) {
            var returnString;
            if (score >= 90)
                return quizConfig.resultComments.excellent;
            else if (score >= 80)
                return quizConfig.resultComments.good;
            else if (score >= 70)
                return quizConfig.resultComments.average;
            else if (score >= 50)
                return quizConfig.resultComments.bad;
            else
                return quizConfig.resultComments.poor;
        };

        superContainer.find('li').click(function() {
            var thisLi = $(this);
            if ( thisLi.parents('.slide-container').find('.question-type').html() == 'Тип вопроса: "Несколько ответов"') {
                if (!thisLi.hasClass('selected')) {
                    thisLi.addClass('selected');
                } else {
                    thisLi.removeClass('selected');
                }
            } else {
                if (thisLi.hasClass('selected')) {
                    thisLi.removeClass('selected');
                } else {
                    thisLi.parents('.answers').children('li').removeClass('selected');
                    thisLi.addClass('selected');
                }
            }
        });

        superContainer.find('.nav-start').click(function() {
            $(this).parents('.slide-container').fadeOut(500, function() {
                $(this).next().fadeIn(500);
                progressKeeper.fadeIn(500);
            });
            startTime = $.now();
            countdown.timeTo("start", quizConfig.timeToTest);
            return false;
        });

        superContainer.find('.next').click(function() {
            notice.hide();
            notice_multi.hide();
            var multianswer = false;
            if ($(this).parents('.slide-container').find('div.question-type').text() === 'Тип вопроса: "Несколько ответов"') {
                multianswer = true;
            }
            if ($(this).parents('.slide-container').find('li.selected').length === 0) {
                notice.fadeIn(300);
                return false;
            }
            notice.hide();
            if ($(this).parents('.slide-container').find('li.selected').length === 1 && multianswer) {
                notice_multi.fadeIn(300);
                return false;
            }
            notice_multi.hide();
            $(this).parents('.slide-container').fadeOut(500, function() {
                $(this).next().fadeIn(500);
            });
            progress.animate({
                width: progress.width() + Math.round(progressWidth / questionLength)
                }, 500);
            return false;
        });

        superContainer.find('.prev').click(function() {
            notice.hide();
            $(this).parents('.slide-container').fadeOut(500, function() {
                $(this).prev().fadeIn(500);
                });
            progress.animate({
                width: progress.width() - Math.round(progressWidth / questionLength)
                }, 500);
            return false;
        });

        superContainer.find('.final').click(function() {
            if ($(this).parents('.slide-container').find('li.selected').length === 0) {
                notice.fadeIn(300);
                return false;
            }
            countdown.timeTo("stop");
            methods.stopQuiz(' Все вопросы пройдены. ');
        });
    };
})(jQuery);