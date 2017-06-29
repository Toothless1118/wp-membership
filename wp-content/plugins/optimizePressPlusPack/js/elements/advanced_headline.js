opjq(document).ready(function($){
    //set animation timing
    var animationDelay = 2500,

        //loading bar effect
        barAnimationDelay = 3800,
        barWaiting = barAnimationDelay - 3000, //3000 is the duration of the transition on the loading bar - set in the scss/css file

        //letters effect
        lettersDelay = 50,
        lettersDelayFast = 25,

        //type effect
        typeLettersDelay = 150,
        typeLettersDelayFast = 50,
        selectionDuration = 500,
        typeAnimationDelay = selectionDuration + 800,

        //clip effect
        revealDuration = 600,
        revealAnimationDelay = 1500,

        $headlines = $('.op-headline');

    initHeadline();

    function initHeadline() {
        //insert <i> element for each letter of a changing word
        singleLetters($('.op-headline.letters').find('b'));

        //initialise headline animation
        animateHeadline($headlines);

        // We need an additional div wrapper
        // for this animation type
        $headlines.each(function () {
            var $headline = $(this);
            var $headlineWordWrapper;

            if ($headline.hasClass('clip')) {
                $headlineWordWrapper = $($headline.find('.op-words-wrapper'));
                if (!$headlineWordWrapper.parent().hasClass('op-words-wrapper-container')) {
                    // $headlineWordWrapper.prev().wrap('<span class="op-words-static-container"></span>');
                    $headlineWordWrapper.wrap('<div class="op-words-wrapper-container"></div>');
                }

            }
        });
    }

    $(document).on('op.afterLiveEditorParse', function() {
        initHeadline();
    });

    /**
     * We want to reposition and resize the headlines
     * if the browser window is resized
     */
    var calculateHeadlineWidth;
    $(window).on('resize', function () {
        clearTimeout(calculateHeadlineWidth);
        var headlineNr = 0;
        calculateHeadlineWidth = setTimeout(function () {
            $headlines.find('.op-words-wrapper').css({ width: 'auto' });
            $headlines.each(function(){
                headlineNr += 1;
                var $headline = $(this);

                if (!$headline.hasClass('type') && !$headline.hasClass('type_fast')) {
                    //assign to .op-words-wrapper the width of its longest word
                    var words = $headline.find('.op-words-wrapper b');
                    var width = 0;
                    var height = 0;

                    $headline.find('.op-words-wrapper').css({ width: 'auto' });

                    if ($headline.hasClass('clip')) {
                        $headline.find('.op-words-wrapper-container').css({ width: 'auto' });
                    }

                    words.each(function(){
                        var wordWidth;

                        $(this).addClass('op-word-relative');
                        wordWidth = $(this).get(0).getBoundingClientRect().width;
                        // var wordHeight = $(this).get(0).getBoundingClientRect().height;

                        if (wordWidth > width) {
                            width = wordWidth + 5;
                        }

                        // if (wordHeight > height) {
                        //     height = wordHeight;
                        // }

                        if ($headline.hasClass('clip')) {
                            $(this).css({ width: wordWidth + 5 });
                        }

                        $(this).removeClass('op-word-relative');
                    });

                    if ($headline.hasClass('clip')) {
                        $headline.find('.op-words-wrapper-container').css({ width: width + 5 });
                    } else {
                        $headline.find('.op-words-wrapper').css({ width: width });
                    }

                }
            });
        }, 200);
    });

    function singleLetters($words) {
        $words.each(function(){
            var word = $(this);
            var headline = word.closest('.letters');
            var letters = word.text().split('');
            var selected = word.hasClass('is-visible');
            var j = 0;
            var isEffectRotate = word.parents('.rotate-2').length > 0;
            var isLetterSpace = false;

            for (i in letters) {
                isLetterSpace = false;
                if (letters[i] === ' ') {
                    isLetterSpace = true;
                }
                letters[i] = letters[i].replace(' ', '&nbsp');
                // letters[i] = String(letters[i]).split(" ").join("&nbsp;");
                if (isEffectRotate) {
                    letters[i] = '<em>' + letters[i] + '</em>';
                }

                letters[i] = (selected) ? '<i class="in">' + letters[i] + '</i>' : '<i>' + letters[i] + '</i>';
                if (headline.hasClass('rotate-2') || headline.hasClass('rotate-3') || headline.hasClass('scale')) {
                    if (j === 0) {
                        letters[i] = '<span class="nobreak">' + letters[i];
                    }
                    if (isLetterSpace) {
                        letters[i] = '</span>' + letters[i] + '<span class="nobreak">';
                    }
                    j = j + 1;
                }
            }

            var newLetters = letters.join('');
            newLetters = newLetters + '</span>';
            word.html(newLetters).css('opacity', 1);
        });
    }

    function animateHeadline($headlines) {
        var duration = animationDelay;
        $headlines.each(function(){
            var headline = $(this);

            if(headline.hasClass('loading-bar')) {
                duration = barAnimationDelay;
                setTimeout(function () {
                    headline.find('.op-words-wrapper b:eq(0)').addClass('is-loading');
                }, barWaiting);
            } else if (!headline.hasClass('type') && !headline.hasClass('type_fast')) {
                //assign to .op-words-wrapper the width of its longest word
                var words = headline.find('.op-words-wrapper b'),
                    width = 0;
                words.each(function(){
                    $(this).css('position', 'relative')
                    var wordWidth = $(this).width();
                    if (wordWidth > width) {
                        width = wordWidth;
                    }
                    $(this).css({
                        width: '',
                        position: ''
                    });
                });
                headline.find('.op-words-wrapper').css('width', width);
            };

            //trigger animation
            setTimeout(function () {
                hideWord(headline.find('.is-visible').eq(0));
            }, duration);
        });
    }

    function hideWord($word) {
        var nextWord = takeNext($word);

        if ($word.parents('.op-headline').hasClass('type')) {

            var parentSpan = $word.parent('.op-words-wrapper');
            parentSpan.addClass('selected').removeClass('waiting');
            parentSpan.removeClass('selected');
            hideLetter($word.find('i').last(), $word, bool, typeLettersDelay, ':first-child');
            setTimeout(function() {
                showWord(nextWord, typeLettersDelay);
            }, typeLettersDelay * $word.find('i').length);

        } else if ($word.parents('.op-headline').hasClass('type_fast')) {

            var parentSpan = $word.parent('.op-words-wrapper');
            parentSpan.addClass('selected').removeClass('waiting_for_fast');
            setTimeout(function(){
                parentSpan.removeClass('selected');
                $word.removeClass('is-visible').addClass('is-hidden').children('i').removeClass('in').addClass('out');
            }, selectionDuration);
            setTimeout(function(){ showWord(nextWord, typeLettersDelayFast) }, typeAnimationDelay);

        } else if ($word.parents('.op-headline').hasClass('letters')) {

            var bool = ($word.children('i').length >= nextWord.children('i').length);
            hideLetterFast($word.find('i').eq(0), $word, bool, lettersDelayFast);
            showLetterFast(nextWord.find('i').eq(0), nextWord, bool, lettersDelayFast);

        } else if ($word.parents('.op-headline').hasClass('clip')) {

            $word.parent().animate({ width : '2px' }, revealDuration, function(){
                switchWord($word, nextWord);
                showWord(nextWord);
            });

        } else if ($word.parents('.op-headline').hasClass('loading-bar')) {

            $word.parent().find('b.is-loading').removeClass('is-loading');
            switchWord($word, nextWord);
            setTimeout(function(){ hideWord(nextWord) }, barAnimationDelay);
            setTimeout(function(){ nextWord.addClass('is-loading') }, barWaiting);

        } else {

            switchWord($word, nextWord);
            setTimeout(function(){ hideWord(nextWord) }, animationDelay);

        }
    }

    function showWord($word, $duration) {
        if ($word.parents('.op-headline').hasClass('type')) {

            showLetter($word.find('i').eq(0), $word, false, $duration);
            $word.addClass('is-visible').removeClass('is-hidden');

        } else if ($word.parents('.op-headline').hasClass('type_fast')) {

            showLetterFast($word.find('i').eq(0), $word, false, $duration);
            $word.addClass('is-visible').removeClass('is-hidden');

        } else if ($word.parents('.op-headline').hasClass('clip')) {

            $word.parent().animate({ 'width' : $word.width() + 10 }, revealDuration, function(){
                setTimeout(function(){ hideWord($word) }, revealAnimationDelay);
            });

        }
    }

    // var $letterToHide;
    function hideLetter($letter, $word, $bool, $duration, direction) {

        direction = direction || ':last-child';
        $letter.removeClass('in').addClass('out');

        if (!$letter.is(direction)) {
            if (direction === ':last-child') {
                setTimeout(function(){ hideLetter($letter.next(), $word, $bool, $duration, direction); }, $duration);
            } else {
                setTimeout(function(){ hideLetter($letter.prev(), $word, $bool, $duration, direction); }, $duration);
            }
        } else if ($bool) {
            setTimeout(function(){ hideWord(takeNext($word)) }, animationDelay);
        }

        if ($letter.is(direction) && $('html').hasClass('no-csstransitions')) {
            var nextWord = takeNext($word);
            switchWord($word, nextWord);
        }
    }

    function hideLetterFast($letter, $word, $bool, $duration, direction) {

        direction = direction || ':last-child';
        $letter.removeClass('in').addClass('out');
        var $nextLetter = $letter.next('i');
        if ($nextLetter.length === 0) {
            $nextLetter = $letter.parent('.nobreak').next('i');
        }

        if ($nextLetter.length === 0) {
            $nextLetter = $letter.next('.nobreak').find('i:eq(0)');
        }

        if ($nextLetter.length > 0) {
            setTimeout(function(){ hideLetterFast($nextLetter, $word, $bool, $duration); }, $duration);
        } else if ($bool) {
            setTimeout(function(){ hideWord(takeNext($word)) }, animationDelay);
        }

        if ($letter.is(direction) && $('html').hasClass('no-csstransitions')) {
            var nextWord = takeNext($word);
            switchWord($word, nextWord);
        }
    }

    function showLetter($letter, $word, $bool, $duration) {
        $letter.addClass('in').removeClass('out');

        if(!$letter.is(':last-child')) {
            setTimeout(function(){ showLetter($letter.next(), $word, $bool, $duration); }, $duration);
        } else {
            if($word.parents('.op-headline').hasClass('type')) { setTimeout(function(){ $word.parents('.op-words-wrapper').addClass('waiting'); }, 200);}
            if(!$bool) { setTimeout(function(){ hideWord($word) }, animationDelay) }
        }
    }

    function showLetterFast($letter, $word, $bool, $duration) {
        var $nextLetter;

        $letter.addClass('in').removeClass('out');

        $nextLetter = $letter.next('i');

        // if ($nextLetter.length === 0) {
        //     $nextLetter = $letter.next('.nobreak').find('i:eq(0)')
        // }

        if ($nextLetter.length === 0) {
            $nextLetter = $letter.parent('.nobreak').next('i');
        }

        if ($nextLetter.length === 0) {
            $nextLetter = $letter.next('.nobreak').find('i:eq(0)');
        }

        if ($nextLetter.length > 0) {
            setTimeout(function () {
                showLetterFast($nextLetter, $word, $bool, $duration);
            }, $duration);
        } else {
            if ($word.parents('.op-headline').hasClass('type_fast')) {
                setTimeout(function () {
                    $word.parents('.op-words-wrapper').addClass('waiting_for_fast');
                }, 200);
            }
            if (!$bool) {
                setTimeout(function () {
                    hideWord($word)
                }, animationDelay);
            }
        }
    }

    function takeNext($word) {
        return (!$word.is(':last-child')) ? $word.next() : $word.parent().children().eq(0);
    }

    function takePrev($word) {
        return (!$word.is(':first-child')) ? $word.prev() : $word.parent().children().last();
    }

    var oldWord1, newWord1;
    window.pauseSwitch = false;
    function switchWord($oldWord, $newWord) {
        if (pauseSwitch === false) {
            $oldWord.removeClass('is-visible').addClass('is-hidden');
            $newWord.removeClass('is-hidden').addClass('is-visible');
        } else {
            oldWord1 = $oldWord;
            newWord1 = $newWord;
        }
    }

    $(window).on('load', function () {
        $(window).trigger('resize');
    });
    $(window).trigger('resize');
});