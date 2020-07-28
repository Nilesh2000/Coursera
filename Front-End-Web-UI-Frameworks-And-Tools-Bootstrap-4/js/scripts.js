$(document).ready(function () {
  $('.loginBtn').click(function () {
    $('#loginModal').modal('show');
  });
  $('.loginClose').click(function () {
    $('#loginModal').modal('hide');
  });
  $('#reserveBtn').click(function () {
    $('#reserveModal').modal('show');
  });
  $('.reserveClose').click(function () {
    $('#reserveModal').modal('hide');
  });

  $('#mycarousel').carousel({ interval: 2000 });

  $('#carouselButton').click(function () {
    if ($('#carouselButton').children('span').hasClass('fa-pause')) {
      $('#mycarousel').carousel('pause');
      $('#carouselButton').children('span').removeClass('fa-pause');
      $('#carouselButton').children('span').addClass('fa-play');
    } else if ($('#carouselButton').children('span').hasClass('fa-play')) {
      $('#mycarousel').carousel('cycle');
      $('#carouselButton').children('span').removeClass('fa-play');
      $('#carouselButton').children('span').addClass('fa-pause');
    }
  });
});
