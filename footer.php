
<footer id="footer">
  <p class="copyright">©️Copyright TAYORI All Light Reserved</p>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>

$(function(){

//////////////////////////////////////
//  共通
//////////////////////////////////////

  //フッターの位置調整
  var $ftr = $('#footer');
  if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight){
    $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight() ) + 'px;'});
  }

  //メッセージ表示
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();

  //msgの文字数（最初はゼロ）が入っている場合は
  if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
    //スライドでメッセージを表示
    $jsShowMsg.slideToggle('slow');
    //メッセージを消すタイミング
    setTimeout(function(){
      $jsShowMsg.slideToggle('slow');
    }, 3000);
  }


  //////////////////////////////////////
  //  日記関係
  //////////////////////////////////////

  //ボタンホバーで下の文字表示
  var $jsBtn = $('.js-select-btn');

  $jsBtn.on('mouseover',function(){
    $(this).next('.js-title').addClass('on');
  });
  $jsBtn.on('mouseleave',function(){
    $(this).next('.js-title').removeClass('on');
  });

  //日記のコメントのテキストカウンター
  var $jsTextarea = $('.js-text-area');
  var $jsTextCount = $('.js-text-count');

  $jsTextarea.on('keyup',function(){
    $jsTextCount.html($(this).val().length);

    if($jsTextarea.val().length > 500){
      $jsTextCount.css('color','red');
      $jsTextarea.css('border','1px solid red');
    }else{
      $jsTextCount.css('color','#202025');
      $jsTextarea.css('border','0.5px solid #f5f5f2');
    }
  });

  //食べたもの日記の画像プレビュー
  var $fileLabel = $('.file-label');
  var $inputFile = $('.js-file-input');
  //画像ドラッグオーバー時
  $fileLabel.on('dragover',function(e){
    e.preventDefault();
    e.stopPropagation();
    $(this).css('border','3px dashed #c8c2bc');
  });
  //画像ドラッグリーブ時
  $fileLabel.on('dragleave',function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border','1px solid #c8c2bc');
  });

  //画像を置いたとき
  $inputFile.on('change',function(e){
    $fileLabel.css('border','1px solid #c8c2bc');
    //画像の情報を変数に
    var files = this.files[0]; //files配列にファイルが入ってる
    var $img = $(this).siblings('.js-file-img'); //そのイメージを変数に
    //ファイルリーダーの準備
    var fileReader = new FileReader();

    //ファイルリーダーが読み込まれた時のイベント
    fileReader.onload = function(event){
      //指定のimgタグのsrc属性を書き換えて、表示する
      $img.attr('src',event.target.result).show();
    };

    //画像を読み込む
    fileReader.readAsDataURL(files);

  });


  //=============================
  //チェックボックス選択時のつけ外し
  //=============================
  //おしっこ（色）
  var $jsPeeColor = $('.js-pee-color-check');

  $jsPeeColor.on('click',function(){
    //スタイルチェンジ用にクラスつけ外し
    $('.color-select-btn').removeClass('checked');
    $(this).parent('.color-select-btn').addClass('checked');
    //POST用に属性値を書き換える
    $jsPeeColor.attr('name','pee-color-select');
    $(this).attr('name','pee-color-checked');
  });

  //おしっこ（量）
  var $jsPeeVol = $('.js-pee-vol-check');

  $jsPeeVol.on('click',function(){
    //スタイルチェンジ用にクラスつけ外し
    $('.vol-select-btn').removeClass('checked');
    $(this).parent('.vol-select-btn').addClass('checked');
    //POST用に属性値を書き換える
    $jsPeeVol.attr('name','pee-vol-select');
    $(this).attr('name','pee-vol-checked');
  });

  //おしっこ（回数）
  var $jsPeeTime = $('.js-pee-numtime-check');

  $jsPeeTime.on('click',function(){
    //スタイルチェンジ用にクラスつけ外し
    $('.numtime-select-btn').removeClass('checked');
    $(this).parent('.numtime-select-btn').addClass('checked');
    //POST用に属性値を書き換える
    $jsPeeTime.attr('name','pee-numtime-select');
    $(this).attr('name','pee-numtime-checked');
  });

  //ウンチ（色）
  var $jsPoofColor = $('.js-poof-color-check');

  $jsPoofColor.on('click',function(){
    //スタイルチェンジ用にクラスつけ外し
    $('.color-select-btn').removeClass('checked');
    $(this).parent('.color-select-btn').addClass('checked');
    //POST用に属性値を書き換える
    $jsPoofColor.attr('name','poof-color-select');
    $(this).attr('name','poof-color-checked');
  });

  //ウンチ（形）
  var $jsPoofShape = $('.js-poof-shape-check');

  $jsPoofShape.on('click',function(){
    //スタイルチェンジ用にクラスつけ外し
    $('.shape-select-btn').removeClass('checked');
    $(this).parent('.shape-select-btn').addClass('checked');
    //POST用に属性値を書き換える
    $jsPoofShape.attr('name','poof-shape-select');
    $(this).attr('name','poof-shape-checked');
  });


  //ウンチ（におい）
  var $jsPoofSmell = $('.js-poof-smell-check');

  $jsPoofSmell.on('click',function(){
    //スタイルチェンジ用にクラスつけ外し
    $('.smell-select-btn').removeClass('checked');
    $(this).parent('.smell-select-btn').addClass('checked');
    //POST用に属性値を書き換える
    $jsPoofSmell.attr('name','poof-smell-select');
    $(this).attr('name','poof-smell-checked');
  });


  //////////////////////////////////////
  //  マイページ
  //////////////////////////////////////

  //マイページのドロップダウン
  var $jsSlideMenu = $('.js-slide-menu');

  $jsSlideMenu.on('click',function(){
    $(this).next('.js-menu-box').slideToggle('.on');
    $(this).children('.fa-angle-down').slideToggle('.off');
    $(this).children('.fa-angle-up').slideToggle('.off');
  });


  //////////////////////////////////////
  //  ピン留めアイコンのAjax処理
  //////////////////////////////////////

  //ウンチ
  //ピン留めアイコンのDOMを取ってくる
  var $keepPoof = $('.js-keep-poof') || null;
  var poofId = $keepPoof.data('diaryid') || null;

   //アイコンのデータがある場合
   if(poofId !== undefined && poofId !== null){
     //クリックされたら
     $keepPoof.on('click',function(){
       var $this = $(this);
       //Ajax処理
       $.ajax({
         type:"POST",
         url:"ajaxPoof.php",
         //d_idという名前でdiaryIdがPOSTされる
         data:{d_id : poofId}
         //成功の場合
       }).done(function(data){
         console.log('Ajax成功');
         $this.toggleClass('active');
         //失敗の場合
       }).fail(function(msg){
         console.log('Ajax失敗');
       });
     });
   }

   //オシッコ

   var $keepPee = $('.js-keep-pee') || null;
   var peeId   = $keepPee.data('diaryid') || null;
    //ピン留めアイコンのDOMを取ってくる

    //アイコンのデータがある場合
    if(peeId !== undefined && peeId !== null){
      //ピン留めアイコンがクリックされたら
      $keepPee.on('click',function(){
        var $this = $(this);
        //Ajax処理
        $.ajax({
          type : "POST",
          url  : "ajaxPee.php",
          data : {d_id : peeId}
          //成功した場合
        }).done(function(data){
          //コンソールにログを出す
          console.log('Ajax成功');
          //クラス名：activeをつけ外しする
          $this.toggleClass('active');

          //失敗の場合
        }).fail(function(msg){
          console.log('Ajax処理失敗');
        });
      });
    }


  //食べ物

  var $keepFood = $('.js-keep-food') || null;
  var foodId   = $keepFood.data('diaryid') || null;
   //ピン留めアイコンのDOMを取ってくる

   //アイコンのデータがある場合
   if(foodId !== undefined && foodId !== null){
     //ピン留めアイコンがクリックされたら
     $keepFood.on('click',function(){
       var $this = $(this);
       //Ajax処理
       $.ajax({
         type : "POST",
         url  : "ajaxFood.php",
         data : {d_id : foodId}
         //成功した場合
       }).done(function(data){
         //コンソールにログを出す
         console.log('Ajax成功');
         //クラス名：activeをつけ外しする
         $this.toggleClass('active');

         //失敗の場合
       }).fail(function(msg){
         console.log('Ajax処理失敗');
       });
     });
   }

   //////////////////////////////////////
   //  食べたもの日記、画像削除のAjax処理
   //////////////////////////////////////

   //pic1
   //DOMを取って変数に
   var $imgDelBtn1 = $('.js-del-btn1');
   //画像の入っている日記IDを取得し変数に
   var pic1DelId = $imgDelBtn1.data('delid') || null;

   //ボタンが押されたら
   $imgDelBtn1.on('click',function(){
     $.ajax({
       type:"POST",
       url: "ajaxDelPic1.php",
       data:{d_id :pic1DelId}

     }).done(function(data){
       console.log('pic1削除のajax成功');

     }).fail(function(msg){
       console.log('ajax失敗');
       console.log(pic1DelId);
     });

     $('.js-del-img1').css('display','none');
   });

   //pic2
   //DOMを取って変数に
   var $imgDelBtn2 = $('.js-del-btn2');
   //画像の入っている日記IDを取得し変数に
   var pic2DelId = $imgDelBtn2.data('delid') || null;

   //ボタンが押されたら
   $imgDelBtn2.on('click',function(){
     $.ajax({
       type:"POST",
       url: "ajaxDelPic2.php",
       data:{d_id :pic2DelId}

     }).done(function(data){
       console.log('pic2削除のajax成功');

     }).fail(function(msg){
       console.log('ajax失敗');
       console.log(pic2DelId);
     });

     $('.js-del-img2').css('display','none');

   });

   //pic3
   //DOMを取って変数に
   var $imgDelBtn3 = $('.js-del-btn3');
   //画像の入っている日記IDを取得し変数に
   var pic3DelId = $imgDelBtn3.data('delid') || null;

   //ボタンが押されたら
   $imgDelBtn3.on('click',function(){
     $.ajax({
       type:"POST",
       url: "ajaxDelPic3.php",
       data:{d_id :pic3DelId}

     }).done(function(data){
       console.log('pic3削除のajax成功');

     }).fail(function(msg){
       console.log('ajax失敗');
       console.log(pic3DelId);
     });

     $('.js-del-img3').css('display','none');

   });



});

</script>
</body>
</html>
