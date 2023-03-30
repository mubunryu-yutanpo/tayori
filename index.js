$(function(){

  //うんこページのボタンホバーで下の文字表示
  var $jsBtn = $('.js-select-btn');

  $jsBtn.on('mouseover',function(){
    $(this).next('.js-color-title').addClass('on');
    $(this).next('.js-shape-title').addClass('on');
  });
  $jsBtn.on('mouseleave',function(){
    $(this).next('.js-color-title').removeClass('on');
    $(this).next('.js-shape-title').removeClass('on');
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

  //マイページのドロップダウン
  var $jsSlideMenu = $('.js-slide-menu');

  $jsSlideMenu.on('click',function(){
    $(this).next('.js-menu-box').slideToggle('.on');
    $(this).children('.fa-angle-down').slideToggle('.off');
    $(this).children('.fa-angle-up').slideToggle('.off');
  });

});
