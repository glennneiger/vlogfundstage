$(document).ready(function() {
    $(".showtweets").on('click', function(){
        $("#detailtitle").html($(this).attr('data-title'));
        $("#cvideos").hide();
        $("#tweets").show();
    });

    $(".showvideos").on('click', function(){
        $("#detailtitle").html($(this).attr('data-title'));
        $("#tweets").hide();
        $("#cvideos").show();
    });
});
    
    
        
$(document).on('click', '.youtubes', function(){
    var iframe = document.createElement( "iframe" );
    iframe.setAttribute( "frameborder", "0" );
    iframe.setAttribute( "allowfullscreen", "" );
    iframe.setAttribute( "src", "https://www.youtube.com/embed/"+ this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );
    this.innerHTML = "";
    this.appendChild( iframe );
});

function init() {
    var imgDefer = document.getElementsByTagName('img');
    for (var i = 0; i < imgDefer.length; i++) {
        if (imgDefer[i].getAttribute('data-src')) {
            imgDefer[i].setAttribute('src', imgDefer[i].getAttribute('data-src'));
        }
    }

    $('.details').tooltipster({
        delay: 10,
        contentAsHTML: true,
        maxWidth: 500,
        interactive: true,
        multiple: false,
        //delay: [0, 200],
        trigger: 'custom',
        triggerOpen: {
            mouseenter: true,
            touchstart: true
        },
        triggerClose: {
            click: true,
            mouseleave: true,
            tap: true
        }
    });
}
        
window.onload = init;
var search = false;
var count = 0;
$(document).ready(function () {
    $("#search").autocomplete({
        source: "ajax/autocomplete.php",
        minLength: 2,
        select: function(event, ui) {

            if(search==false){
                $('#youtubers').empty();
                $("#homelink").text("Clear Search");
                search = true;
            }
            $('#youtubers').hide();
            $('#searchloader').show();
            $('#loadmore').hide();
            $(".tag").append(' <a href="#!">'+$("#search").val()+' <span data-channelid="'+ui.item.channelid+'" class="removebtn" style="margin-left:15px;font-size:20px">x</span></a>');
            $.ajax({
               url: "ajax/getdata",
               data : {search: ui.item.channelid ,offset:0, order:'<?php echo $ord; ?>', sort: '<?php echo $sort; ?>'},
               type: "POST",
               success : function(data){
                 if(data){
                    $("#search").val('');
                    $('#searchloader').hide();
                    $('#youtubers').append(data);
                    $('#youtubers').show();
                    count++;
                    init();
                 }else{
                    $('#youtubers').html('No records found');
                 }
               },
            });
        }
    });
});
        
        
$(document).on('click', '.removebtn', function(){
   var channelid = $(this).attr('data-channelid');
   $("#"+channelid).remove();
   $(this).parent().remove();
   count--;
   if(count==0){
       window.location='index';
   }
});

$(document).ready(function () {
    $(".comparetext").autocomplete({
        source: "ajax/autocomplete.php",
        minLength: 2,
        select: function(event, ui) {

        }
    });
});


$(document).on('click','.showinfoimg',function(e){
    e.preventDefault();
    $(this).parent().find('.showinfo').click();
});
        
        
$(document).on('click','.showinfo',function(e){
    e.preventDefault();
    var title = $(this).attr('data-title');
    var channelid = $(this).attr('data-channelid');
    var subs = $("#"+channelid+'-subs').text();
    var img = $("#"+channelid+'-img').attr('src');
    var views = $("#"+channelid+'-views').text();
    var instagram = $(this).attr('data-instagram');
    var twitter = $(this).attr('data-twitter');
    $(".showtweets").attr('data-twitter', channelid);
    $("#ctitle").html(title);
    $("#csubs").html(subs);
    $("#cviews").html(views);
    $("#cimg").attr('src', img);
    $("#videoloader").show();
    $("#cvideos").empty();
    $('#infomodal').modal('show');
    $("#tweets").hide();
    $("#cvideos").show();
    $("#detailtitle").html('Latest Videos');
    if(instagram!=''){
        $(".instagram").attr('href', instagram);
        $(".instagram").show();
    }else{
        $(".instagram").hide();
    }

    if(twitter!=''){
        $("#tweets").empty();
        $("#tweets").html('<a class="twitter-timeline" href="https://twitter.com/'+twitter+'?ref_src=twsrc%5Etfw">Tweets by '+twitter+'</a>');
        $("#tweets").append($("<script />", {
              src: 'https://platform.twitter.com/widgets.js'
        }));
        $('.showtweets').show();
    }else{
        $('.showtweets').hide();
    }

    $.ajax({
       url: "ajax/ajaxgetvideos",
       type: "POST",
       data: {id:channelid},
       success : function(data){
         if(data){
           $("#videoloader").hide();
           $("#cvideos").html(data);
         }else{

         }
       },
    });



});

$(function () {
    var btn = $("#addchannelbtn");
    $('#addchannelform').on('submit', function (e) {
    e.preventDefault();
    $("#msg").html('');
    btn.prop('disabled', true);
    btn.html('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            type: 'post',
            url: 'ajax/ajaxaddchannel.php',
            data: $('#addchannelform').serialize(),
            success: function (data) {
              btn.prop('disabled', false);
              btn.html('Submit');
              $("#msg").html(data);
            }
        });
    });
});
        
var show = false;
$("#showfilters").click(function(e){
      e.preventDefault();
      if(show==false){
          $("#filters").slideDown();
          $(this).html('Hide Filters')
          show = true;
      }else{
          $("#filters").slideUp();
          $(this).html('Show Filters')
          show = false;
      }
});