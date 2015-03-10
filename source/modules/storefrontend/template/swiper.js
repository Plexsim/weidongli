// JavaScript Document
$(document).ready(function(e) {

  function getSlideWidth() {
    w = 0;
    w2 = 0;
    last_w = 0;
    $('.mainlist li').each(function() {
      last_w = parseInt($(this).outerWidth());
      w += last_w;
      });
    w2 = w - last_w;
    w = Math.max(1000, w);
    return w;
  }

  /***不需要自动滚动，去掉即可***/
  time = window.setInterval(function(){
    w = getSlideWidth();
    $('.piclist').css('width', w + 'px');//ul宽度
  },1000);
  /***不需要自动滚动，去掉即可***/
  w = getSlideWidth();
  $('.piclist').css('width', w + 'px');//ul宽度
  $('.og_next').click(function(){
    w = getSlideWidth();
    if(w > 0) {
      ml = parseInt($('.mainlist').css('left'));
      ri = ml + w;
      box_w = parseInt($('.box').css('width'));
      if (ri < box_w) {
        $('.mainlist').css({left: -w + 'px'});
        $('.mainlist').animate({left:'0px'},'slow');
      }
      else if(ml>0-w && ml<=0){
        $('.mainlist').animate({left: ml - 400 + 'px'},'slow');//默认图片滚动
      }else{//交换图片显示时
        $('.mainlist').css({left: '0px'})//默认图片放在显示区域右
      }
    }
  })
  $('.og_prev').click(function(){
      w = getSlideWidth();
      $('.piclist').css('width', w + 'px');//ul宽度
      if (w > 0) {
        ml = parseInt($('.mainlist').css('left'));
        if(ml>0-w && ml<=-400){
          $('.mainlist').animate({left: ml + 400 + 'px'},'slow');
        }else{
          $('.mainlist').animate({left: '0px'},'slow');
        }
      }
  })
});

$(document).ready(function(){
  $('.og_prev,.og_next').hover(function(){
      $(this).fadeTo('fast',1);
    },function(){
      $(this).fadeTo('fast',0.7);
  })

})


