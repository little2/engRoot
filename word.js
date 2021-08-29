class wordClass {
    //静态属性
    //count = 2;
    //构造方法
    constructor() {
        this.userId = 0;
        this.threshold = 4; //自動載入的閥值
    }

    //
    // Utility Class
    //
    ajaxPromise(param) {
        param.data['__system_catalog__'] = this.catalog;
        return new Promise((resovle, reject) => {
            $.ajax({
                "type": param.type || "post",
                "async": param.async || true,
                "url": param.url,
                "data": param.data || "",
                "success": res => {
                    resovle(res);
                },
                "error": err => {
                    reject(err);
                }
            })
        })
    }

    playSound(keyword) {

        $.ajax({
            type: 'POST', //GET or POST
            url: "js_vocabulary_play_sound.php", //請求的頁面
            cache: false, //防止抓到快取的回應
            data: { keyword: keyword }, //要傳送到頁面的參數
            success: function (data) {
                //$('#audio_play').playbackRate=1;

                $('#audio_play').attr('src', data['file']);
                // $('#audio_play').play();
            }
        });

    }

    resizeHeight(obj) {
		$(obj).height(10);
		while ($(obj).outerHeight() < $(obj).prop('scrollHeight') + parseFloat($(obj).css("borderTopWidth")) + parseFloat($(obj).css("borderBottomWidth"))) {
            $(obj).height($(obj).height() + 1);           
		};
	}

    resizeTextAreaHeight() {
        let parentThis = this;
        $('textarea').each(function () {
			parentThis.resizeHeight(this);
			$(this).unbind();
			$(this).on('click',function(){parentThis.resizeHeight(this)})			
		})
    }



    //
    // Word Card
    //

    getWordCard() {

        let parentThis = this;
        this.ajaxPromise({
            "url": "getInfo.php",
            "data": { 'act': 'getWordCard' }
        }).then(res => {
            Object.keys(res.wordCard).forEach(function (elem, index) {
                parentThis.buildWordCard({ 'word': elem, 'definition': res.wordCard[elem] });
            });
            $('.fa-volume-up').bind('click', function () { parentThis.playSound($(this).data('word')); });

            $('#zoneWordCard').append('<audio id="audio_play" autoplay="autoplay"></audio>');

            $(".btn").unbind();
            $(".btn").on("click", function () {
                let f = $(this).data('familiarity');
                let w = $(this).data('word');
                if (parseInt(f) < 3) {
                    window.open('https://www.little2.top/english/' + w + '.html', 'englishroot');
                }

                parentThis.setWordFamiliarity({ 'familiarity': f, 'word': w });
            });

            $(".myDefinition-word-text").unbind();
            $(".myDefinition-word-text").on("click", function () {
                // console.log($(this).data('word'));
                let obj = $('#word_' + $(this).data('word') + ' .card .card-body p');
                if(obj.text()=="null")
                {
                    let word = $(this).data('word');
                    new Promise((resovle, reject) => {
                        let resEng = parentThis.getWord(word);                        
                        resovle(resEng);
                    }).then( resEng => {                     
                        obj.text(resEng.definition[0]);
                        obj.removeClass('text-white');
                    })

                    //let res = parentThis.getWord($(this).data('word'));

                    
                }
                else
                {
                    obj.removeClass('text-white');
                }

               
                // console.log('#word_' + $(this).data('word')+'.card.card-body p');
            });



            if (res.user_id != '1') {
                $(".btn").addClass('d-none');
            }





        }).catch(err => {
            console.log("第一个请求失败_301");
        })
    }

    setWordFamiliarity(obj) {
        let parentThis = this;


        this.ajaxPromise({
            "url": "setInfo.php",
            "data": { 'act': 'setWordFamiliarity', 'familiarity': obj.familiarity, 'word': obj.word }
        }).then(res => {
            $('#word_' + obj.word).addClass('d-none');
            parentThis.resetProcessBar();

        }).catch(err => {
            console.log("L63");
        })

    }

    buildWordCard(obj) {

        if ($("#word_" + obj.word).length == 0) {
            //length
            let divWordCard =
                '<div class="div-crad col-sm-6 mt-3" id="word_' + obj.word + '">' +
                '<div class="card">' +
                '<div class="card-body">' +
                '<h5 class="card-title"><span class="myDefinition-word-text" data-word="' + obj.word + '">' + obj.word + '</span><span class="fa fa-volume-up ml-3" data-word="' + obj.word + '"></span></h5>' +
                '<p class="card-text text-white">' + obj.definition + '</p>' +
                '<a data-familiarity="4" data-word="' + obj.word + '" class="btn btn-success mr-2 text-white">熟悉</a>' +
                '<a data-familiarity="3" data-word="' + obj.word + '" class="btn btn-dark mr-2 text-white">错记</a>' +
                '<a data-familiarity="2" data-word="' + obj.word + '" class="btn btn-secondary mr-2 text-white">似曾</a>' +
                '<a data-familiarity="1" data-word="' + obj.word + '" class="btn btn-outline-secondary mr-2 ">不熟</a>' +
                '</div>' +
                '</div>' +
                '</div>'
            $('#zoneWordCard').append(divWordCard);
        }

    }


    ///
    /// Word
    ///
    getWord(word) {
        
        let timestamp = new Date().getTime();
        let thisParent=this;

        return this.ajaxPromise({
            "url": "js_query_dic.php",
            "data": { 'word': word, 'timestamp': timestamp }
        }).then(res => {            
            return res;
        }).catch(err => {
            console.log("第一个请求失败_117");
            console.log(err);
        })
    }

    loadWordField(data) {
        $('#keyword').val(data.vocabulary.word);
        $('#search_result').removeClass('hide');
        //$('#search_more').attr('href','http://tw.dictionary.search.yahoo.com/search?p='+data.vocabulary.word+'&fr2=dict');
        $('#search_more').attr('href', 'https://fanyi.baidu.com/#en/zh/' + data.vocabulary.word);
        $('#search_more_iciba').attr('href', 'http://www.iciba.com/' + data.vocabulary.word);
        $('#search_root_more').attr('href', 'https://www.youdict.com/root/search?wd=' + data.vocabulary.word);
        $('#search_root_more_youdict').attr('href', 'https://www.youdict.com/ciyuan/s/' + data.vocabulary.word);

        $('#definition').val(data.vocabulary.definition);
        $('#synonym').val(data.vocabulary.synonym);
        $('#antonym').val(data.vocabulary.antonym);
        $('#analysis').val(data.vocabulary.analysis);
        $('#symbol').val(data.vocabulary.symbol);

        // search keyword via Internet if no exist
        if (data.vocabulary.definition == "" || data.vocabulary.symbol == "" ||
            data.vocabulary.definition == null || data.vocabulary.symbol == null) {
            var word = $('#keyword').val();
           
            let parentThis = this ; 
            new Promise((resovle, reject) => {
                let resEng = parentThis.getWord(word);  
                console.log(resEng);              
                resovle(resEng);
            }).then(resEng => {
                if ($('#definition').val() == "") {
                    $('#definition').val(resEng.definition);                   
                }
    
                if ($('#symbol').val() == "") {
                    $('#symbol').val(resEng.symbol);
                }               
                            
            })

        }

        $('#memo').val(data.vocabulary.memo);
        $('#root').val(data.vocabulary.root);
        $('#root_zone').empty();
        $('#synonyms_zone').empty();
        $('#antonyms_zone').empty();
        $('#audio_play').attr('src', "");

        //load root
        if (typeof (data.root_dir) != 'undefined') {
            for (var root_key in data.root_dir) {
                $('#root_zone').append('<button type="button" class="btn btn-info" onClick="find_root(\'' + root_key + '\')">' + root_key + '</button> <i>' + data.root_dir[root_key].representative + '</i><p>' + data.root_dir[root_key].explain + '</p>')
            }
        }


        //load synonyms
        if (typeof (data.keyMeaning) != 'undefined') {
            for (var keyMeaning in data.keyMeaning) {
                //$('#synonyms_zone').append('<div class="form-group row"><button type="button" class="btn btn-info btn-lg col-sm-2">' + keyMeaning + '</button><div class="col-sm-6"><input type="text" class="form-control input-lg" id="synonymRow['+keyMeaning+']" name="synonymRow['+keyMeaning+']" placeholder="请输入同义词;分号隔开" autocomplete="off"></input></div></div>');
                $('#synonyms_zone').append('<div style="margin-top:10px;"></div><button type="button" class="btn btn-info btn-lg ">' + keyMeaning + '</button> <input type="text" style="border:0px;width:80%" class="form-control-plaintext" id="synonymRow[' + keyMeaning + ']" name="synonymRow[' + keyMeaning + ']" placeholder="请输入同义词;分号隔开" autocomplete="off" value="xxx"></input><div style="margin-top:5px;"></div>');
                let _tepSynonyString = '';
                for (var word in data.keyMeaning[keyMeaning]) {
                    if (data.vocabulary.word == word) {
                        let inputSynonym = '<input type="text" class="form-control" name="keyMeaning[' + keyMeaning + '][' + word + ']" placeholder="Analysis" autocomplete="off" value="' + data.keyMeaning[keyMeaning][word].analysis + '"></input>';
                        $('#synonyms_zone').append('<p><button type="button" class="btn  btn-primary" onClick="find_word(\'' + word + '\')">' + word + '</button><div style="margin-top:5px;"></div> ' + inputSynonym + '</p>');
                    }
                    else {
                        $('#synonyms_zone').append('<p><button type="button" class="btn  btn-primary" onClick="find_word(\'' + word + '\')">' + word + '</button><br> ' + data.synonym[word].analysis + '</p>');
                    }

                    if (_tepSynonyString != '') _tepSynonyString = _tepSynonyString + ';';
                    _tepSynonyString = _tepSynonyString + word;

                }
                $('input[name="synonymRow[' + keyMeaning + ']"]').val(_tepSynonyString)
                //var values = $("input[name='synonymRow[]']").map(function(){return $(this).val();}).get();
            }
        }

        //load antonym
        if (typeof (data.antonym) != 'undefined') {
            for (var word in data.antonym) {
                $('#antonyms_zone').append('<p><button type="button" class="btn  btn-primary" onClick="find_word(\'' + word + '\')">' + word + '</button> <i>' + data.antonym[word].analysis + '</i></p>')
            }
        }        

    }

    initWordField()
    {
        $('#definition').val("");
        $('#symbol').val("");

        $('#root').val("");
        $('#memo').val("");
        $('#root_zone').html("");
    }


    ///
    /// Process Bar
    ///
    setProcessBar(valueNow, valueMax) {
        let percentage = parseInt(valueNow / valueMax * 100);
        //<div class="progress-bar w-75" style="width: 75%" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
        $('.progress-bar').css({ "width": percentage + "%" })
            .attr('aria-valuenow', valueNow)
            .attr('valuemax', valueMax)
            .text(percentage + "%")
            ;
    }

    resetProcessBar() {
        let parentThis = this;
        let valueNow = 0;
        let valueMax = 0;
        $(".div-crad").each(function () {
            valueMax++;
            if ($(this).hasClass('d-none')) {
                valueNow++;
            }
        });

        if ((valueNow + this.threshold) > valueMax) {
            // console.log('Loading before valueNow:'+valueNow,'valueMax:'+valueMax);

            new Promise((res, rej) => {
                parentThis.getWordCard();
            }).then(res => {
                parentThis.setProcessBar(valueNow, valueMax);
            })
        }
        else {
            // console.log('valueNow:'+valueNow,'valueMax:'+valueMax);
            this.setProcessBar(valueNow, valueMax);
        }

    }

}
