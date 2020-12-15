    <footer id="footer">
      <p>&copy; Share Book .Allrights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script>
      $(function(){
        
        //フッター固定
        let $ftr = $('#footer');
        if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
          $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;'});
        }

        //メッセージ表示
        let $jsShowMsg = $('#js-show-msg');
        let msg = $jsShowMsg.text();
        if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
          $jsShowMsg.slideToggle('slow');
          setTimeout(function(){$jsShowMsg.slideToggle('slow');},5000);
        }

        //テキストカウント
        let $countUp = $('#js-count');
        let $countView = $('#js-count-view');
        $countUp.on('keyup', function(e){
          $countView.html($(this).val().length);
        });

        //画像ライブプレビュー
        let $dropArea = $('.imgDrop__area');
        let $fileInput = $('.imgDrop__input--file');
        $dropArea.on('dragover', function(e){
          e.stopPropagation();
          e.preventDefault();
          $(this).css('border', '3px #fff dashed');
        });
        $dropArea.on('dragleave', function(e){
          e.stopPropagation();
          e.preventDefault();
          $(this).css('border', 'none');
        });
        $fileInput.on('change', function(e){
          $dropArea.css('border', 'none');
          let file = this.files[0];
          let $img = $(this).siblings('.prev-img');
          let fileReader = new FileReader();

          fileReader.onload = function(event){
            $img.attr('src', event.target.result).show();
          };
          fileReader.readAsDataURL(file);
        });
        
      });
    </script>
  </body>
</html>